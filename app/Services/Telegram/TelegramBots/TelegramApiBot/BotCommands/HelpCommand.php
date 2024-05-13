<?php

namespace App\Services\Telegram\TelegramBots\TelegramApiBot\BotCommands;

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
            'text' => "Я пока не умею отвечать на такое 'api bot"
        ];
        return $this->handler->buildMessage($message);
    }
}