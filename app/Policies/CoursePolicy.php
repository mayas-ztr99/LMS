<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{

    public function __construct()
    {
        //
    }
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Instructor','Student']);
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['Admin', 'Instructor','Student']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('Admin') ;
    }
}
