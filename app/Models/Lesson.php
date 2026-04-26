<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'content',
        'video_url',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
