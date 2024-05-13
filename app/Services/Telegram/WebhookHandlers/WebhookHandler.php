<?php

namespace App\Services\Telegram\WebhookHandlers;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Interfaces\Telegram\WebhookHandler\WebhookHandlerInterface;
use App\Services\Telegram\WebhookHandlers\Keyboards\KeyboardsHandler;

class WebhookHandler implements WebhookHandlerInterface
{
    public array $request;
    public string $command_name;
    public ?string $parent_command_name;
    public array $callback_query = [];
    protected ?int $chat_id = 0;
    protected array $bot_config;
    public KeyboardsHandler $keyboard_handler;

    public function __construct(KeyboardsHandler $keyboardHandler)
    {
        $this->keyboard_handler = $keyboardHandler;
    }
    public function handle(array $request, array $botConfig): TelegramCommandInterface
    {
        $this->request = $request;
        $this->bot_config = $botConfig;
        $this->setChatId();

        if ($this->isMyChatMember()) {
            return $this->handleMyChatMember($botConfig);
        }

        if ($this->isCommand()) {
            return $this->handleCommand($botConfig);
        }
        if ($this->isCallbackQuery()) {
            return $this->handleCallbackQuery($botConfig);
        }
        // По умолчанию, возвращаем обработчик команды помощи
        return new $botConfig['commands']['/help']($this->request, $this);
    }

    protected function isMyChatMember(): bool
    {
        if (isset($this->request['my_chat_member'])) {
            return true;
        }
        return false;
    }
    //Телеграм отправляет в обновлении состояния чата (chat update). Он отправляется, 
    //когда в чате происходят изменения в участниках, такие как добавление нового участника, изменение его 
    //статуса или выход из чата. Позже прописать функционал
    protected function handleMyChatMember(array $botConfig): TelegramCommandInterface
    {
        return new $botConfig['commands']['/help']($this->request, $this);
    }

    protected function isCallbackQuery(): bool
    {
        if (isset($this->request['callback_query'])) {
            return true;
        }
        return false;
    }

    protected function handleCallbackQuery(array $botConfig): TelegramCommandInterface
    {
        $this->setParentCommandName();
        $this->setCallbackQuery();

        $command = $this->getParentCommandName();
        if (isset($botConfig['commands'][$command]))
            return new $botConfig['commands'][$command]($this->request, $this);

        return new $botConfig['commands']['/help']($this->request, $this);
    }

    protected function isCommand(): bool
    {
        $entities = $this->getEntities();
        if (!empty($entities)) {
            $entitiesType = $this->getEntitiesType($entities);
            if ($entitiesType === 'bot_command') {
                return true;
            }
        }
        return false;
    }

    protected function handleCommand(array $botConfig): TelegramCommandInterface
    {
        $this->setCommandName();
        $command = $this->getCommandName();
        if (isset($botConfig['commands'][$command])) {
            return new $botConfig['commands'][$command]($this->request, $this);
        }
        return new $botConfig['commands']['/help']($this->request, $this);
    }

    public function getCommandName(): string
    {
        return $this->command_name;
    }

    public function getParentCommandName(): string
    {
        return $this->parent_command_name;
    }
    public function setCommandName(): void
    {
        $this->command_name = $this->request['message']['text'];
    }

    public function setParentCommandName(): void
    {
        $callbackQuery = json_decode($this->request['callback_query']['data'], true);

        $this->command_name = $callbackQuery['command'];
        $this->parent_command_name = $callbackQuery['parent_command'];
    }
    public function getEntities(): mixed
    {
        $entities = $this->request['message']['entities'] ?? [];
        return $entities;
    }

    public function getEntitiesType(mixed $entities): ?string
    {
        $entitiesType = $entities[0]['type'] ?? null;
        return $entitiesType;
    }

    public function setChatId(): void
    {
        if (isset($this->request['message'])) {
            $this->chat_id = $this->request['message']['chat']['id'];
        }

        if (isset($this->request['callback_query'])) {
            $this->chat_id = $this->request['callback_query']['message']['chat']['id'];
        }
    }

    public function getChatId(): int
    {
        return $this->chat_id;
    }
    public function setCallbackQuery(): void
    {
        $this->callback_query = json_decode($this->request['callback_query']['data'], true);
    }

    public function getCallbackQuery(): array
    {
        return $this->callback_query;
    }
    public function buildMessage(array $message): array
    {
        $defaults = [
            "bot_type" => $this->bot_config['type'],
            "parse_mode" => 'html',
            "reply_id" => null,
            "chat_id" => $this->getChatId(),
            "message_type" => $message['message_type'] ?? 'text'
        ];
        return array_merge($defaults, $message);
    }
}
