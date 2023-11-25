<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSS example</title>
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
</head>
<body>
<section class="section">
    <div class="container">
        <div class="columns">
            <div class="column">
                <div class="card">
                    <div class="header">
                        <h1>1</h1>
                    </div>
                    <div class="container">
                        <p>April 30, 2023</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>