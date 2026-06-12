<?php

use App\Http\Controllers\Api\PrayerApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 (JSON untuk PWA / AJAX)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    Route::get('/prayer-times', [PrayerApiController::class, 'index'])->name('api.prayer-times');
});
