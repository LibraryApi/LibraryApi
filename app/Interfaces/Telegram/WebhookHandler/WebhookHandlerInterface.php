<?php

namespace App\Interfaces\Telegram\WebhookHandler;


interface WebhookHandlerInterface
{
    
    public function handle(array $request,array $botConfig);
}
