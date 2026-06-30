<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()!==null;
    }
    public function rules(): array
    {
        return [
            'fcm_token' => 'required|string',
        ];
    }
}
