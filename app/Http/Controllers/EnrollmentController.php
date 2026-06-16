<?php

namespace App\Http\Controllers;

use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class EnrollmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private EnrollmentService $enrollmentService)
    {
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->enrollmentService->index();

            return $this->successResponse($data, 'Enrollments retrieved successfully.');
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to retrieve enrollments.', 500, $e->getMessage());
        }
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        try {
            $enrollment = $this->enrollmentService->store($request->validated());

            return $this->successResponse(
                $enrollment->load(['student', 'course', 'coupon']),
                'Student enrolled successfully.',
                201
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                'Validation failed.',
                422,
                $e->errors()
            );
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse(
                'Something went wrong while enrolling the student.',
                500,
                $e->getMessage()
            );
        }
    }

    public function show(Enrollment $enrollment): JsonResponse
    {
        try {
            $data = $this->enrollmentService->show($enrollment);

            return $this->successResponse($data, 'Enrollment retrieved successfully.');
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to retrieve enrollment.', 500, $e->getMessage());
        }
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): JsonResponse
    {
        try {
            $data = $this->enrollmentService->update($enrollment, $request->validated());

            return $this->successResponse($data, 'Enrollment updated successfully.');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                'Validation failed.',
                422,
                $e->errors()
            );
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to update enrollment.', 500, $e->getMessage());
        }
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        try {
            $this->enrollmentService->destroy($enrollment);

            return $this->successResponse(null, 'Enrollment deleted successfully.');
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to delete enrollment.', 500, $e->getMessage());
        }
    }

    public function studentCourses(User $student): JsonResponse
    {
        try {
            $courses = $this->enrollmentService->studentCourses($student);

            return $this->successResponse($courses, 'Student courses retrieved successfully.');
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to retrieve student courses.', 500, $e->getMessage());
        }
    }

    public function courseStudents(Course $course): JsonResponse
    {
        try {
            $students = $this->enrollmentService->courseStudents($course);

            return $this->successResponse($students, 'Course students retrieved successfully.');
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse('Failed to retrieve course students.', 500, $e->getMessage());
        }
    }
}
