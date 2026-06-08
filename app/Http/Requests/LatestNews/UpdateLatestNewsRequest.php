<?php

namespace App\Http\Requests\LatestNews;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLatestNewsRequest extends FormRequest
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
            'title' => ['sometimes', 'required','string', 'max:255'],
            'content' => ['sometimes','required', 'string'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->hasAny(['title', 'content', 'sort_order'])) {
                $validator->errors()->add('payload', 'At least one field (title, content, sort_order) must be provided for update.');
            }
        });
    }
}
