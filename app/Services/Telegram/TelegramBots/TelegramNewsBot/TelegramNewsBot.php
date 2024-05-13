<?php

namespace App\Services\Telegram\TelegramBots\TelegramNewsBot;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Interfaces\Telegram\TelegramBot\TelegramBotInterface;
use App\Interfaces\Telegram\WebhookHandler\WebhookHandlerInterface;
use App\Interfaces\Telegram\WebhookSender\WebhookSenderInterface;
use App\Services\Telegram\TelegramBots\TelegramNewsBot\BotCommands\HelpCommand;
use App\Services\Telegram\TelegramBots\TelegramNewsBot\BotCommands\StartCommand;
use Illuminate\Http\Request;

class TelegramNewsBot implements TelegramBotInterface
{
    protected $handler;
    public $sender;
    public $request;
    const BOT_TYPE = 'news';
    protected static $commands = [
        "/start"             => StartCommand::class,
        "/help"              => HelpCommand::class,
    ];

    public function __construct(WebhookHandlerInterface $handler, WebhookSenderInterface $sender)
    {
        $this->handler = $handler;
        $this->sender = $sender;
    }

    public function handle(Request $request): TelegramCommandInterface
    {
        $this->request = $request;
        $request = $request->input();

        $botConfig = [
            'type' => self::BOT_TYPE,
            'commands' => $this::$commands
        ];

        $callback = $this->handler->handle($request, $botConfig);

        return $callback;
    }

    public function sendMessage(array $message)
    {
        $messageType = $message['message_type'];

        $webhookSender = $this->sender->createMessageSender($messageType);
        return $webhookSender->message($message)->sendMessage();
    }
}
