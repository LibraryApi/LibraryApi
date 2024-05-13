<?php

namespace App\Services\Telegram\WebhookHandlers\Keyboards;

use App\Interfaces\Telegram\TelegramBot\Command\TelegramCommandInterface;
use App\Interfaces\Telegram\WebhookHandler\WebhookHandlerInterface;
use App\Services\Telegram\WebhookHandlers\WebhookHandler;

class KeyboardsHandler
{
    public function buildInlineKeyboard(array $buttons)
    {
        $inlineKeyboard = ['inline_keyboard' => []];
        foreach ($buttons as $row) {
            $buttonRow = [];
            foreach ($row as $button) {
                $buttonRow[] = [
                    'text' => $button['text'],
                    'callback_data' => json_encode($button['callback_data']),
                ];
            }
            $inlineKeyboard['inline_keyboard'][] = $buttonRow;
        }
        return $inlineKeyboard;
    }

    public function buildButtons()
    {
        return 'yes';
    }
}
