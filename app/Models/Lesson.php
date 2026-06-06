<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Lesson extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'sort_order',
        'is_published',
    ];
    protected $casts = [
        'is_published' => 'boolean',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('videos');
        $this->addMediaCollection('materials');
    }
    // public function registerMediaConversions(?Media $media = null): void
    // {
    //     $this->addMediaConversion('thumb')
    //         ->width(368)
    //         ->height(232)
    //         ->sharpen(10);
    // }
}
