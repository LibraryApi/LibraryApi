<?php

namespace App\Services\Telegram\WebhookHandlers;

use App\Interfaces\Telegram\WebhookHandlerInterface;
use App\Services\Telegram\WebhookHandlers\Commands\StartCommandHandler;
use App\Services\Telegram\WebhookHandlers\Commands\HelpCommandHandler;
use App\Services\Telegram\WebhookHandlers\Commands\BooksCommandHandler;
use App\Services\Telegram\WebhookHandlers\Commands\NewsCommandsHandler;
use App\Services\Telegram\WebhookHandlers\Commands\PostsCommandHandler;
use App\Services\Telegram\WebhookHandlers\Commands\AdminCommandsHandler;
use Illuminate\Http\Request;

class WebhookHandler implements WebhookHandlerInterface
{
   protected $request;
   protected $bot_type = '';

   protected $chat_id = '';

   protected $command_name;
   protected static $commands = [
      "/start"             => StartCommandHandler::class,
      "/help"              => HelpCommandHandler::class,
      "/posts"             => PostsCommandHandler::class,
      "/books"             => BooksCommandHandler::class,
      '/today'             => NewsCommandsHandler::class,
      '/week'              => NewsCommandsHandler::class,
      '/posts_today'       => NewsCommandsHandler::class,
      '/books_today'       => NewsCommandsHandler::class,
      '/delete_post'       => AdminCommandsHandler::class,
      '/delete_book'        => AdminCommandsHandler::class

   ];
   public function __construct(Request $request, string $botType)
   {
      $this->request = $request;
      $this->bot_type = $botType;
      $this->chat_id = $this->getChatId($request);
      $this->command_name = $this->setCommandName($request);
   }

   public function createMessageHandler(): ?WebhookHandlerInterface
   {
      if ($this->request->input('my_chat_member')) {
         return $this->handle();
      }

      if ($this->request->input('callback_query')) {
         return $this->handleCallbackQuery($this->request);
      }

      $entities = $this->getEntities($this->request);

      if ($this->isCommand($entities)) {
         $this->setCommandName($this->request);

         if (isset(self::$commands[$this->command_name])) {
            return new self::$commands[$this->command_name]($this->request, $this->bot_type);
         }
      }
      return new HelpCommandHandler($this->request, $this->bot_type);
   }
   public function handle(): ?array
   {
      $message = [
         'bot_type' => $this->bot_type,
         "message_type" => 'text',
         "text" => "К сожалению я пока не умею обрабатывать такое. Введите команду /help для информации или выберите 'меню'"
      ];
      return $message;
   }

   public function handleCallbackQuery(Request $request): ?WebhookHandlerInterface
   {
      if ($request->input('callback_query')['data'] == 'books_list' || $request->input('callback_query')['data'] == 'posts_list') {
         return new self::$commands['/start']($this->request, $this->bot_type);
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

   public function getChatId(Request $request): int|string
   {
      if ($request->input("message")) {
         $chat_id = $request->input('message')['chat']['id'];
      }

      if ($request->input('callback_query')) {
         $chat_id = $request->input('callback_query')['message']['chat']['id'];
      }
      return $chat_id;
   }

   public function getCommandName(): string
   {
      return $this->command_name;
   }

   public function setCommandName(Request $request): ?string
   {

      if ($request->input("message")) {
         $command_name = explode(' ', $request->input('message')['text'])[0];
      }

      if ($request->input('callback_query')) {
         $command_name  = $request->input('callback_query')['data'];
      }
      return $command_name;
   }

   public function buildMessage(array $message): array
   {
      $defaults = [
         "bot_type" => $this->bot_type,
         "parse_mode" => 'html',
         "reply_id" => null,
         "chat_id" => $this->chat_id,
         "message_type" => $message['message_type'] ?? 'text'
      ];
      return array_merge($defaults, $message);
   }
}
