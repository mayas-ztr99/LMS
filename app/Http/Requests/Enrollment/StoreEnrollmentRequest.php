<?php

namespace App\Http\Requests\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
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
            'student_id' => ['required','integer', 'exists:users,id'],
            'course_id' => ['required','integer', 'exists:courses,id'],
            'coupon_id' => ['nullable','integer', 'exists:coupons,id'],
            'status' => ['nullable','in:pending,active,completed,cancelled'],
            'paid_price' => ['nullable','numeric','min:0'],
        ];
    }
}
