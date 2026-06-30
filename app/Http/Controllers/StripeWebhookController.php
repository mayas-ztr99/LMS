<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Throwable;

class StripeWebhookController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function handle(Request $request)
    {
        try {
            $this->paymentService->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );

            return response()->json(['received' => true], 200);
        } catch (SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Webhook error'], 500);
        }
    }
}
