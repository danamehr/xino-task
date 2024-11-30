<?php

use App\Modules\Invoice\Http\V1\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->prefix('v1/invoices')->name('v1.invoices.')->group(function () {
    Route::get('', [InvoiceController::class, 'index'])->name('invoices.index');
});
