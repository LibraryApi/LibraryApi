<?php

namespace App\Interfaces\Telegram;

interface WebhookSenderInterface
{
    public function sendMessage(): void;
    public function createMessageSender(string $messageType): WebhookSenderInterface;
}
