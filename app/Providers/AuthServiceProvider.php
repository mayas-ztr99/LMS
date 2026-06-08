<?php

namespace App\Providers;

use App\Models\LatestNews;
use App\Models\Review;
use App\Policies\LatestNewsPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Review::class => ReviewPolicy::class,
        LatestNews::class => LatestNewsPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
