<?php

namespace App\Services\WrapperServices\Telegram;

use App\Interfaces\Telegram\TelegramBot\TelegramBotInterface;
use App\Interfaces\Telegram\WebhookHandler\WebhookHandlerInterface;
use App\Interfaces\Telegram\WebhookSender\WebhookSenderInterface;
use App\Services\WrapperServices\Telegram\WebhookHandlers\WebhookHandler;
use App\Services\WrapperServices\Telegram\WebhookSenders\WebhookSender;
use App\Services\WrapperServices\Telegram\TelegramBots\TelegramAdminBot\TelegramAdminBot;
use App\Services\WrapperServices\Telegram\TelegramBots\TelegramApiBot\TelegramApiBot;
use App\Services\WrapperServices\Telegram\TelegramBots\TelegramNewsBot\TelegramNewsBot;
use App\Services\WrapperServices\Telegram\WebhookHandlers\Keyboards\KeyboardsHandler;

class TelegramBotFactory
{

    public function createWebhookHandler($keyboardsHandler): WebhookHandlerInterface
    {
        return new WebhookHandler($keyboardsHandler);
    }


    public function createWebhookSender(): WebhookSenderInterface
    {
        return new WebhookSender();
    }

    public function createKeyboardsHandler()
    {
        return new KeyboardsHandler();
    }


    public function createTelegramBot(string $botType): TelegramBotInterface
    {
        $keyboardsHandler = $this->createKeyboardsHandler();
        $handler = $this->createWebhookHandler($keyboardsHandler);
        $sender = $this->createWebhookSender();

        if ($botType == TelegramAdminBot::BOT_TYPE) {
            return new TelegramAdminBot($handler, $sender);
        }

        if ($botType == TelegramNewsBot::BOT_TYPE) {
            return new TelegramNewsBot($handler, $sender);
        }

        if ($botType == TelegramApiBot::BOT_TYPE) {
            return new TelegramApiBot($handler, $sender);
        }
        return new TelegramAdminBot($handler, $sender);
    }
}
