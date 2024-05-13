<?php

namespace App\Services\Telegram\TelegramBots\TelegramApiBot\BotCommands;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Models\Book;
use App\Models\Post;
use App\Services\Telegram\TelegramBots\TelegramApiBot\TelegramApiBot;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class StartCommand extends TelegramApiBot implements TelegramCommandInterface
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
        
        $command = $this->handler->command_name;
        if (isset($command) && $command == 'books_list') {

            $books = $this->getBooks();
            $message = ['text' => $books];
            return $this->handler->buildMessage($message);
        }

        if (isset($command) && $command == 'posts_list') {

            $posts = $this->getPosts();
            $message = ['text' => $posts];
            return $this->handler->buildMessage($message);
        }
        $message = [
            'text' => 'Это твоя карманная библиотека API BOT, ты можешь отсюда просматривать и читать книги или статьи',
            "message_type" => 'buttons',
            "buttons" => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'список книг',
                            'callback_data' => json_encode(['command' => 'books_list', 'parent_command' => '/start'])
                        ],
                        [
                            'text' => 'список статей',
                            'callback_data' => json_encode(['command' => 'posts_list', 'parent_command' => '/start'])
                        ]
                    ]
                ],
            ]
        ];
        return $this->handler->buildMessage($message);
    }

    public function getBooks(): ?string
    {
        $books = Book::paginate(5);

        $booksMessage = "*Список книг*\n";
        foreach ($books as $book) {
            $booksMessage .= "\n*Nазвание:* {$book->title}\n";
            $booksMessage .= "*Автор:* {$book->author}\n";
            $booksMessage .= "*Описание:* {$book->description}\n\n";
        }

        $booksMessage .= "Страница {$books->currentPage()} из {$books->lastPage()}";

        return $booksMessage;
    }

    public function getPosts(): ?string
    {
        $posts = Post::paginate(5);

        $postsMessage = "*Список постов*\n";
        foreach ($posts as $post) {
            $postsMessage .= "\n*Nазвание:* {$post->title}\n";
            $postsMessage .= "*Описание:* {$post->content}\n\n";
        }
        return $postsMessage .= "Страница {$posts->currentPage()} из {$posts->lastPage()}";;
    }
}
