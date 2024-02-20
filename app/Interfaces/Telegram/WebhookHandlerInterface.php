<?php

namespace App\Interfaces\Telegram;


interface WebhookHandlerInterface
{
    public function createMessageHandler(): ?WebhookHandlerInterface;
    public function handle(): ?array;
}
