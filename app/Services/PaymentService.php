<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    public function __construct(
        private CouponService $couponService,
        private EnrollmentService $enrollmentService,
    ) {
    }

    public function createCheckoutSession(Course $course, User $user, ?string $couponCode = null): array
    {
        if ($course->students()->where('users.id', $user->id)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'course' => ['You are already enrolled in this course.'],
            ]);
        }

        $pricing = $this->couponService->previewFinalPrice($course->id, $couponCode);

        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'coupon_id' => $pricing['coupon']['id'] ?? null,
            'amount' => $pricing['base_price'],
            'discount_amount' => $pricing['discount'],
            'final_amount' => $pricing['final_price'],
            'currency' => 'usd',
            'status' => 'pending',
        ]);

        if ((float) $pricing['final_price'] <= 0) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $enrollment = $this->enrollmentService->createFromPaidPayment($payment);

            $payment->update([
                'enrollment_id' => $enrollment->id,
            ]);

            if ($payment->coupon_id) {
                $coupon = Coupon::find($payment->coupon_id);
                if ($coupon) {
                    $this->couponService->consumeCoupon($coupon);
                }
            }

            return [
                'payment_id' => $payment->id,
                'checkout_url' => null,
                'payment_required' => false,
                'base_price' => $pricing['base_price'],
                'discount' => $pricing['discount'],
                'final_price' => $pricing['final_price'],
                'currency' => 'usd',
                'status' => $payment->status,
                'message' => 'No payment required. Enrollment completed successfully.',
            ];
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $frontendUrl = rtrim(config('services.frontend_url', config('app.url')), '/');

        try {
            $session = StripeCheckoutSession::create([
                'mode' => 'payment',
                'success_url' => $frontendUrl . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $frontendUrl . '/payment/cancel',
                'customer_email' => $user->email,
                'line_items' => [
                    [
                        'quantity' => 1,
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => (int) round($pricing['final_price'] * 100),
                            'product_data' => [
                                'name' => $course->title,
                            ],
                        ],
                    ],
                ],
                'metadata' => [
                    'payment_id' => (string) $payment->id,
                    'course_id' => (string) $course->id,
                    'user_id' => (string) $user->id,
                    'coupon_code' => $couponCode ?? '',
                ],
            ]);

            $payment->update([
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
            ]);return [
                'payment_id' => $payment->id,
                'checkout_url' => $session->url,
                'payment_required' => true,
                'base_price' => $pricing['base_price'],
                'discount' => $pricing['discount'],
                'final_price' => $pricing['final_price'],
                'currency' => 'usd',
                'status' => $payment->status,
            ];
        } catch (ApiErrorException $e) {
            $payment->update(['status' => 'failed']);

            throw $e;
        }
    }

    public function handleWebhook(string $payload, ?string $signature): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $event = Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object),
            default => null,
        };
    }

    private function handleCheckoutSessionCompleted(object $session): void
    {
        DB::transaction(function () use ($session) {
            $payment = Payment::where('stripe_session_id', $session->id)->lockForUpdate()->first();

            if (! $payment || $payment->status === 'paid') {
                return;
            }

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_intent_id' => $session->payment_intent ?? $payment->stripe_payment_intent_id,
            ]);

            $enrollment = $this->enrollmentService->createFromPaidPayment($payment);

            $payment->update([
                'enrollment_id' => $enrollment->id,
            ]);

            if ($payment->coupon_id) {
                $coupon = Coupon::find($payment->coupon_id);
                if ($coupon) {
                    $this->couponService->consumeCoupon($coupon);
                }
            }
        });
    }

    private function handlePaymentIntentSucceeded(object $intent): void
    {
        DB::transaction(function () use ($intent) {
            $payment = Payment::where('stripe_payment_intent_id', $intent->id)->lockForUpdate()->first();

            if (! $payment || $payment->status === 'paid') {
                return;
            }

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $enrollment = $this->enrollmentService->createFromPaidPayment($payment);

            $payment->update([
                'enrollment_id' => $enrollment->id,
            ]);

            if ($payment->coupon_id) {
                $coupon = Coupon::find($payment->coupon_id);
                if ($coupon) {
                    $this->couponService->consumeCoupon($coupon);
                }
            }
        });
    }

    private function handlePaymentIntentFailed(object $intent): void
    {
        Payment::where('stripe_payment_intent_id', $intent->id)->update([
            'status' => 'failed',
        ]);
    }
}
