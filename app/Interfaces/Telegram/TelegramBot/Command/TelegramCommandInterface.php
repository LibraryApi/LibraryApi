<?php

namespace App\Interfaces\Telegram\TelegramBot\Command;

use Illuminate\Http\Request;

interface TelegramCommandInterface
{
    public function buildMessage();
}