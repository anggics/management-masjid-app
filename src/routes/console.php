<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Penjadwalan tugas (cron) — dijalankan oleh container "scheduler"
|--------------------------------------------------------------------------
*/

// Pembersihan cache jadwal sholat yang sudah kedaluwarsa (lebih dari 7 hari).
Schedule::call(function () {
    \App\Models\PrayerCache::where('cached_at', '<', now()->subDays(7))->delete();
})->dailyAt('03:00')->name('cleanup-prayer-cache');
