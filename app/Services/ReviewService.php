<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function index()
    {
        return Review::with(['user', 'course'])->latest()->get();
    }

    public function store(array $data, int $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            return Review::create([
                'course_id' => $data['course_id'],
                'user_id'   => $userId,
                'rating'    => $data['rating'],
                'comment'   => $data['comment'] ?? null,
            ]);
        });
    }

    public function update(Review $review, array $data)
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update([
                'course_id' => $data['course_id'] ?? $review->course_id,
                'rating'    => $data['rating'] ?? $review->rating,
                'comment'   => array_key_exists('comment', $data)
                    ? $data['comment']
                    : $review->comment,
            ]);

            return $review->fresh(['user', 'course']);
        });
    }

    public function destroy(Review $review)
    {
        return DB::transaction(function () use ($review) {
            return $review->delete();
        });
    }
}
