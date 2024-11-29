<?php

namespace App\Providers;

use App\Modules\Invoice\Services\InvoiceService;
use App\Modules\Invoice\Services\InvoiceServiceInterface;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\Services\PaymentServiceInterface;
use App\Modules\Subscription\Services\PlanService;
use App\Modules\Subscription\Services\PlanServiceInterface;
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
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(PlanServiceInterface::class, PlanService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
