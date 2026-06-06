<?php

namespace App\Http\Requests\Course;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Course::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],

            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'instructor_ids' => ['required', 'array'],
            'instructor_ids.*' => ['integer', Rule::exists('users','id')],
        ];
    }
}
