<?php

namespace App\Services\Telegram\WebhookSenders\Buttons;
use App\Services\Telegram\WebhookSenders\WebhookSender;
use Illuminate\Support\Facades\Http;

class ButtonSender extends WebhookSender
{
    protected $data;
    protected $method;

    public function sendMessage(): void
    {
        Http::post(
            'https://api.telegram.org/bot' . $this->token . '/' . $this->method,
            $this->data
        )->json();
    }
    public function message(array $message): self
    {

        $this->setToken($message['bot_type']);

        $this->method = 'sendMessage';
        $this->data = [
            'chat_id' => $message['chat_id'],
            'text' => $message['text'],
            'parse_mode' => 'html',
            'reply_markup' => $message['buttons'],
        ];
        if($message['reply_id'])
        {
            $this->data['reply_parameters'] = [
                'message_id' => $message['reply_id']
            ];
        }
        return $this;
    }

    public function editMessage()
    {
        return "editText";
    }
}