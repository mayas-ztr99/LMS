<?php

namespace App\Services\Admin;

use App\Http\Requests\CourseMedia\StoreCourseMediaRequest;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CourseMediaService
{
    // Upload Images
    public function uploadImages(StoreCourseMediaRequest $request, Course $course)
    {
        foreach ($request->file('images') as $image) {
            $course
                ->addMedia($image)
                ->toMediaCollection('images', 'public');
        }
        ///
        return $course->getMedia('images');
    }
    //Update Images

    public function updateImages(StoreCourseMediaRequest $request, Course $course)
    {
        //Delete old images
        $course->clearMediaCollection('images');
        //Upload new images
        foreach ($request->file('images') as $image) {
            $course
                ->addMedia($image)
                ->toMediaCollection('images', 'public');
        }
        return $course->getMedia('images');
    }
    //Delete Image
    public function deleteImage(Course $course, Media $media)
    {
        if (
            $media->model_type !== Course::class ||
            $media->model_id !== $course->id
        ) {
            return false;
        }
        $media->delete();
        return true;
    }
    // Upload PDF
    public function uploadPdf(StoreCourseMediaRequest $request, Course $course)
    {
        $pdf = $request->file('pdf_file');

        $path = $pdf->store(
            "courses/{$course->id}/pdfs",
            'public'
        );

        $course->update([
            'pdf_path' => $path,
            'pdf_original_name' =>$pdf->getClientOriginalName(),
            'pdf_size' =>$pdf->getSize(),
        ]);

        return [
            'pdf_path' => $path,
            'pdf_original_name' =>$pdf->getClientOriginalName(),
            'pdf_size' =>$pdf->getSize(),
        ];
    }
    //Update PDF
    public function updatePdf(StoreCourseMediaRequest $request, Course $course)
    {
        //Delete old PDF
        if ($course->pdf_path) {
            Storage::disk('public')
                ->delete($course->pdf_path);
        }

        //Upload new PDF
        $pdf = $request->file('pdf_file');

        $path = $pdf->store(
            "courses/{$course->id}/pdfs",
            'public'
        );

        $course->update([
            'pdf_path' => $path,
            'pdf_original_name' => $pdf->getClientOriginalName(),
            'pdf_size' => $pdf->getSize(),
        ]);
        return [
            'pdf_path' => $path,
            'pdf_original_name' =>$pdf->getClientOriginalName(),
            'pdf_size' =>
            $pdf->getSize(),
        ];
    }

    // Delete PDF

    public function deletePdf(Course $course)
    {
        if (!$course->pdf_path) {
            return false;
        }
        Storage::disk('public')
            ->delete($course->pdf_path);

        $course->update([
            'pdf_path' => null,
            'pdf_original_name' => null,
            'pdf_size' => null,
        ]);
        return true;
    }
}
