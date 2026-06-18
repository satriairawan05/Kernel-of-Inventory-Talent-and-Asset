<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Carbon::macro('rupiah', function ($amount) {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config('app.locale', 'id');
        \Carbon\Carbon::setLocale('id');
        
        Paginator::useBootstrapFive();
    }
}
