<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ManageUserRoleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'user_id'=>['required','exists:users,id'],
            'role'=>['required','string','exists:roles,name']
        ];
    }
}
