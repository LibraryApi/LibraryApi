<?php

namespace App\Services\Telegram\WebhookSenders\Documents;
use App\Services\Telegram\WebhookSenders\WebhookSender;

class DocumentSender extends WebhookSender
{
    public function message(array $message): self
    {
        $this->setToken($message['bot_type'] ?? 'LibraryAdminBot');
        $this->send_method = 'sendDocument';
        $this->chat_id = $message['chat_id'] ?? 6109443752;

        $this->data = [
            'chat_id' => $this->chat_id,
            'document' => $message['document'], // Путь к файлу или URL
            'caption' => $message['caption'] ?? '', // Описание документа, необязательно
        ];
        if(isset($message['reply_id']))
        {
            $this->data['reply_to_message_id'] = $message['reply_id'];
        }
        return $this;
    }

    public function editMessage()
    {
        return "editText";
    }
}