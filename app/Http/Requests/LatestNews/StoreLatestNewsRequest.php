<?php

namespace App\Http\Requests\LatestNews;

use Illuminate\Foundation\Http\FormRequest;

class StoreLatestNewsRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer','min:0'],
        ];
    }
}
