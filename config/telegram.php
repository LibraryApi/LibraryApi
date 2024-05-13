<?php

return [
    'bots' => [
        'api' => [
            'token' => env('TELEGRAM_API_BOT_TOKEN'),
            'type' => 'library_api',
        ],
        'news' => [
            'token' => env('TELEGRAM_NEWS_BOT_TOKEN'),
            'type' => 'library_news',
        ],
        'admin' => [
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'type' => 'library_admin',
        ],
    ],
];