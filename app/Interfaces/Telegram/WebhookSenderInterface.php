<?php

namespace App\Interfaces\Telegram;

interface WebhookSenderInterface
{
    public function sendMessage(): void;

    public function message(array $message): self;
    public function createMessageSender(string $messageType): WebhookSenderInterface;
}
