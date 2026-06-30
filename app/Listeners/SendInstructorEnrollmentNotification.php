<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class SendInstructorEnrollmentNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function __construct(
        protected FirebaseNotificationService $firebaseNotificationService
    ) {}

    public function handle(StudentEnrolled $event): void
    {
        $enrollment = $event->enrollment->loadMissing(['student', 'course.instructors']);

        if (! $enrollment->student || ! $enrollment->course) {
            return;
        }

        foreach ($enrollment->course->instructors as $instructor) {
            $this->firebaseNotificationService->sendToUser(
                $instructor,
                'New Student Enrollment',
                $enrollment->student->name.' enrolled in your course '.$enrollment->course->title,
                [
                    'type' => 'instructor_enrollment',
                    'course_id' => $enrollment->course_id,
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                ]
            );
        }
    }

    public function failed(StudentEnrolled $event, Throwable $e): void
    {
        logger()->error('Instructor enrollment listener failed', [
            'enrollment_id' => $event->enrollment->id ?? null,
            'message' => $e->getMessage(),
        ]);
    }
}
