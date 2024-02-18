<?php

namespace App\Http\Controllers\Telegram;

use App\Facades\Telegram;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $token;
    protected $domain;
    public function __construct()
    {
        $this->token = env('TELEGRAM_ADMIN_BOT_TOKEN');
        $this->domain = 'https://api.telegram.org/bot';
    }
    public function setWebhook(Request $request): array
    {
        $url = $request->input('url');

        $response = Http::post($this->domain . $this->token . '/setWebhook', [
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

    public function LibraryApiHandler(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = $request->all();
        $botType = 'LibraryApiBot';
        Log::info('Webhook data:', $data);
        Telegram::setToken($botType);

        $handlerClass = Telegram::handle($request);

        if ($handlerClass) {

            $handlerInstance = new $handlerClass($request);
            $handlerInstance->setBotType($botType);
            $handlerInstance->sendMessage();
        }
        return response()->json(['status' => 'ok']);
    }

    public function LibraryNewsHandler(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = $request->all();
        $botType = 'LibraryNewsBot';
        Log::info('Webhook data:', $data);
        Telegram::setToken($botType);

        $handlerClass = Telegram::handle($request);

        if ($handlerClass) {

            $handlerInstance = new $handlerClass($request);
            $handlerInstance->setBotType($botType);
            $handlerInstance->sendMessage();
        }

        return response()->json(['status' => 'ok']);
    }

    public function LibraryAdminHandler(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = $request->all();
        $botType = 'LibraryAdminBot';
        Log::info('Webhook data:', $data);
        Telegram::setToken($botType);

        $handlerClass = Telegram::handle($request);

        if ($handlerClass) {

            $handlerInstance = new $handlerClass($request);
            $handlerInstance->setBotType($botType);
            $handlerInstance->sendMessage();
        }

        return response()->json(['status' => 'ok']);
    }
}
