<?php

namespace App\Services\Telegram\TelegramBots\TelegramAdminBot;

use App\Services\Telegram\TelegramBots\TelegramAdminBot\BotCommands\HelpCommand;
use App\Services\Telegram\TelegramBots\TelegramAdminBot\BotCommands\StartCommand;


class TelegramAdminBot
{
    protected $handler;
    public $sender;
    public $request;

    protected static $commands = [
        "/start"             => StartCommand::class,
        "/help"              => HelpCommand::class,
    ];

    public function __construct($handler, $sender)
    {
        $this->handler = $handler;
        $this->sender = $sender;
    }

    public function handle($request)
    {
        $this->request = $request;
        $request = $request->input();

        $botConfig = [
            'type' => 'admin',
            'commands' => $this::$commands
        ];

        $callback = $this->handler->handle($request, $botConfig);

        return $callback;
    }
    
    public function sendMessage($message)
    {
        $messageType = $message['message_type'];

        $webhookSender = $this->sender->createMessageSender($messageType);
        return $webhookSender->message($message)->sendMessage();
    }
}
