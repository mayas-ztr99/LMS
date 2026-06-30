<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Throwable;

class FirebaseNotificationService
{
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        try {
            $tokens = $user->deviceTokens()
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            logger()->info('Firebase sendToUser started', [
                'user_id' => $user->id,
                'tokens_count' => count($tokens),
            ]);

            if (empty($tokens)) {
                logger()->warning('No device tokens found', [
                    'user_id' => $user->id,
                ]);
                return;
            }

            $messaging = app('firebase.messaging');

            $message = CloudMessage::new()
                ->withNotification(FcmNotification::create($title, $body))
                ->withData($this->normalizeData($data));

            $report = $messaging->sendMulticast($message, $tokens);

            logger()->info('Firebase sendMulticast finished', [
                'user_id' => $user->id,
                'successes' => $report->successes()->count(),
                'failures' => $report->failures()->count(),
            ]);

        } catch (Throwable $e) {
            logger()->error('Firebase sendToUser exception', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function normalizeData(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[(string) $key] = is_bool($value)
                ? ($value ? 'true' : 'false')
                : (string) $value;
        }

        return $normalized;
    }
}
