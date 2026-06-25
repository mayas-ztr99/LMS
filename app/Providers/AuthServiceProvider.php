<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LatestNews;
use App\Models\Lesson;
use App\Models\Review;
use App\Policies\CouponPolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\LatestNewsPolicy;
use App\Policies\LessonPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Review::class => ReviewPolicy::class,
        LatestNews::class => LatestNewsPolicy::class,
        Coupon::class => CouponPolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Course::class => CoursePolicy::class,
        Lesson::class => LessonPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
