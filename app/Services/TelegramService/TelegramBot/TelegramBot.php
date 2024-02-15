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
            'https://api.telegram.org/bot' . env("TELEGRAM_BOT_TOKEN") . '/' . $this->method,
            $this->data
        )->json();
    }
}
