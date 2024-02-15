<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;

class Handler extends ExceptionHandler
{
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
           
            $text = (string)view('telegram.error', ['e' => $e]);
            $url = 'https://api.telegram.org/bot6790010215:AAEHj3dG7674TW7pKmjmZdeVOWDFE-qEEtQ/sendMessage';
            Http::post($url, [
                "chat_id" => 6109443752,
                "text" => $text,
                "parse_mode" => 'html'
            ])->json();
        });
    }
}
