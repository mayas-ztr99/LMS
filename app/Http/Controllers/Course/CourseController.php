<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\IndexCourseRequest;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\Course\CourseService;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests, ApiResponseTrait;


    public function __construct(private readonly CourseService $courseService) {}

    public function index(IndexCourseRequest $request)
    {
        try {
            $this->authorize('viewAny', Course::class);
            $courses = $this->courseService->paginate($request->validated());

            return $this->successResponse(
                CourseResource::collection($courses),
                'Courses retrieved successfully'
            );
        } catch (Throwable $e) {

            return $this->errorResponse(
                'Something went wrong',
                $e->getMessage(),
                500
            );
        }
    }
    public function store(StoreCourseRequest $request)
    {
        try {
            $course = $this->courseService->create(
                $request->validated(),
                $request->user()
            );
            return $this->successResponse(
                new CourseResource(
                    $course->load(['instructors', 'category'])
                ),
                'Course created successfully',
                201
            );
        } catch (Throwable $e) {
            // dd($e->getMessage(),$e->getFile(),$e->getLine());
            report($e);
            return $this->errorResponse(
                'Something went wrong while creating the course',
                500
            );
        }
    }

    public function show(Course $course)
    {
        try {
            $this->authorize('view', $course);

            return $this->successResponse(
                new CourseResource($course->load(['instructors', 'category'])),
                'Course retrieved successfully'
            );
        } catch (Throwable $e) {
            //dd($e->getMessage());
            return $this->errorResponse(
                'Something went wrong',
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {

            $updated = $this->courseService->update(
                $course,
                $request->validated()
            );

            return $this->successResponse(
                new CourseResource($updated),
                'Course updated successfully'
            );
        } catch (Throwable $e) {

            report($e);

            return $this->errorResponse(
                'Something went wrong while updating the course',
                500
            );
        }
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        try {

            $this->courseService->destroy($course);

            return $this->successResponse(
                null,
                'Course deleted successfully'
            );
        } catch (Throwable $e) {

            report($e);

            return $this->errorResponse(
                'Something went wrong while deleting the course',
                500
            );
        }
    }
}
