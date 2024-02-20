<?php

namespace App\Services\Telegram\WebhookHandlers\Commands;

use App\Models\Book;
use App\Models\Post;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;
use Illuminate\Http\Request;

class StartCommandHandler extends WebhookHandler
{
   public function __construct(Request $request, string $botType)
   {
      parent::__construct($request, $botType);
   }
   public function handle(): array
   {

      if ($this->bot_type == 'LibraryApiBot') {
         $message = $this->libraryApiHandle();
      }

      if ($this->bot_type == 'LibraryNewsBot') {
         $message = $this->libraryNewsHandle();
      }

      if ($this->bot_type == 'LibraryAdminBot') {
         $message = $this->libraryAdminHandle();
      }

      return $message;
   }

   public function libraryNewsHandle()
   {
      $message = ["text" => "Добро пожаловать! Здесь вы можете получать новости с сайта. А также запрашивать самостоятельно! Используйте команду /help для большей информации"];
      return $this->buildMessage($message);
   }

   public function libraryAdminHandle()
   {
      $message = ["text" => "Добро пожаловать! Это админ бот. Используйте команду /help для большей информации"];
      return $this->buildMessage($message);
   }
   public function libraryApiHandle()
   {
      $callbackQuery = $this->request->input('callback_query');
      if ($callbackQuery && isset($callbackQuery['data']) && $callbackQuery['data'] == 'books_list') {

         $books = $this->getBooks();
         $message = ['text' => $books];
         return $this->buildMessage($message);
      }

      if ($callbackQuery && isset($callbackQuery['data']) && $callbackQuery['data'] == 'posts_list') {

         $posts = $this->getPosts();
         $message = ['text' => $posts];
         return $this->buildMessage($message);
      }
      $message = [
         'text' => 'Это твоя карманная библиотека, ты можешь отсюда просматривать и читать книги или статьи',
         "message_type" => 'buttons',
         "buttons" => [
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
         ]
      ];
      return $this->buildMessage($message);
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
