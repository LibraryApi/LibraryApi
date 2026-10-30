<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Webhook\SetWebhookRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\WebhookResource;
use App\Services\Application\Webhook\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(private readonly WebhookService $webhooks)
    {
    }

    public function setWebhook(SetWebhookRequest $request): JsonResponse
    {
        $result = $this->webhooks->set($request->validated('url'));

        if (!$result['ok']) {
            return response()->json(new ErrorResource($result['error']), $result['status']);
        }

        return response()->json(new WebhookResource($result['data']));
    }

    public function LibraryApiHandler(Request $request): void
    {
        $this->webhooks->handle($request, 'api');
    }

    public function LibraryNewsHandler(Request $request): void
    {
        $this->webhooks->handle($request, 'news');
    }

    public function LibraryAdminHandler(Request $request): void
    {
        $this->webhooks->handle($request, 'admin');
    }

    public function deleteWebhook(): JsonResponse
    {
        return response()->json(new WebhookResource($this->webhooks->delete()));
    }

    public function getWebhookInfo(): JsonResponse
    {
        return response()->json(new WebhookResource($this->webhooks->info()));
    }
}
