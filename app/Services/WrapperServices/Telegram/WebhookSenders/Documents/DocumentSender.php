<?php

namespace App\Services\WrapperServices\Telegram\WebhookSenders\Documents;
use App\Services\WrapperServices\Telegram\WebhookSenders\WebhookSender;
use Illuminate\Support\Facades\Http;

class DocumentSender extends WebhookSender
{
    protected $document;
    protected $filename;
    public function sendMessage(): void
    {
        $res = Http::attach('document', $this->document, $this->filename)->post(
            'https://api.telegram.org/bot' . $this->token . '/' . $this->send_method,
            $this->data
        )->json();
    }
    public function message(array $message): self
    {
        $this->setToken($message['bot_type'] ?? 'LibraryAdminBot');
        $this->send_method = 'sendDocument';
        $this->chat_id = $message['chat_id'] ?? 6109443752;
        $this->document = $message['document'];
        $this->filename = $message['filename'] ?? "file.jpg";

        $this->data = [
            'chat_id' => $this->chat_id,
            'caption' => $message['caption'] ?? 'документ',
        ];
        if(isset($message['reply_id']))
        {
            $this->data['reply_to_message_id'] = $message['reply_id'];
        }
        return $this;
    }
}