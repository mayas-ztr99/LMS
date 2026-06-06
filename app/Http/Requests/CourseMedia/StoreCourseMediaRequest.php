<?php

namespace App\Http\Requests\CourseMedia;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCourseMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Admin');
    }

    public function rules(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
