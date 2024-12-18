<?php

namespace App\Services\WrapperServices\Telegram\TelegramBots\TelegramNewsBot\BotCommands;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;

class HelpCommand implements TelegramCommandInterface
{
    public $request;
    public $handler;
    public function __construct($request, $handler){
        $this->request = $request;
        $this->handler = $handler;
    }
 
    public function buildMessage(){
        $message = [
            'text' => "Я пока не умею отвечать на такое NEWS BOt"
        ];
        return $this->handler->buildMessage($message);
    }
}