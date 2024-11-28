<?php

namespace App\Modules\Subscription\Providers;

use App\Modules\Subscription\Models\Section;
use App\Modules\Subscription\Policies\SectionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/v1.php');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'subscription');

        Gate::policy(Section::class, SectionPolicy::class);
    }
}
