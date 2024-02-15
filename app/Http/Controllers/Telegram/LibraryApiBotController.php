<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LibraryApiBotController extends Controller
{
    private $bot;
    private $chatID = 6109443752;
    const DOMAIN = 'https://api.telegram.org/';

    public function __construct()
    {
        $this->bot = env('LIBRARY_API_BOT_TOKEN');
    }

    public function sendMessage()
    {
            $url = self::DOMAIN . 'bot' . $this->bot . '/sendMessage';
            Http::post($url, [
                "chat_id" => $this->chatID,
                "text" => "hellosds",
                "parse_mode" => 'html'
            ]);
    }
}
