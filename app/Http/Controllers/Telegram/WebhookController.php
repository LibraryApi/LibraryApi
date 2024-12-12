<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\WrapperServices\Telegram\TelegramBotFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    protected $token;
    protected $domain;

    public function __construct()
    {
        $this->token = env('TELEGRAM_API_BOT_TOKEN');
        $this->domain = 'https://api.telegram.org/bot';
    }


    public function setWebhook(Request $request)
    {
        $url = $request->input('url');
        if (empty($url)) {
            return response()->json(['error' => 'Не указан URL'], 400);
        }

        $urlParts = explode('/', $url);
        $method = end($urlParts);

        $botConfig = config("telegram.bots.{$method}");

        if ($botConfig == null) {
            return response()->json(['error' => 'Неверное имя бота'], 400);
        }

        $token = $botConfig['token'];
        $webhookUrl = $this->domain . $token . '/setWebhook';

        $response = Http::post($webhookUrl, [
            "url" => $url,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Не удалось установить вебхук'], $response->status());
        }

        $responseData = $response->json();

        if (!$responseData['ok']) {
            return response()->json(['error' => 'Ошибка API Telegram: ' . $responseData['description']], 500);
        }

        Log::info('Вебхук успешно установлен для бота: ' . $method);

        return $response->json();
    }

    public function LibraryApiHandler(Request $request): void
    {
        $this->send($request, 'api');
    }

    public function LibraryNewsHandler(Request $request): void
    {
        $this->send($request, 'news');
    }

    public function LibraryAdminHandler(Request $request): void
    {
        $this->send($request, 'admin');
    }

    public function send(Request $request, string $botType): void
    {
        Log::info('Webhook data:', $request->all());
        $telegramBotFactory = new TelegramBotFactory();

        $telegram = $telegramBotFactory->createTelegramBot($botType);
        $message = $telegram->handle($request)->buildMessage();

        $telegram->sendMessage($message);
    }

    public function deleteWebhook(): array
    {
        $url = $this->domain . $this->token . '/deleteWebhook';
        $response = Http::post($url, [])->json();
        return $response;
    }

    public function getWebhookInfo(): array
    {
        $url = $this->domain . $this->token . '/getWebhookInfo';
        $response = Http::post($url, [])->json();
        return $response;
    }
}
