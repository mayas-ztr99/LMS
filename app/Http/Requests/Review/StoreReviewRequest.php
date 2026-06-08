<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'user_id'   => ['required', 'exists:users,id'],
            'rating'    => ['required', 'integer', 'min:1', 'max:5'],
            'comment'   => ['nullable', 'string', 'max:5000'],
        ];
    }
}
