<?php

namespace App\Interfaces\Telegram;


interface WebhookHandlerInterface
{
    
    public function handle(): ?array;
    public function createMessageHandler(): ?WebhookHandlerInterface;
}
