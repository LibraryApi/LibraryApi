<?php

namespace App\Services\Telegram\TelegramBots\TelegramAdminBot\BotCommands;

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
            'text' => "Я пока не умею отвечать на такое"
        ];
        return $this->handler->buildMessage($message);
    }
}