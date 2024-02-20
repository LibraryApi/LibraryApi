<?php

namespace App\Services\TelegramService\Webhook\Commands;

use App\Facades\Telegram;
use App\Services\TelegramService\Webhook\TelegramWebhook;
use Illuminate\Http\Request;

class Admin extends TelegramWebhook
{
    public $request;

    public function __construct(Request $request)
    {
      $this->request = $request;
    }

    public function SendMessage(){

      if($this->bot_type == 'LibraryApiBot'){
        return $this->libraryApiHandle();
      }
  
      if($this->bot_type == 'LibraryNewsBot'){
        return $this->libraryNewsHandle();
      }
  
      if($this->bot_type == 'LibraryAdminBot'){
        return $this->libraryAdminHandle();
      }
      return null;
    }
    
    public function libraryApiHandle()
    {
      Telegram::message($this->chat_id, 'Не знаю такой команды')->sendMessage();
    }
    public function libraryNewsHandle()
    {
      Telegram::message($this->chat_id, 'Не знаю такой команды')->sendMessage();
    }
  
    public function libraryAdminHandle(){
      return Telegram::message($this->chat_id, 'это админские команды')->sendMessage();
    }
}