<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\FollowResource;
use App\Services\Application\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorSubscriptionController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        return response()->json(new AuthorCollection(
            $this->userService->authors(100, $search)
        ));
    }

    public function subscriptions(Request $request): JsonResponse
    {
        return response()->json(new AuthorCollection(
            $this->userService->subscribedAuthors($request->user(), 100)
        ));
    }

    public function subscribe(Request $request, int $id): JsonResponse
    {
        $author = $this->userService->find($id);

        if (!$author) {
            return response()->json(new ErrorResource('Автор не найден'), Response::HTTP_NOT_FOUND);
        }

        $result = $this->userService->follow($request->user(), $author);

        if (!$result['ok']) {
            return response()->json(new ErrorResource($result['error']), $result['status']);
        }

        return response()->json(new AuthorResource(
            $author->fresh()->loadCount(['books', 'followers'])->load(['books' => fn ($query) => $query->latest()])
        ));
    }

    public function unsubscribe(Request $request, int $id): JsonResponse
    {
        $author = $this->userService->find($id);

        if (!$author) {
            return response()->json(new ErrorResource('Автор не найден'), Response::HTTP_NOT_FOUND);
        }

        $result = $this->userService->unfollow($request->user(), $author);

        return response()->json(new FollowResource($result['data']));
    }
}
