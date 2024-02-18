<?php

namespace App\Services\TelegramService\TelegramBot;

use Illuminate\Support\Facades\Http;

class TelegramBot
{
    protected $data;
    protected $method;

    protected $token;

    public function sendMessage(): ?array
    {
        return Http::post(
            'https://api.telegram.org/bot' . $this->token . '/' . $this->method,
            $this->data
        )->json();
    }

    public function setToken($botType): ?string
    {
        if ($botType == 'LibraryApiBot') {
            $this->token = env('TELEGRAM_BOT_TOKEN');
        }
        if ($botType == 'LibraryNewsBot') {
            $this->token = env('TELEGRAM_NEWS_BOT_TOKEN');
        }
        if ($botType == 'LibraryAdminBot') {
            $this->token = env('TELEGRAM_ADMIN_BOT_TOKEN');
        }
        return null;
    }
}
