<?php

namespace App\Services\Telegram;

use App\Interfaces\Telegram\TelegramBotFactoryInterface;
use App\Interfaces\Telegram\WebhookHandlerInterface;
use App\Interfaces\Telegram\WebhookSenderInterface;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use App\Services\Telegram\WebhookHandlers\Handler;
use App\Services\Telegram\WebhookSenders\WebhookSender;
use Illuminate\Http\Request;
use App\Services\Telegram\TelegramBots\TelegramAdminBot\TelegramAdminBot;
use App\Services\Telegram\TelegramBots\TelegramApiBot\TelegramApiBot;
use App\Services\Telegram\TelegramBots\TelegramNewsBot\TelegramNewsBot;
class TelegramBotFactory
{

    public function createWebhookHandler(Request $request, string $botType): WebhookHandlerInterface
    {
        return new WebhookHandler($request, $botType);
    }


    public function createWebhookSender(): WebhookSenderInterface
    {
        return new WebhookSender();
    }

    public function createTelegramBot($botType)
    {
        $handler = new Handler();
        $sender = new WebhookSender();
        if ($botType == "admin") {
            return new TelegramAdminBot($handler, $sender);
        }

        if ($botType == 'news') {
            return new TelegramNewsBot($handler, $sender);
        }

        if ($botType == 'api') {
            return new TelegramApiBot($handler, $sender);
        }
    }
}
