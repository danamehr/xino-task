<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Subscription\Providers\SubscriptionServiceProvider::class,
    App\Modules\User\Providers\UserServiceProvider::class,
    App\Modules\Invoice\Providers\InvoiceServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
];
