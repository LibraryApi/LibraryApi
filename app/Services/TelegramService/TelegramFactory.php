<?php

namespace App\Services\TelegramService;

use App\Services\TelegramService\TelegramBot\TelegramFile;
use App\Services\TelegramService\TelegramBot\TelegramMessage;
use App\Services\TelegramService\Webhook\TelegramWebhook;

class TelegramFactory
{

    private TelegramMessage $message;
    private TelegramFile $file;
    private TelegramWebhook $handle;

    public function __construct()
    {
        $this->message = new TelegramMessage();
        $this->file = new TelegramFile();
        $this->handle = new TelegramWebhook();
    }
    /* 
        после вызова любого метода через фасад Telegram вызываемый метод попадает сюда
        тут определяется метод и создается экзмепляр этого класса
        что позволяет дальше работать с эти объектом 
    */
    public function __call(string $name, array $arguments)
    {

        foreach ($this as $key => $prop)
        {
            if(method_exists($this->$key, $name))
            {
                return call_user_func_array([$this->$key, $name], $arguments);
            }
        }
        throw new \Exception('Такого метода '.$name.' не нашлось');
    }
}