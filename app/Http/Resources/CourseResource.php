<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'is_published' => $this->is_published,
            'level' => $this->level,

            'instructors' => $this->instructors->map(function ($instructor) {
                return [
                    'name' => $instructor->name,
                    'email' => $instructor->email,
                ];
            }),

            'category' => [
                'name' => $this->category?->name,
            ],

            'images'=> $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                ];
            }),
            'pdf_path'=> $this->pdf_path,
            'pdf_original_name'=> $this->pdf_original_name,
            'pdf_size'=> $this->pdf_size,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
