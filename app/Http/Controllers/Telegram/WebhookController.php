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

    public function send(Request $request, string $botType): \Illuminate\Http\JsonResponse
    {
        Log::info('Webhook data:', $request->all());

        $telegramBotFactory = new TelegramBotFactory();
        $webhookHandler = $telegramBotFactory->createWebhookHandler($request, $botType);
        $webhookSender = $telegramBotFactory->createWebhookSender();

        $messageHandler = $webhookHandler->createMessageHandler();
        if (is_array($messageHandler)) {
            return response()->json(['status' => 'ok']);
        }
        $message = $messageHandler->handle();
        $messageType = $message['message_type'];

        $messageSendler = $webhookSender->createMessageSender($messageType);
        $messageSendler->message($message)->sendMessage();

        return response()->json(['status' => 'ok']);
    }
    public function LibraryApiHandler(Request $request): \Illuminate\Http\JsonResponse
    {
        $botType = 'LibraryApiBot';
        return $this->send($request, $botType);
    }

    public function LibraryNewsHandler(Request $request): \Illuminate\Http\JsonResponse
    {
        $botType = 'LibraryNewsBot';
        return $this->send($request, $botType);
    }

    public function LibraryAdminHandler(Request $request): \Illuminate\Http\JsonResponse
    {

        $botType = 'LibraryAdminBot';
        return $this->send($request, $botType);

        /* $data = $request->all();
        $botType = 'LibraryAdminBot';
        Log::info('Webhook data:', $data);
        Telegram::setToken($botType);

        $handlerClass = Telegram::handle($request);

        if ($handlerClass) {

            $handlerInstance = new $handlerClass($request);
            $handlerInstance->setBotType($botType);
            $handlerInstance->sendMessage();
        }

        return response()->json(['status' => 'ok']); */
    }
}
