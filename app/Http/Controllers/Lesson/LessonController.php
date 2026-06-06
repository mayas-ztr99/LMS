<?php

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Models\Lesson;
use App\Services\Lessons\LessonService;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Throwable;

class LessonController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;
    public function __construct(private LessonService $lessonService)
    {
    }

    public function index()
    {
        try {
            $lessons = Lesson::query()
                ->with('course')
                ->orderBy('course_id')
                ->orderBy('sort_order')
                ->get();
            return $this->successResponse($lessons,'Lessons retrieved successfully.');
        } catch (Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to fetch lessons.');
        }
    }

    public function store(StoreLessonRequest $request)
    {
        try {
            $lesson = $this->lessonService->create($request->validated());
            return $this->successResponse($lesson, 'Lesson created successfully.');
        } catch (Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to create lesson.');
        }
    }

    public function show(Lesson $lesson)
    {
        try {
            $this->authorize('view', $lesson);
            return $this->successResponse(
                $lesson->load(['course', 'media']),
                'Lesson retrieved successfully.'
            );
        } catch (Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to retrieve lesson.');
        }
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        try {
            $lesson = $this->lessonService->update($lesson, $request->validated());
            return $this->successResponse($lesson, 'Lesson updated successfully.');
        } catch (Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to update lesson.');
        }
    }

    public function destroy(Lesson $lesson)
    {
        try {
            $this->authorize('delete', $lesson);
            $this->lessonService->delete($lesson);
            return $this->successResponse(null, 'Lesson deleted successfully.');
        } catch (Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to delete lesson.');
        }
    }

    // public function reorder(Request $request)
    // {
    //     try {
    //         $this->lessonService->reorder(
    //             $request->integer('course_id'),
    //             $request->input('lesson_ids')
    //         );

    //         return $this->successResponse(null, 'Lessons reordered successfully.');
    //     } catch (Throwable $e) {
    //         report($e);

    //         return $this->errorResponse('Failed to reorder lessons.');
    //     }
    // }
}
