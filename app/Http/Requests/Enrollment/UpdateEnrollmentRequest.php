<?php

namespace App\Http\Requests\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
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
            'coupon_id' => ['sometimes','nullable','integer', 'exists:coupons,id'],
            'status' => ['sometimes','nullable','in:pending,active,completed,cancelled'],
            'paid_price' => ['sometimes','nullable','numeric','min:0'],
        ];
    }
}
