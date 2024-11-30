<?php

use App\Modules\Payment\Http\V1\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->prefix('v1/payments')->name('v1.payments.')->group(function () {
    Route::post('renewal-webhook', [PaymentController::class, 'renewal'])->name('renewal-webhook');
});


