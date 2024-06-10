<?php

return [
    'shop_id' => env('YOOKASSA_SHOP_ID'),
    'secret_key' => env('YOOKASSA_SECRET_KEY'),
    'return_url' => env('YOOKASSA_RETURN_URL', 'https://d84d-212-94-29-97.ngrok-free.app/about'), // URL для возврата после оплаты
];
