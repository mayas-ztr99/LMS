<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseCheckoutRequest extends FormRequest
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
            'coupon_code'=>['nullable','string','exists:coupons,code'],
        ];
    }
    public function messages():array
    {
        return [
            'coupon_code.exists'=>'The coupon code is invalid.'
        ];
    }
}
