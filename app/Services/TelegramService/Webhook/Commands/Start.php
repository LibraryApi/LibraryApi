<?php

namespace App\Services\TelegramService\Webhook\Commands;

use App\Facades\Telegram;
use App\Services\TelegramService\Webhook\TelegramWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Start extends TelegramWebhook
{
  public function sendMessage(): ?array
  {

    Telegram::message($this->chat_id, 'Привет! Спасибо что подписался')->sendMessage();
    return Telegram::buttons($this->chat_id, 'Это твоя карманная библиотека, ты можешь отсюда просматривать и читать книги или статьи', [
        'inline_keyboard' => [
          [
            [
              'text' => 'список книг',
              'callback_data' => 1
            ],
            [
              'text' => 'список статей',
              'callback_data' => 2
            ]
          ]
        ],
    ])->sendMessage();
  }
}
