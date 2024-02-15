<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function setWebhook(Request $request)
    {
        $url = $request->input('url');

        $response = Http::post('https://api.telegram.org/bot' . env('LIBRARY_API_BOT_TOKEN') . '/setWebhook', [
            "url" => $url,
        ])->json();
        return $response;
    }

    public function deleteWebhook()
    {
        $url = 'https://api.telegram.org/bot' . env('LIBRARY_API_BOT_TOKEN') . '/deleteWebhook';
        $response = Http::post($url, [])->json();
        return $response;
    }

    public function getWebhookInfo()
    {
        $url = 'https://api.telegram.org/bot' . env('LIBRARY_API_BOT_TOKEN') . '/getWebhookInfo';
        $response = Http::post($url, [])->json();
        return $response;
    }

    public function webhookHandler(Request $request)
    {
        // Получите данные из входящего запроса

        $data = $request->all();
        // Запись данных в лог
        Log::info('Webhook data:', $request->all());
            // Проверяем, что есть сообщение
            if (isset($data['message']['text'])) {
                // Получаем текст сообщения
                $messageText = strtolower($data['message']['text']);
    
                // Проверяем, содержит ли сообщение слово "привет"
                if (strpos($messageText, 'привет') !== false) {
                    // Отправляем ответное сообщение
                    $chatID = $data['message']['chat']['id'];
                    $this->sendResponse($chatID, 'Привет! Как дела?');
                }
            }
    
            // Верните ответ Telegram, например, 200 OK
            return response()->json(['status' => 'ok']);
        }
    
        // Функция для отправки ответного сообщения
        private function sendResponse($chatID, $text)
        {
            $botToken = env('LIBRARY_API_BOT_TOKEN');
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
            Http::post($url, [
                "chat_id" => 6109443752,
                "text" => $text,
            ]);
        }
}
