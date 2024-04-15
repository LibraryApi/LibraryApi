<?php

namespace App\Services\Telegram\WebhookSenders\Buttons;
use App\Services\Telegram\WebhookSenders\WebhookSender;
use Illuminate\Support\Facades\Http;

class ButtonSender extends WebhookSender
{
    public function message(array $message): self
    {

        $this->setToken($message['bot_type'] ?? 'LibraryAdminBot');
        $this->send_method = 'sendMessage';
        $this->chat_id = $message['chat_id'] ?? 6109443752;

        $this->data = [
            'chat_id' => $this->chat_id,
            'text' => $message['text'],
            'parse_mode' => $message['parse_mode'] ?? 'html',
            'reply_markup' => $message['buttons'],
        ];
        if(isset($message['reply_id']))
        {
            $this->data['reply_parameters'] = [
                'message_id' => $message['reply_id']
            ];
        }
        return $this;
    }
}