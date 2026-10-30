<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interaction\RateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\InteractionResource;
use App\Services\Application\Interaction\InteractionService;
use Illuminate\Http\JsonResponse;

class InteractionController extends Controller
{
    public function __construct(private readonly InteractionService $interactionService)
    {
    }

    public function like(string $type, int $id): JsonResponse
    {
        $result = $this->interactionService->like(auth()->id(), $type, $id);

        if (!$result) {
            return response()->json(new ErrorResource('Объект не найден'), 404);
        }

        return response()->json(
            new InteractionResource($result['data']),
            $result['created'] ? 201 : 200
        );
    }

    public function unlike(string $type, int $id): JsonResponse
    {
        $result = $this->interactionService->unlike(auth()->id(), $type, $id);

        if (!$result) {
            return response()->json(new ErrorResource('Объект не найден'), 404);
        }

        return response()->json(new InteractionResource($result));
    }

    public function rate(RateRequest $request, string $type, int $id): JsonResponse
    {
        $result = $this->interactionService->rate(auth()->id(), $type, $id, $request->integer('value'));

        if (!$result) {
            return response()->json(new ErrorResource('Объект не найден'), 404);
        }

        return response()->json(new InteractionResource($result));
    }

    public function unrated(string $type, int $id): JsonResponse
    {
        $result = $this->interactionService->unrate(auth()->id(), $type, $id);

        if (!$result) {
            return response()->json(new ErrorResource('Объект не найден'), 404);
        }

        return response()->json(new InteractionResource($result));
    }
}
