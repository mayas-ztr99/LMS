<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Models\Review;
use App\Services\ReviewService;
use Throwable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use \App\Traits\ApiResponseTrait,AuthorizesRequests;
    public function __construct(private ReviewService $reviewService)
    {
    }

    public function index()
    {
        try {
            $this->authorize('viewAny', Review::class);

            $reviews = $this->reviewService->index();

            return $this->successResponse($reviews, 'Reviews retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StoreReviewRequest $request)
    {
        try {
            $this->authorize('create', Review::class);

            $review = $this->reviewService->store($request->validated(), Auth::id());

            return $this->successResponse($review, 'Review created successfully', 201);
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Review $review)
    {
        try {
            $this->authorize('view', $review);

            return $this->successResponse($review, 'Review retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        try {
            $this->authorize('update', $review);

            $updatedReview = $this->reviewService->update($review, $request->validated());

            return $this->successResponse($updatedReview, 'Review updated successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Review $review)
    {
        try {
            $this->authorize('delete', $review);

            $this->reviewService->destroy($review);

            return $this->successResponse(null, 'Review deleted successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
