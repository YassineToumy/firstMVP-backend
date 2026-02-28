<?php

namespace App\Providers;

use App\Services\ListingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ListingService::class);
    }

    public function boot(): void
    {
        //
    }
}