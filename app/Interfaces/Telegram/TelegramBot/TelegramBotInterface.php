<?php

namespace App\Interfaces\Telegram\TelegramBot;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use Illuminate\Http\Request;

interface TelegramBotInterface
{
    public function handle(Request $request): TelegramCommandInterface;

    public function sendMessage(array $message);
}