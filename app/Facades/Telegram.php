<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static TelegramMessage message(mixed $chat_id, string $text, $reply_id = null)
 * @method static TelegramMessage editMessage(mixed $chat_id, string $text, int $message_id)
 * @method static TelegramMessage buttons(mixed $chat_id, string $text, array $buttons)
 * @method static TelegramMessage editButtons(mixed $chat_id, string $text, array $buttons, int $message_id)
 *
 * @method static TelegramFile document(mixed $chat_id, $file, string $filename, $reply_id = null)
 * @method static TelegramFile getFile(string $file_id)
 * @method static TelegramFile photo(mixed $chat_id, $file, $reply_id = null)
 * @method static TelegramFile album(mixed $chat_id, array $file_url, $reply_id = null)
 * @method TelegramBot send(string $token)
 */

class Telegram extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Telegram::class;
    }
}