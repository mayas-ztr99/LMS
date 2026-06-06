<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseMedia\StoreCourseMediaRequest;
use App\Models\Course;
use App\Services\Admin\CourseMediaService;
use App\Traits\ApiResponseTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CourseMediaController extends Controller
{
    use ApiResponseTrait;
    protected $courseMediaService;

    public function __construct(CourseMediaService $courseMediaService)
    {
        $this->courseMediaService = $courseMediaService;
    }

    public function uploadImages(StoreCourseMediaRequest $request, Course $course)
    {
        try {
            if (!$request->hasFile('images')) {
                return $this->errorResponse('No images uploaded', 422);
            }
            $images = $this->courseMediaService
                ->uploadImages($request, $course);
            return $this->successResponse(
                $images,
                'Images uploaded successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to upload images',
                500,
                $e->getMessage()
            );
        }
    }

    public function updateImages(StoreCourseMediaRequest $request, Course $course)
    {
        try {
            if (!$request->hasFile('images')) {
                return $this->errorResponse('No images uploaded', 422);
            }
            $images = $this->courseMediaService
                ->updateImages($request, $course);
            return $this->successResponse(
                $images,
                'Images updated successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to update images',
                500,
                $e->getMessage()
            );
        }
    }

    public function deleteImage(Course $course, Media $media)
    {
        try {
            $deleted = $this->courseMediaService
                ->deleteImage($course, $media);
            if (!$deleted) {
                return $this->errorResponse(
                    'Image not found',
                    404
                );
            }
            return $this->successResponse(
                [],
                'Image deleted successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to delete image',
                500,
                $e->getMessage()
            );
        }
    }

    public function uploadPdf(StoreCourseMediaRequest $request, Course $course)
    {
        try {
            if (!$request->hasFile('pdf_file')) {
                return $this->errorResponse('No PDF uploaded', 422);
            }
            $pdf = $this->courseMediaService
                ->uploadPdf($request, $course);
            return $this->successResponse(
                $pdf,
                'PDF uploaded successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to upload PDF',
                500,
                $e->getMessage()
            );
        }
    }

    public function updatePdf(StoreCourseMediaRequest $request, Course $course)
    {
        try {
            if (!$request->hasFile('pdf_file')) {
                return $this->errorResponse('No PDF uploaded', 422);
            }
            $pdf = $this->courseMediaService
                ->updatePdf($request, $course);
            return $this->successResponse(
                $pdf,
                'PDF updated successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to update PDF',
                500,
                $e->getMessage()
            );
        }
    }

    public function deletePdf(Course $course)
    {
        try {
            $deleted = $this->courseMediaService
                ->deletePdf($course);
                if (!$deleted) {
                return $this->errorResponse(
                    'No PDF found',
                    404
                );
            }
            return $this->successResponse(
                [],
                'PDF deleted successfully',
                200
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Failed to delete PDF',
                500,
                $e->getMessage()
            );
        }
    }
}
