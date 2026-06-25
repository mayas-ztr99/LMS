<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function before(User $user,string $ability)
    {
        if ($user->HasRole('admin')) {
            return true;
        }
        return false;
    }
    public function viewAny(User $user): bool
    {
        return false;
    }
    public function view(User $user,Coupon $coupon): bool
    {
        return false;
    }
    public function create(User $user): bool
    {
        return false;
    }
    public function update(User $user,Coupon $coupon): bool
    {
        return false;
    }
    public function delete(User $user,Coupon $coupon): bool
    {
        return false;
    }
}
