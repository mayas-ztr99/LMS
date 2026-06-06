<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function __construct()
    {
        //
    }
    // Admin bypass.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('Admin') ? true : null;
    }

    //View lessons list.
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Instructor']);
    }

    //Create lesson.
    public function create(User $user, Course $course): bool
    {
        return $user->hasRole('Instructor') && $course->hasInstructor($user->id);
    }

    // View single lesson.
    public function view(User $user, Lesson $lesson): bool
    {
        // Admin handled by before()

        // Instructor can view only lessons of his courses
        if ($user->hasRole('Instructor')) {
            return $lesson->course->hasInstructor($user->id);
        }

        // Student must be enrolled
        if ($user->hasRole('Student')) {
            return Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $lesson->course_id)
                ->exists();
        }
        return false;
    }

    //Update lesson.
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('Instructor') && $lesson->course->hasInstructor($user->id);
    }
    //Delete lesson.
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('Instructor') && $lesson->course->hasInstructor($user->id);
    }

    //Upload/Delete media files.
    public function manageMedia(User $user, Lesson $lesson): bool
    {
        return  $user->hasRole('Instructor') && $lesson->course->hasInstructor($user->id);
    }
}
