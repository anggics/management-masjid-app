<?php

namespace App\Providers;

use App\Models\Mosque;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Composer berjalan lazy saat view dirender (request time), bukan saat
        // boot/build. Ini mencegah query DB dijalankan saat `composer dump-autoload`
        // / `package:discover` pada tahap build Docker (MySQL belum tersedia).
        View::composer('*', function ($view) {
            try {
                $view->with('mosque', Mosque::current());
            } catch (\Throwable $e) {
                $view->with('mosque', null);
            }
        });
    }
}
