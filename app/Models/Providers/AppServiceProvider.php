<?php

namespace App\Models\Providers;

use App\Events\BookCreated;
use App\Listeners\UpdateAuthorStats;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            BookCreated::class,
            UpdateAuthorStats::class,
        );
    }
}
