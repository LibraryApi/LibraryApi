<?php

namespace App\Services\Telegram\TelegramBots\TelegramAdminBot\BotCommands;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Models\Book;
use App\Models\Post;
use App\Services\Telegram\TelegramBots\TelegramAdminBot\TelegramAdminBot;

class StartCommand extends TelegramAdminBot implements TelegramCommandInterface
{
    public $request;
    public $handler;
    public function __construct($request, $handler)
    {
        $this->request = $request;
        $this->handler = $handler;
    }
    public function buildMessage()
    {
        $callback = $this->handler->getCallbackQuery();

        if (isset($callback['command']) && $callback['command'] == 'books_list') {

            return $this->buildBooksMessage();
        }

        if (isset($callback['command']) && $callback['command'] == 'posts_list') {

            return $this->buildPostsMessage();
        }

        return $this->buildStartMessage();
    }
    public function getBooks()
    {
        $books = Book::paginate(5);

        $booksMessage = $this->formatBooksMessage($books);
        $keyboard = $this->generateBooksKeyboard($books);

        $booksMessage .= "Страница {$books->currentPage()} из {$books->lastPage()}";
        $message = ["books" => $booksMessage, 'keyboard' => $keyboard];
        return $message;
    }

    public function formatBooksMessage($books)
    {
        $booksMessage = "<b>Список книг</b>\n\n";
        foreach ($books as $book) {
            $booksMessage .= "<b>Название:</b>{$book->title}\n";
            $booksMessage .= "<b>Автор:</b> {$book->author}\n";
            $booksMessage .= "<a href=\"http://www.example.com\"><b>ссылка для скачивания</b></a>\n\n";
        }
        return $booksMessage;
    }

    public function generateBooksKeyboard($books)
    {
        $keyboard = ['inline_keyboard' => []];
        foreach ($books as $book) {
            $callbackData = json_encode(['book_id' => $book->id, 'command' => 'books_list', 'parent_command' => '/start']);
            $keyboard['inline_keyboard'][] = [
                [
                    'text' => $book->title,
                    'callback_data' => $callbackData,
                ],
            ];
        }
        return $keyboard;
    }
    public function getPosts(): ?string
    {
        $posts = Post::paginate(5);

        $postsMessage = "<b>Список постов</b>\n\n";
        foreach ($posts as $post) {
            $pd = $post;
            $postsMessage .= "<b>Название:</b> {$post->title}\n";
            $postsMessage .= "<b>Автор:</b> {$post->user->name}\n";
            $postsMessage .= "<a href=\"http://www.example.com\"><b>ссылка для скачивания</b></a>\n\n";
        }
        return $postsMessage .= "Страница {$posts->currentPage()} из {$posts->lastPage()}";;
    }
    public function buildStartMessage()
    {
        $test = $this->handler->keyboard_handler->buildButtons();
        $buttons = [
            [
                ['text' => 'список книг', 'callback_data' => ['command' => 'books_list', 'parent_command' => '/start']],
                ['text' => 'список статей', 'callback_data' => ['command' => 'posts_list', 'parent_command' => '/start']]
            ]
        ];
        $message = [
            'text' => 'Это твоя карманная библиотека, ты можешь отсюда просматривать и читать книги или статьи',
            "message_type" => 'buttons',
            "buttons" => $this->handler->keyboard_handler->buildInlineKeyboard($buttons),
        ];

        return $this->handler->buildMessage($message);
    }
    public function buildBooksMessage()
    {
        $books = $this->getBooks();
        $message = [
            'text' => $books['books'],
            "message_type" => 'buttons',
            'buttons' => $books['keyboard'],
        ];
        return $this->handler->buildMessage($message);
    }
    public function buildPostsMessage()
    {
        $posts = $this->getPosts();
        $message = ['text' => $posts];
        return $this->handler->buildMessage($message);
    }
}
