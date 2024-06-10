<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог книг</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Каталог книг</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Автор</th>
                    <th>Цена</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$book->title}}</td>
                    <td>{{$book->user->name}}</td>
                    <td>{{$book->price}}</td>
                    <td>
                        <a href={{ route('books.pay') }} class="btn btn-primary">Купить</a>
                    </td>
                </tr>
                <!-- Другие книги -->
            </tbody>
        </table>
    </div>
</body>
</html>
