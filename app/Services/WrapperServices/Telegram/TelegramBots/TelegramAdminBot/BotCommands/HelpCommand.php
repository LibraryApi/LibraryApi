<?php

namespace App\Services\WrapperServices\Telegram\TelegramBots\TelegramAdminBot\BotCommands;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Services\WrapperServices\Telegram\TelegramBots\TelegramAdminBot\TelegramAdminBot;

class HelpCommand extends TelegramAdminBot implements TelegramCommandInterface
{
    public $request;
    public $handler;
    public function __construct($request, $handler){
        $this->request = $request;
        $this->handler = $handler;
    }
 
    public function buildMessage(){
        $message = [
            'text' => "Я пока не умею отвечать на такое"
        ];
        return $this->handler->buildMessage($message);
    }
}