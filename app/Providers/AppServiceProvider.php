<?php

namespace App\Providers;

use App\Facades\Telegram;
use App\Services\TelegramService\TelegramFactory;
use Illuminate\Support\ServiceProvider;
use App\Services\RoleService;
use App\Services\TelegramService\Webhook\TelegramWebhook;
use Illuminate\Http\Request;

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
    public function boot(Request $request): void
    {
        $this->app->bind(Telegram::class, function(){
            return new TelegramFactory();
        });

        $this->app->bind(TelegramWebhook::class, function() {
            return new TelegramWebhook();
        });
    }
}
