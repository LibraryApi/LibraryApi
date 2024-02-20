<?php

namespace App\Services\TelegramService\Webhook\Commands;

use App\Facades\Telegram;
use App\Services\TelegramService\Webhook\TelegramWebhook;
use Illuminate\Http\Request;

class BooksToday extends TelegramWebhook
{
    public $request;

    public function __construct(Request $request)
    {
      $this->request = $request;
    }
    
    public function SendMessage(){
      return Telegram::message($this->chat_id, 'это командa booksToday')->sendMessage();
    }
}