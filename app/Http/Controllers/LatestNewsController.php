<?php

namespace App\Http\Controllers;

use App\Http\Requests\LatestNews\StoreLatestNewsRequest;
use App\Http\Requests\LatestNews\UpdateLatestNewsRequest;
use App\Models\LatestNews;
use App\Services\LatestNewsService;
use Throwable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class LatestNewsController extends Controller
{
    use \App\Traits\ApiResponseTrait,AuthorizesRequests;

    public function __construct(private LatestNewsService $latestNewsService)
    {
    }

    public function index()
    {
        try {
            $news = $this->latestNewsService->index();

            return $this->successResponse($news, 'Latest news retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(LatestNews $latestNews)
    {
        try {
            $news= $this->latestNewsService->show($latestNews);
            return $this->successResponse($news, 'Latest news retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StoreLatestNewsRequest $request)
    {
        try {
            $this->authorize('create', LatestNews::class);

            $news = $this->latestNewsService->store($request->validated());

            return $this->successResponse($news, 'News created successfully', 201);
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function update(UpdateLatestNewsRequest $request, LatestNews $latestNews)
    {
        try {
            $this->authorize('update', $latestNews);
            $news = $this->latestNewsService->update($latestNews, $request->validated());
            return $this->successResponse($news, 'News updated successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(LatestNews $latestNews)
    {
        try {
            $this->authorize('delete', $latestNews);

            $this->latestNewsService->destroy($latestNews);

            return $this->successResponse(null, 'News deleted successfully');
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
