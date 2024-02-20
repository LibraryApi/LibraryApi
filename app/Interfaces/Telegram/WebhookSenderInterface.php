<?php

namespace App\Interfaces\Telegram;

interface WebhookSenderInterface
{
    public function sendMessage(): void;
    public function createMessageSender(array $message): ?WebhookSenderInterface;
    public function message(array $message): self;
}
