<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <--- Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind(); // <--- Tambahkan ini agar desain tombolnya rapi

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $pendingRefundsCount = 0;
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
                $pendingRefundsCount = \App\Models\Refund::where('status', 'pending')->count();
            }
            $view->with('pendingRefundsCount', $pendingRefundsCount);
        });
    }
}