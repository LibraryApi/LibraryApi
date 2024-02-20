<?php

namespace App\Interfaces\Telegram;

use Illuminate\Http\Request;

interface TelegramBotFactoryInterface
{
    public function createWebhookHandler(Request $request, string $botType): WebhookHandlerInterface;

    public function createWebhookSender(): WebhookSenderInterface;

}
