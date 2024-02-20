<?php

namespace App\Services\Telegram\WebhookHandlers\Commands;

use App\Facades\Telegram;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class HelpCommandHandler extends WebhookHandler
{
   public function __construct(Request $request, string $botType)
   {
      parent::__construct($request, $botType);
   }
   public function handle(): array
   {

      $message = [
         'bot_type' => $this->bot_type,
         "chat_id" => $this->chat_id,
         "message_type" => 'text',
         "text" => "Hello"
      ];
      if ($this->bot_type == 'LibraryApiBot') {
         $message = $this->libraryApiHandle();
      }

      if ($this->bot_type == 'LibraryNewsBot') {
         $message = $this->libraryNewsHandle();
      }

      if ($this->bot_type == 'LibraryAdminBot') {
         $message = $this->libraryAdminHandle();
      }

      return $this->buildMessage($message);
   }

   public function libraryApiHandle()
   {
      $message = [
         'bot_type' => $this->bot_type,
         "chat_id" => $this->chat_id,
         "message_type" => 'text',
         "text" => "я пока не умею работать с этой командой Апи Бот"
      ];
      return $this->buildMessage($message);
   }
   public function libraryNewsHandle()
   {
      $message = [
         'bot_type' => $this->bot_type,
         "chat_id" => $this->chat_id,
         "message_type" => 'text',
         "text" => "я пока не умею работать с этой командой Новости Бот"
      ];
      return $this->buildMessage($message);
   }
   public function libraryAdminHandle()
   {
      $message = [
         'bot_type' => $this->bot_type,
         "chat_id" => $this->chat_id,
         "reply_id" => 1,
         "message_type" => 'text',
         "text" => "я пока не умею работать с этой командой Админ Бот"
      ];
      return $this->buildMessage($message);
   }
}
