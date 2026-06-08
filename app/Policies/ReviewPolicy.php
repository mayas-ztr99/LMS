<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Instructor', 'Student']);
    }

    public function view(User $user, Review $review): bool
    {
        return $user->hasAnyRole(['Instructor', 'Student']) || $review->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Instructor', 'Student']);
    }

    public function update(User $user, Review $review): bool
    {
        return $user->hasRole('Instructor') || $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->hasRole('Instructor') || $review->user_id === $user->id;
    }

    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }
}
