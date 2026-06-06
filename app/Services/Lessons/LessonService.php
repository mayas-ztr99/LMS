<?php

namespace App\Services\Lessons;

use App\Models\Lesson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LessonService
{
    public function create(array $data, array $videos = [], array $materials = []): Lesson
    {
        return DB::transaction(function () use ($data, $videos, $materials) {
            $lesson = Lesson::create(collect($data)->except(['videos', 'materials'])->all());

            $this->attachMedia($lesson, $videos, $materials);

            return $lesson->fresh();
        });
    }

    public function update(Lesson $lesson, array $data, array $videos = [], array $materials = []): Lesson
    {
        return DB::transaction(function () use ($lesson, $data, $videos, $materials) {
            $lesson->update(collect($data)->except(['videos', 'materials'])->all());

            $this->attachMedia($lesson, $videos, $materials);

            return $lesson->fresh();
        });
    }

    public function delete(Lesson $lesson): void
    {
        DB::transaction(function () use ($lesson) {
            $lesson->clearMediaCollection('videos');
            $lesson->clearMediaCollection('materials');
            $lesson->delete();
        });
    }

    public function deleteMedia(Media $media): void
    {
        $media->delete();
    }

    private function attachMedia(Lesson $lesson, array $videos, array $materials): void
    {
        foreach ($videos as $video) {
            if ($video instanceof UploadedFile) {
                $lesson->addMedia($video)->toMediaCollection('videos', 'private');
            }
        }

        foreach ($materials as $file) {
            if ($file instanceof UploadedFile) {
                $lesson->addMedia($file)->toMediaCollection('materials', 'private');
            }
        }
    }

    public function reorder(array $lessonIds): void
    {
        DB::transaction(function () use ($lessonIds) {
            foreach ($lessonIds as $index => $lessonId) {
                Lesson::whereKey($lessonId)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }
}
