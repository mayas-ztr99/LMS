<?php

namespace App\Services;

use App\Events\StudentEnrolled;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function index()
    {
        return Enrollment::with(['student', 'course', 'coupon'])
            ->latest()
            ->paginate(15);
    }

    public function show(Enrollment $enrollment)
    {
        return $enrollment->load(['student', 'course', 'coupon']);
    }

    public function store(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {
            $exists = Enrollment::where('student_id', '=', $data['student_id'])
                ->where('course_id', '=', $data['course_id'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'course_id' => ['This student is already enrolled in this course.'],
                ]);
            }

            $enrollment = Enrollment::create([
                'student_id'  => $data['student_id'],
                'course_id'   => $data['course_id'],
                'coupon_id'   => $data['coupon_id'] ?? null,
                'status'      => $data['status'] ?? 'pending',
                'paid_price'  => $data['paid_price'] ?? 0,
                'enrolled_at' => now(),
            ]);

            event(new StudentEnrolled($enrollment));
            logger()->info('after Student enrolled event');
            return $enrollment->load(['student', 'course', 'coupon']);
        });
    }

    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        $enrollment->update([
            'coupon_id'  => $data['coupon_id'] ?? $enrollment->coupon_id,
            'status'     => $data['status'] ?? $enrollment->status,
            'paid_price' => array_key_exists('paid_price', $data)
                ? $data['paid_price']
                : $enrollment->paid_price,
        ]);

        return $enrollment->refresh()->load(['student', 'course', 'coupon']);
    }

    public function destroy(Enrollment $enrollment): void
    {
        $enrollment->delete();
    }

    public function studentCourses(User $student)
    {
        return $student->enrolledCourses()
            ->latest('enrollments.created_at')
            ->get();
    }

    public function courseStudents(Course $course)
    {
        return $course->students()
            ->latest('enrollments.created_at')
            ->get();
    }

    public function createFromPaidPayment(Payment $payment): Enrollment
    {
        return DB::transaction(function () use ($payment) {
            $existing = Enrollment::where('student_id', $payment->user_id)
                ->where('course_id', $payment->course_id)
                ->first();

            if ($existing) {
                return $existing;
            }

            $enrollment = Enrollment::create([
                'student_id'  => $payment->user_id,
                'course_id'   => $payment->course_id,
                'coupon_id'   => $payment->coupon_id,
                'status'      => 'active',
                'paid_price'  => $payment->final_amount,
                'enrolled_at' => now(),
            ]);

            event(new StudentEnrolled($enrollment));
            logger()->info('after Student enrolled event from payment');
            return $enrollment->load(['student', 'course', 'coupon']);
        });
    }
}
