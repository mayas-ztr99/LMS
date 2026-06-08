<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'course_id' => ['sometimes', 'required', 'exists:courses,id'],
            'user_id'   => ['sometimes', 'required', 'exists:users,id'],
            'rating'    => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'comment'   => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
