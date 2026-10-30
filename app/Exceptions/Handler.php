<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $token = env('TELEGRAM_ADMIN_BOT_TOKEN');
            if (!$token) {
                return;
            }
            $text = (string)view('telegram.error', ['e' => $e]);
            $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
            Http::timeout(3)->post($url, [
                "chat_id" => 6109443752,
                "text" => $text,
                "parse_mode" => 'html'
            ])->json();
        });
    }
}
