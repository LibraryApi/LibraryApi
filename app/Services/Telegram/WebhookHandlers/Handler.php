<?php

namespace App\Services\Telegram\WebhookHandlers;

class Handler
{
    public $request;
    public $command_name;
    public $parent_command_name;
    protected $chat_id;
    protected $bot_config;

    public function handle(array $request, $botConfig)
    {
        $this->request = $request;
        $this->bot_config = $botConfig;

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

    protected function isMyChatMember()
    {
        if (isset($this->request['my_chat_member'])) {
            return true;
        }
        return false;
    }
    //Телеграм отправляет в обновлении состояния чата (chat update). Он отправляется, 
    //когда в чате происходят изменения в участниках, такие как добавление нового участника, изменение его 
    //статуса или выход из чата. Позже прописать функционал
    protected function handleMyChatMember($botConfig)
    {
        return new $botConfig['commands']['/help']($this->request, $this);
    }

    protected function isCallbackQuery()
    {
        if (isset($this->request['callback_query'])) {
            return true;
        }
        return false;
    }

    protected function handleCallbackQuery($botConfig)
    {
        $this->setParentCommandName();
        $command = $this->getParentCommandName();
        if (isset($botConfig['commands'][$command]))
            return new $botConfig['commands'][$command]($this->request, $this);

        return new $botConfig['commands']['/help']($this->request, $this);
    }

    protected function isCommand()
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

    protected function handleCommand($botConfig)
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
    public function setCommandName()
    {
        $this->command_name = $this->request['message']['text'];
    }

    public function setParentCommandName()
    {
        $callbackQuery = json_decode($this->request['callback_query']['data'], true);

        $this->command_name = $callbackQuery['command'];
        $this->parent_command_name = $callbackQuery['parent_command'];
    }
    public function getEntities()
    {
        $entities = $this->request['message']['entities'] ?? [];
        return $entities;
    }

    public function getEntitiesType($entities): ?string
    {
        $entitiesType = $entities[0]['type'] ?? null;
        return $entitiesType;
    }

    public function getChatId()
    {
        if (isset($this->request['message'])) {
            $chat_id = $this->request['message']['chat']['id'];
        }

        if (isset($this->request['callback_query'])) {
            $chat_id = $this->request['callback_query']['message']['chat']['id'];
        }

        return $chat_id;
    }

    public function buildMessage(array $message): array
    {
        $defaults = [
            "bot_type" => $this->bot_config['type'],
            "parse_mode" => 'html',
            "reply_id" => null,
            "chat_id" => $this->chat_id,
            "message_type" => $message['message_type'] ?? 'text'
        ];
        return array_merge($defaults, $message);
    }
}
