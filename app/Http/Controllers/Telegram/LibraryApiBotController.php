<?php

namespace App\Http\Controllers\Telegram;

use App\Facades\Telegram;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LibraryApiBotController extends Controller
{
    private $bot;
    private $chatID = 6109443752;
    const DOMAIN = 'https://api.telegram.org/';

    public function __construct()
    {
        $this->bot = env('TELEGRAM_BOT_TOKEN');
    }

    public function getBooks(){
        $books = Book::all();
        return view('telegram.books', ['books' => $books]);
    }
}
