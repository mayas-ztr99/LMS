<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
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
            'code' =>[ 'required','string','unique:coupons,code','max:255'],
            'type' =>[ 'required',Rule::in(['fixed', 'percent'])],
            'value' => [ 'required','numeric','min:0'],
            'max_uses' => [ 'required','integer','min:1'],
            'is_active' => [ 'required','boolean'],
            'expires_at' => [ 'required','date_format:Y-m-d','after:today'],
        ];
    }
    public function messages(): array
    {
        return [
            'code.required' => 'The coupon code is required.',
            'code.unique' => 'The coupon code must be unique.',
            'code.max' => 'The coupon code may not be greater than 255 characters.',
            'type.required' => 'The coupon type is required.',
            'type.in' => 'The coupon type must be either fixed or percent.',
            'value.required' => 'The coupon value is required.',
            'value.numeric' => 'The coupon value must be a number.',
            'value.min' => 'The coupon value must be at least 0.',
            'max_uses.integer' => 'The max uses must be an integer.',
            'max_uses.min' => 'The max uses must be at least 1.',
            'expires_at.date_format' => 'The expiration date must be in the format YYYY-MM-DD.',
            'expires_at.after' => 'The expiration date must be a future date.',
        ];
    }
}
