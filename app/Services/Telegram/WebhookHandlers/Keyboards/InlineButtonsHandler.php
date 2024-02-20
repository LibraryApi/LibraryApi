<?php

namespace App\Services\Telegram\WebhookHandlers\Commands;

use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class InlineButtonsHadnler extends WebhookHandler
{
   public function __construct(Request $request, string $botType)
   {
      parent::__construct($request, $botType);
   }
   public function handle(): array
   {
      $message =  [
         "text" => "это ответ на keyboard"
      ];
      return $this->buildMessage($message);
   }
}