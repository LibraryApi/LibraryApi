<?php

namespace App\Services\Telegram\WebhookSenders;

use App\Interfaces\Telegram\WebhookSenderInterface;
use App\Services\Telegram\WebhookSenders\Buttons\ButtonSender;
use App\Services\Telegram\WebhookSenders\Documents\DocumentSerder;
use App\Services\Telegram\WebhookSenders\Texts\TextSender;
use Illuminate\Support\Facades\Http;

class WebhookSender implements WebhookSenderInterface
{
    protected $token;

    protected $bot_type;

    protected $chat_id;
    protected static $senders = [
        "text" => TextSender::class,
        "document" => DocumentSerder::class,
        "buttons" => ButtonSender::class

    ];
    public function sendMessage(): void
    {
        Http::post(
            'https://api.telegram.org/bot' . $this->token . '/' . "sendMessage",
            [
                'chat_id' => $this->chat_id,
                'text' => "Это ответ от родителя сендлера",
                'parse_mode' => 'html',
                'link_preview_options' => [
                    'is_disabled' => true
                ],

            ],
        )->json();
    }

    public function createMessageSender(array $message): ?WebhookSenderInterface
    {
        if (isset(self::$senders[$message['message_type']])) {
            return new self::$senders[$message['message_type']];
        }
        return null;
    }
    public function message(array $message): self
    {
        $this->chat_id = $message['chat_id'];
        $method = 'sendMessage';
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message['text'],
            'parse_mode' => $message['parse_mode'],
            'link_preview_options' => [
                'is_disabled' => true
            ],

        ];
        if ($message['reply_id']) {
            $data['reply_parameters'] = [
                'message_id' => $message['reply_id']
            ];
        }
        return $this;
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
