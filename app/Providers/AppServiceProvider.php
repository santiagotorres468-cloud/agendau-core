<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Le enseñamos a Laravel quién es un "admin-only"
        Gate::define('admin-only', function ($user) {
            return $user->rol === 'admin';
        });
    }
}
