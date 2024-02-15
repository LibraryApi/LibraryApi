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
        $this->token = env('TELEGRAM_BOT_TOKEN');
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

    public function webhookHandler(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = $request->all();

        Log::info('Webhook data:', $data);

        $handlerClass = Telegram::handle($request);

        if ($handlerClass) {

            $handlerInstance = new $handlerClass($request);

            $handlerInstance->sendMessage();
        }

        return response()->json(['status' => 'ok']);
    }
}
