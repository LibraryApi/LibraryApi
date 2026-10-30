<?php

namespace App\Services\Application\Webhook;

use App\Services\WrapperServices\Telegram\TelegramBotFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    private string $domain = 'https://api.telegram.org/bot';

    public function set(string $url): array
    {
        $method = basename(parse_url($url, PHP_URL_PATH) ?: '');
        $botConfig = config("telegram.bots.{$method}");

        if (!$botConfig) {
            return ['ok' => false, 'status' => 400, 'error' => 'Неверное имя бота'];
        }

        $response = Http::post($this->domain . $botConfig['token'] . '/setWebhook', ['url' => $url]);

        if ($response->failed()) {
            return ['ok' => false, 'status' => $response->status(), 'error' => 'Не удалось установить вебхук'];
        }

        $data = $response->json();

        if (!($data['ok'] ?? false)) {
            return ['ok' => false, 'status' => 500, 'error' => 'Ошибка API Telegram: ' . ($data['description'] ?? '')];
        }

        Log::info('Вебхук успешно установлен для бота: ' . $method);

        return ['ok' => true, 'data' => $data];
    }

    public function delete(): array
    {
        return Http::post($this->domain . env('TELEGRAM_API_BOT_TOKEN') . '/deleteWebhook', [])->json() ?? [];
    }

    public function info(): array
    {
        return Http::post($this->domain . env('TELEGRAM_API_BOT_TOKEN') . '/getWebhookInfo', [])->json() ?? [];
    }

    public function handle(Request $request, string $botType): void
    {
        Log::info('Webhook data:', $request->all());

        $telegram = (new TelegramBotFactory())->createTelegramBot($botType);
        $message = $telegram->handle($request)->buildMessage();
        $telegram->sendMessage($message);
    }
}
