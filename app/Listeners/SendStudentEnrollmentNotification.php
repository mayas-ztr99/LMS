<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class SendStudentEnrollmentNotification implements ShouldQueue
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

        $this->firebaseNotificationService->sendToUser(
            $enrollment->student,
            'Enrollment Successful',
            'You have successfully enrolled in '.$enrollment->course->title,
            [
                'type' => 'student_enrollment',
                'course_id' => $enrollment->course_id,
                'enrollment_id' => $enrollment->id,
            ]
        );
    }

    public function failed(StudentEnrolled $event, Throwable $e): void
    {
        logger()->error('Student enrollment listener failed', [
            'enrollment_id' => $event->enrollment->id ?? null,
            'message' => $e->getMessage(),
        ]);
    }
}
