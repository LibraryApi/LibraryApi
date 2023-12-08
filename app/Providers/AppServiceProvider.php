<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RoleService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RoleService::class, function ($app) {
            return new RoleService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
