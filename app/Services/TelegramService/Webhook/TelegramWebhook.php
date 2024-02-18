<?php

namespace App\Services\TelegramService\Webhook;

use App\Facades\Telegram;
use App\Services\TelegramService\Webhook\Commands\Admin;
use App\Services\TelegramService\Webhook\Commands\BooksToday;
use App\Services\TelegramService\Webhook\Commands\Posts;
use App\Services\TelegramService\Webhook\Commands\PostsToday;
use App\Services\TelegramService\Webhook\Commands\Start;
use App\Services\TelegramService\Webhook\Commands\Today;
use App\Services\TelegramService\Webhook\Commands\Week;
use Illuminate\Http\Request;

class TelegramWebhook
{
    public $chat_id = 6109443752;
    public $bot_type = "";

    protected static $commands = [
        '/start'            => Start::class,
        '/books'            => BooksToday::class,
        '/posts'            => Posts::class,
        '/today'            => Today::class,
        '/week'             => Week::class,
        '/posts_today'      => PostsToday::class,
        '/books_today'      => BooksToday::class,
        '/delete_post'       => Admin::class,
        '/delete_book'       => Admin::class
    ];

    public function handle(Request $request): ?string
    {
        if($request->input('my_chat_member')){
            return null;
        }
        if ($request->input('callback_query')) {
            return $this->handleCallbackQuery($request);
        }

        $entities = $this->getEntities($request);

        if ($this->isCommand($entities)) {
            $command_name = $this->getCommandName($request);

            if (isset(self::$commands[$command_name])) {
                return self::$commands[$command_name];
            } else {
                Telegram::message(6109443752, "Неизвестная команда: $command_name")->sendMessage();
            }
        } else {
            Telegram::message(6109443752, "Я умею работать только с командами. Отправьте команду")->sendMessage();
        }
        return null;
    }

    public function handleCallbackQuery(Request $request): ?string
    {
        if ($request->input('callback_query')['data'] == 'books_list' || $request->input('callback_query')['data'] == 'posts_list') {
            return self::$commands['/start'];
        }
    }
    public function isCommand($entities): bool
    {
        if (!empty($entities)) {
            $entitiesType = $this->getEntitiesType($entities);
            if ($entitiesType === 'bot_command') {
                return true;
            }
        }
        return false;
    }

    public function getCommandName(Request $request): ?string
    {
        $command_name = explode(' ', $request->input('message')['text'])[0];
        return $command_name;
    }

    public function getEntities(Request $request): ?array
    {
        $entities = $request->input('message')['entities'] ?? [];
        return $entities;
    }

    public function getEntitiesType($entities): ?string
    {
        $entitiesType = $entities[0]['type'] ?? null;
        return $entitiesType;
    }

    public function setBotType($botType): void
    {
        $this->bot_type = $botType;
        return;
    }
}
