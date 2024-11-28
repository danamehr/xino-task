<?php

use App\Modules\Subscription\Http\V1\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->prefix('v1/subscriptions')->name('v1.subscriptions.')->group(function () {
    Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('sections/{slug}', [SectionController::class, 'show'])->name('sections.show');
});


