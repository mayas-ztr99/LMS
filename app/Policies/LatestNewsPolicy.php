<?php

namespace App\Policies;

use App\Models\LatestNews;
use App\Models\User;

class LatestNewsPolicy
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
        return true;
    }

    public function view(?User $user=null, ?LatestNews $latestNews=null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, LatestNews $latestNews): bool
    {
        return false;
    }

    public function delete(User $user, LatestNews $latestNews): bool
    {
        return false;
    }
}
