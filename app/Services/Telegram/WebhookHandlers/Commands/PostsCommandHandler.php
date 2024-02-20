<?php

namespace App\Services\Telegram\WebhookHandlers\Commands;

use App\Facades\Telegram;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class PostsCommandHandler extends WebhookHandler
{
   public function __construct(Request $request, string $botType)
   {
      parent::__construct($request, $botType);
   }
   public function handle(): array
   {

      if ($this->bot_type == 'LibraryApiBot') {
         $message = $this->libraryApiHandle();
      }

      if ($this->bot_type == 'LibraryNewsBot') {
         $message = $this->libraryNewsHandle();
      }

      if ($this->bot_type == 'LibraryAdminBot') {
         $message = $this->libraryAdminHandle();
      }

      return $message;
   }

   public function libraryNewsHandle()
   {
      $message = ["text" => "Ответ на команду Posts NewsBot"];
      return $this->buildMessage($message);
   }

   public function libraryAdminHandle()
   {
      $message = ["text" => "Ответ на команду Posts AdminBot"];
      return $this->buildMessage($message);
   }

   public function libraryApiHandle()
   {
      $message = ["text" => "Ответ на команду Posts ApiBot"];
      return $this->buildMessage($message);
   }
}