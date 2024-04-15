<?php

namespace App\Services\Telegram\WebhookSenders;

use App\Interfaces\Telegram\WebhookSenderInterface;
use App\Services\Telegram\WebhookSenders\Buttons\ButtonSender;
use App\Services\Telegram\WebhookSenders\Documents\DocumentSender;
use App\Services\Telegram\WebhookSenders\Texts\TextSender;
use Illuminate\Support\Facades\Http;

class WebhookSender implements WebhookSenderInterface
{
    protected $token;
    protected $bot_type;
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
    }

    public function createMessageSender(string $messageType = 'text'): WebhookSenderInterface
    {
        if (isset(self::$senders[$messageType])) {
            return new self::$senders[$messageType];
        }
        //return new TextSender();
    }

    public function setToken(string $botType): ?string
    {
        if ($botType == 'LibraryApiBot') {
            $this->token = env('TELEGRAM_API_BOT_TOKEN');
        }
        if ($botType == 'LibraryNewsBot') {
            $this->token = env('TELEGRAM_NEWS_BOT_TOKEN');
        }
        if ($botType == 'LibraryAdminBot') {
            $this->token = env('TELEGRAM_ADMIN_BOT_TOKEN');
        }
        return null;
    }

    protected function setBotType(string $botType): void
    {
        $this->bot_type = $botType;
    }
}
