<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function getAllCoupons()
    {
        return Coupon::latest()->paginate(10);
    }

    public function createCoupon(array $data): Coupon
    {
        $data['code'] = Str::upper(trim($data['code']));
        $data['used_count'] = $data['used_count'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return Coupon::create($data);
    }

    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        if (isset($data['code'])) {
            $data['code'] = Str::upper(trim($data['code']));
        }

        $coupon->update($data);

        return $coupon->refresh();
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function previewFinalPrice(int $courseId, ?string $couponCode = null): array
    {
        $course = Course::findOrFail($courseId);
        $basePrice = (float) $course->price;

        if ($basePrice <= 0) {
            return [
                'course_id' => $course->id,
                'base_price' => 0,
                'discount' => 0,
                'final_price' => 0,
                'coupon' => null,
            ];
        }

        if (!$couponCode) {
            return [
                'course_id' => $course->id,
                'base_price' => $basePrice,
                'discount' => 0,
                'final_price' => $basePrice,
                'coupon' => null,
            ];
        }

        $coupon = Coupon::query()
            ->where('code', strtoupper(trim($couponCode)))
            ->first();

        $this->validateCouponForPreview($coupon);

        $discount = $this->calculateDiscount($basePrice, $coupon);
        $finalPrice = max(0, $basePrice - $discount);

        return [
            'course_id' => $course->id,
            'base_price' => $basePrice,
            'discount' => $discount,
            'final_price' => $finalPrice,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
        ];
    }

    public function consumeCoupon(Coupon $coupon): Coupon
    {
        return DB::transaction(function () use ($coupon) {
            $coupon = Coupon::whereKey($coupon->id)->lockForUpdate()->firstOrFail();

            if (! $coupon->is_active) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['This coupon is not active.'],
                ]);
            }

            if ($coupon->expires_at && now()->greaterThan($coupon->expires_at)) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['This coupon has expired.'],
                ]);
            }

            if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
                throw ValidationException::withMessages([
                    'coupon_code' => ['This coupon has reached its maximum number of uses.'],
                ]);
            }

            $coupon->increment('used_count');

            if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
                $coupon->update(['is_active' => false]);
            }
            return $coupon->refresh();
        });
    }

    private function calculateDiscount(float $basePrice, Coupon $coupon): float
    {
        $discount = 0;

        if ($coupon->type === 'percent') {
            $discount = ($basePrice * (float) $coupon->value) / 100;
        } elseif ($coupon->type === 'fixed') {
            $discount = (float) $coupon->value;
        }

        return min($discount, $basePrice);
    }
    private function validateCouponForPreview(?Coupon $coupon): void
    {
        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => ['Coupon code is invalid.'],
            ]);
        }

        if (! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon is not active.'],
            ]);
        }

        if ($coupon->expires_at && now()->greaterThan($coupon->expires_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon has expired.'],
            ]);
        }

        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            throw ValidationException::withMessages([
                'coupon_code' => ['This coupon has reached its maximum number of uses.'],
            ]);
        }
    }
}
