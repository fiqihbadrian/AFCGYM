<?php

namespace App\Providers;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Jumlah pesanan yang belum dikonfirmasi, tampil sebagai badge di sidebar.
        View::composer('admin.layouts.sidebar', function ($view) {
            $view->with('pesananMenunggu', Auth::check()
                ? Pesanan::where('status', Pesanan::STATUS_MENUNGGU)->count()
                : 0);
        });
    }
}
