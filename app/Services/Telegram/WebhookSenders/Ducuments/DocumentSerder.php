<?php

namespace App\Services\Telegram\WebhookSenders\Documents;

class DocumentSerder
{
    public function sendMessage($message){
        return 'DocumentSendler';
    }
    public function messages()
    {
        return "Text";
    }

    public function editMessage()
    {
        return "editText";
    }
}