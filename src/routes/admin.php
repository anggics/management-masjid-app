<?php

use App\Http\Controllers\Admin\CommitteeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PrayerOverrideController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QurbanController;
use App\Http\Controllers\Admin\QurbanTypeController;
use App\Http\Controllers\Admin\QurbanYearController;
use App\Http\Controllers\Admin\StudyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Panel Admin & Staff (RBAC server-side)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])
    ->prefix('panel')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Profil masjid (admin + staff)
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

        // Master jenis hewan qurban (admin + staff)
        Route::get('/jenis-qurban', [QurbanTypeController::class, 'index'])->name('qurban-types.index');
        Route::post('/jenis-qurban', [QurbanTypeController::class, 'store'])->name('qurban-types.store');
        Route::put('/jenis-qurban/{qurbanType}', [QurbanTypeController::class, 'update'])->name('qurban-types.update');
        Route::delete('/jenis-qurban/{qurbanType}', [QurbanTypeController::class, 'destroy'])->name('qurban-types.destroy');

        // Master tahun qurban (hijriah) — admin + staff
        Route::get('/tahun-qurban', [QurbanYearController::class, 'index'])->name('qurban-years.index');
        Route::post('/tahun-qurban', [QurbanYearController::class, 'store'])->name('qurban-years.store');
        Route::put('/tahun-qurban/{qurbanYear}', [QurbanYearController::class, 'update'])->name('qurban-years.update');
        Route::delete('/tahun-qurban/{qurbanYear}', [QurbanYearController::class, 'destroy'])->name('qurban-years.destroy');

        // Panitia qurban (admin + staff)
        Route::get('/panitia-qurban', [CommitteeController::class, 'index'])->name('committees.index');
        Route::post('/panitia-qurban', [CommitteeController::class, 'store'])->name('committees.store');
        Route::put('/panitia-qurban/{committee}', [CommitteeController::class, 'update'])->name('committees.update');
        Route::delete('/panitia-qurban/{committee}', [CommitteeController::class, 'destroy'])->name('committees.destroy');

        // Data qurban (admin + staff)
        Route::get('/qurban', [QurbanController::class, 'index'])->name('qurban.index');
        Route::post('/qurban', [QurbanController::class, 'store'])->name('qurban.store');
        Route::put('/qurban/{participant}', [QurbanController::class, 'update'])->name('qurban.update');
        Route::delete('/qurban/{participant}', [QurbanController::class, 'destroy'])->name('qurban.destroy');

        // Verifikasi bukti transfer (admin + staff)
        Route::get('/setoran', [DepositController::class, 'index'])->name('deposits.index');
        Route::post('/setoran/{deposit}/verifikasi', [DepositController::class, 'verify'])->name('deposits.verify');
        Route::post('/setoran/{deposit}/tolak', [DepositController::class, 'reject'])->name('deposits.reject');

        // Jadwal kajian (admin + staff)
        Route::get('/kajian', [StudyController::class, 'index'])->name('study.index');
        Route::post('/kajian', [StudyController::class, 'store'])->name('study.store');
        Route::put('/kajian/{study}', [StudyController::class, 'update'])->name('study.update');
        Route::delete('/kajian/{study}', [StudyController::class, 'destroy'])->name('study.destroy');

        // Override jadwal sholat (admin + staff)
        Route::get('/jadwal-sholat', [PrayerOverrideController::class, 'index'])->name('prayer.index');
        Route::post('/jadwal-sholat', [PrayerOverrideController::class, 'store'])->name('prayer.store');
        Route::delete('/jadwal-sholat/{override}', [PrayerOverrideController::class, 'destroy'])->name('prayer.destroy');

        // Laporan keuangan (lihat & input: admin + staff)
        Route::get('/keuangan', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/keuangan', [FinanceController::class, 'store'])->name('finance.store');
        Route::get('/keuangan/export', [FinanceController::class, 'export'])->name('finance.export');
    });

/*
|--------------------------------------------------------------------------
| Khusus Admin penuh
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('panel')
    ->name('admin.')
    ->group(function () {
        // Metode pembayaran QRIS/rekening
        Route::get('/pembayaran', [PaymentMethodController::class, 'index'])->name('payments.index');
        Route::post('/pembayaran', [PaymentMethodController::class, 'store'])->name('payments.store');
        Route::put('/pembayaran/{method}', [PaymentMethodController::class, 'update'])->name('payments.update');
        Route::delete('/pembayaran/{method}', [PaymentMethodController::class, 'destroy'])->name('payments.destroy');

        // Hapus catatan keuangan (admin only, dengan audit log)
        Route::put('/keuangan/{record}', [FinanceController::class, 'update'])->name('finance.update');
        Route::delete('/keuangan/{record}', [FinanceController::class, 'destroy'])->name('finance.destroy');

        // Manajemen pengguna (admin only)
        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
