<?php
namespace App\Services\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class CourseService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;

        return Course::query()
            ->with(['instructor', 'category'])
            ->filter($filters)
            ->when(isset($filters['sort_by']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_dir'] ?? 'desc');
            }, fn ($query) => $query->latest())
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, User $actor): Course
    {
        return DB::transaction(function () use ($data) {
            $instructorIds = $data['instructor_ids'] ?? [];
            unset($data['instructor_ids']);
            $course = Course::create($data);
            $course->instructors()->attach($instructorIds);
            return $course->load(['instructors', 'category']);
        });
    }

    public function update(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            $instructorIds = $data['instructor_ids'] ?? [];
            unset($data['instructor_ids']);
            $course->update($data);
            if (!empty($instructorIds)) {
                $course->instructors()->sync($instructorIds);
            }
            return $course->load(['instructors', 'category']);
        });
    }

    public function destroy(Course $course): void
    {
        DB::transaction(function () use ($course): void {
            $course->instructors()->detach();
            $course->delete();
        });
    }
}
