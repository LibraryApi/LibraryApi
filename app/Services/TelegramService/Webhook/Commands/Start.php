<?php

namespace App\Services\TelegramService\Webhook\Commands;

use App\Facades\Telegram;
use App\Models\Book;
use App\Models\Post;
use App\Services\TelegramService\Webhook\TelegramWebhook;
use Illuminate\Http\Request;

class Start extends TelegramWebhook
{

  public $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function sendMessage()
  {

    if($this->bot_type == 'LibraryApiBot'){
      return $this->libraryApiHandle();
    }

    if($this->bot_type == 'LibraryNewsBot'){
      return $this->libraryNewsHandle();
    }

    if($this->bot_type == 'LibraryAdminBot'){
      return $this->libraryAdminHandle();
    }
    return null;
  }
  public function libraryApiHandle()
  {
    $callbackQuery = $this->request->input('callback_query');
    if ($callbackQuery && isset($callbackQuery['data']) && $callbackQuery['data'] == 'books_list') {
      $books = $this->getBooks();
      return Telegram::message($this->chat_id, $books, 'markdown')->sendMessage();
    }

    if ($callbackQuery && isset($callbackQuery['data']) && $callbackQuery['data'] == 'posts_list') {
      $posts = $this->getPosts();
      return Telegram::message($this->chat_id, $posts, 'markdown')->sendMessage();
    }

    Telegram::message($this->chat_id, 'Привет! Спасибо что подписался')->sendMessage();
    return Telegram::buttons($this->chat_id, 'Это твоя карманная библиотека, ты можешь отсюда просматривать и читать книги или статьи', [
      'inline_keyboard' => [
        [
          [
            'text' => 'список книг',
            'callback_data' => 'books_list'
          ],
          [
            'text' => 'список статей',
            'callback_data' => 'posts_list'
          ]
        ]
      ],
    ])->sendMessage();
  }

  public function libraryNewsHandle()
  {
    Telegram::message($this->chat_id, 'Привет! Я пока не подготовил ответ')->sendMessage();
  }

  public function libraryAdminHandle(){
    Telegram::message($this->chat_id, 'Привет! Скоро тут будут админские фишки!')->sendMessage();
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
