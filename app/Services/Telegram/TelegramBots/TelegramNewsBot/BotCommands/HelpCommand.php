<?php

namespace App\Services\Telegram\TelegramBots\TelegramNewsBot\BotCommands;

class HelpCommand
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