<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StoreCourseCheckoutRequest;
use App\Models\Course;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    public function __construct(private PaymentService $paymentService)
    {
    }

    public function store(StoreCourseCheckoutRequest $request, Course $course)
    {
        try {
            $data = $this->paymentService->createCheckoutSession(
                $course,
                $request->user(),
                $request->validated()['coupon_code'] ?? null
            );

            return $this->successResponse('Checkout session created successfully', $data, 201);
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Request $request, Payment $payment)
    {
        try {
            if ($payment->user_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $payment->load(['student', 'course', 'coupon', 'enrollment']);

            return $this->successResponse('Payment retrieved successfully', $payment);
        } catch (Throwable $e) {
            return $this->errorResponse('Unable to retrieve payment', 500);
        }
    }
}
