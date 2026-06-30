<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFcmTokenRequest;
use App\Models\DeviceToken;
use App\Traits\ApiResponseTrait;
use Throwable;

class FcmTokenController extends Controller
{
    use ApiResponseTrait;

    public function store(StoreFcmTokenRequest $request)
    {
        try {
            $user = $request->user();

            DeviceToken::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'token'   => $request->validated()['fcm_token'],
                ],
                [
                    'device_type' => $request->device_type ?? null,
                ]
            );

            return $this->successResponse(
                [
                    'tokens' => $user->deviceTokens()->pluck('token')
                ],
                'FCM token saved successfully.'
            );

        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse(
                'Failed to save FCM token.',
                500,
                $e->getMessage()
            );
        }
    }
}
