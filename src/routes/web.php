<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QurbanController;
use App\Http\Controllers\SadaqahController;
use App\Http\Controllers\StudyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik (tanpa login) — F-USR-24
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/jadwal-sholat', [PrayerController::class, 'index'])->name('prayer.index');
Route::get('/sedekah', [SadaqahController::class, 'show'])->name('sadaqah');
Route::get('/keuangan', [FinanceController::class, 'public'])->name('finance.public');
Route::get('/kajian', [StudyController::class, 'public'])->name('study.public');
Route::get('/kajian/{study}', [StudyController::class, 'show'])->name('study.show');
Route::get('/qurban', [QurbanController::class, 'index'])->name('qurban.index');

/*
|--------------------------------------------------------------------------
| Autentikasi — F-USR-21..23
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Reset password via email — F-USR-23
    Route::get('/lupa-password', [PasswordResetController::class, 'showRequest'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Area User (butuh login) — F-USR-12,13,14,16,25
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/akun/qurban', [QurbanController::class, 'mine'])->name('user.qurban');
    Route::post('/akun/qurban/daftar', [QurbanController::class, 'register'])->name('user.qurban.register');
    Route::post('/akun/qurban/{participant}/setor', [QurbanController::class, 'deposit'])->name('user.qurban.deposit');
    Route::put('/akun/qurban/{participant}/label', [QurbanController::class, 'updateLabel'])->name('user.qurban.label');

    Route::get('/akun/notifikasi', [NotificationController::class, 'index'])->name('user.notifications');
    Route::post('/akun/notifikasi/{notification}/baca', [NotificationController::class, 'read'])->name('user.notifications.read');
});

require __DIR__.'/admin.php';
