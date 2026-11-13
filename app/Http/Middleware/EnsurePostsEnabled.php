<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePostsEnabled
{
    /**
     * Posts are temporarily unavailable to regular users. Administrators keep
     * access so existing content can still be managed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('features.posts_enabled')) {
            return $next($request);
        }

        $user = $request->user('sanctum') ?? $request->user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Раздел постов временно недоступен.',
            'code' => 'posts_disabled',
        ], Response::HTTP_FORBIDDEN);
    }
}
