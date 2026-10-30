<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedResource;
use App\Services\Application\Feed\FeedService;
use Illuminate\Http\JsonResponse;

class FeedController extends Controller
{
    public function __construct(private readonly FeedService $feedService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(new FeedResource($this->feedService->get(auth()->user())));
    }
}
