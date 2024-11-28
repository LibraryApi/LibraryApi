<?php

namespace App\Services\WrapperServices\Telegram\WebhookSenders;

use App\Interfaces\Telegram\WebhookSender\WebhookSenderInterface;
use App\Services\WrapperServices\Telegram\WebhookSenders\Buttons\ButtonSender;
use App\Services\WrapperServices\Telegram\WebhookSenders\Documents\DocumentSender;
use App\Services\WrapperServices\Telegram\WebhookSenders\Texts\TextSender;
use Illuminate\Support\Facades\Http;

class WebhookSender implements WebhookSenderInterface
{
    protected $token;
    protected $chat_id;
    protected $send_method;
    protected $data;
    protected static $senders = [
        "text" => TextSender::class,
        "document" => DocumentSender::class,
        "buttons" => ButtonSender::class

    ];
    public function sendMessage(): void
    {
        $res = Http::post(
            'https://api.telegram.org/bot' . $this->token . '/' . $this->send_method,
            $this->data
        )->json();
        return;
    }

    public function createMessageSender(string $messageType = 'text'): WebhookSenderInterface
    {
        if (isset(self::$senders[$messageType])) {
            return new self::$senders[$messageType];
        }
        return new TextSender();
    }

    public function setToken(string $botType): void
    {
        $botConfig = config("telegram.bots.{$botType}");
        $this->token = $botConfig['token'];
    }

    public function message(array $message): self {
        return $this;
    }
}