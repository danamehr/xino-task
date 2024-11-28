<?php

namespace App\Providers;

use App\Modules\Subscription\Services\SectionService;
use App\Modules\Subscription\Services\SectionServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SectionServiceInterface::class, SectionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
