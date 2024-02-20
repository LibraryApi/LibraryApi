<?php

namespace App\Services\Telegram\WebhookHandlers\Commands;

use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class TextHadnler extends WebhookHandler
{
   public function __construct(Request $request, string $botType)
   {
      parent::__construct($request, $botType);
   }
   public function handle(): array
   {
      $message =  [
         "text" => "это ответ на text"
      ];
      return $this->buildMessage($message);
   }
}