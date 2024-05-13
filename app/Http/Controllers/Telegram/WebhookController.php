<?php

namespace App\Http\Controllers\Telegram;

use App\Facades\Telegram;
use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $token;
    protected $domain;

    public function __construct()
    {
        $this->token = env('TELEGRAM_API_BOT_TOKEN');
        $this->domain = 'https://api.telegram.org/bot';
    }
    public function setWebhook(Request $request): array
    {
        $url = $request->input('url');

        $urlParts = explode('/', $url);

        $method = end($urlParts);
        if ($method == 'library_news') {
            $token = env('TELEGRAM_NEWS_BOT_TOKEN');
        }

        if ($method == 'library_api') {
            $token = env('TELEGRAM_API_BOT_TOKEN');
        }

        if ($method == 'library_admin') {
            $token = env('TELEGRAM_ADMIN_BOT_TOKEN');
        }
        $response = Http::post($this->domain . $token . '/setWebhook', [
            "url" => $url,
        ])->json();
        return $response;
    }

    public function LibraryApiHandler(Request $request)
    {
        $this->send($request, 'api');
    }

    public function LibraryNewsHandler(Request $request)
    {
        $this->send($request, 'news');
    }

    public function LibraryAdminHandler(Request $request)
    {
        $this->send($request, 'admin');
    }

    public function send(Request $request, $botType)
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
