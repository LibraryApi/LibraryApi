<?php

namespace App\Services\Telegram;

use App\Interfaces\Telegram\TelegramBotFactoryInterface;
use App\Interfaces\Telegram\WebhookHandlerInterface;
use App\Interfaces\Telegram\WebhookSenderInterface;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use App\Services\Telegram\WebhookSenders\WebhookSender;
use Illuminate\Http\Request;

class TelegramBotFactory implements TelegramBotFactoryInterface
{

    public function createWebhookHandler(Request $request, string $botType): WebhookHandlerInterface
    {
        return new WebhookHandler($request, $botType);
    }


    public function createWebhookSender(): WebhookSenderInterface
    {
        return new WebhookSender();
    }
}
