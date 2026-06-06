<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'price',
        'is_published',
        'level',
        'pdf_path',
        'pdf_original_name',
        'pdf_size',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructors', 'course_id', 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $value) {
                $q->where(function (Builder $inner) use ($value) {
                    $inner->where('title', 'like', "%{$value}%")
                        ->orWhere('description', 'like', "%{$value}%");
                });
            })
            ->when($filters['instructor_id'] ?? null, fn(Builder $q, int $id) => $q->where('instructor_id', $id))
            ->when($filters['category_id'] ?? null, fn(Builder $q, int $id) => $q->where('category_id', $id))
            ->when($filters['level'] ?? null, fn(Builder $q, string $level) => $q->where('level', $level))
            ->when(array_key_exists('is_published', $filters), function (Builder $q) use ($filters) {
                $bool = filter_var($filters['is_published'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if (! is_null($bool)) {
                    $q->where('is_published', $bool);
                }
            })
            ->when($filters['min_price'] ?? null, fn(Builder $q, $price) => $q->where('price', '>=', $price))
            ->when($filters['max_price'] ?? null, fn(Builder $q, $price) => $q->where('price', '<=', $price));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');
    }
    public function hasInstructor(int $userId): bool
    {
        return $this->instructors()->where('users.id', $userId)->exists();
    }
}
