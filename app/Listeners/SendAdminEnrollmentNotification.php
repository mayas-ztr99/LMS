<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class SendAdminEnrollmentNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function __construct(
        protected FirebaseNotificationService $firebaseNotificationService
    ) {}

    public function handle(StudentEnrolled $event): void
    {
        $enrollment = $event->enrollment->loadMissing(['student', 'course']);

        if (! $enrollment->student || ! $enrollment->course) {
            return;
        }

        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $this->firebaseNotificationService->sendToUser(
                $admin,
                'New Enrollment',
                $enrollment->student->name . ' enrolled in ' . $enrollment->course->title,
                [
                    'type' => 'admin_enrollment',
                    'course_id' => $enrollment->course_id,
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                ]
            );
        }
    }

    public function failed(StudentEnrolled $event, Throwable $e): void
    {
        logger()->error('Admin enrollment listener failed', [
            'enrollment_id' => $event->enrollment->id ?? null,
            'message' => $e->getMessage(),
        ]);
    }
}
