<?php

namespace App\Interfaces\Telegram;

use App\Interfaces\Telegram\TelegramBot\TelegramBotInterface;
use App\Interfaces\Telegram\WebhookHandler\WebhookHandlerInterface;
use App\Interfaces\Telegram\WebhookSender\WebhookSenderInterface;
use Illuminate\Http\Request;

interface TelegramBotFactoryInterface
{
    public function createWebhookHandler(Request $request, string $botType): WebhookHandlerInterface;

    public function createWebhookSender(): WebhookSenderInterface;

    public function createTelegramBot(string $bottype): TelegramBotInterface;

}
