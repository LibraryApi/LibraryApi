<?php

namespace App\Services\TelegramService\Webhook;

use App\Facades\Telegram;
use App\Services\TelegramService\Webhook\Commands\Start;
use Illuminate\Http\Request;

class TelegramWebhook
{
    protected $request;
    public $chat_id = 6109443752;

    protected static $commands = [
        '/start' => Start::class,
        //'/edit_profile' => EditProfile::class,
    ];

    public function handle(Request $request): ?string
    {
        $entities = $request->input('message')['entities'] ?? [];

        if($request->input('callback_query'))
        {
            $data = json_decode($request->input('callback_query')['data']);
            return self::$commands['/start'];
        }

        if (!empty($entities)) {
            $entitiesType = $entities[0]['type'] ?? null;

            if ($entitiesType === 'bot_command') {
                $command_name = explode(' ', $request->input('message')['text'])[0];

                if (isset(self::$commands[$command_name])) {
                    return self::$commands[$command_name];
                } else {
                    Telegram::message(6109443752, "Неизвестная команда: $command_name")->sendMessage();
                } 
            } else {
                Telegram::message(6109443752, "Тип не является 'bot_command'")->sendMessage();
            }
        } else {
            Telegram::message(6109443752, "Я не знаю как вам ответить")->sendMessage();
        }

        return null;
    }
}
