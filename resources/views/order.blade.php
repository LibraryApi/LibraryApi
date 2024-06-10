<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение покупки</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Подтверждение покупки</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{$book->title}}</h5>
                <p class="card-text">Автор: {{$book->user->name}}</p>
                <p class="card-text">Цена: {{$book->price}} руб.</p>
                <form method="POST" action="{{ route('book.pay', $book->id) }}" accept-charset="UTF-8" class="w-50">
                    @csrf
                    <!-- Здесь могут быть дополнительные поля для данных пользователя и оплаты -->
                    <input type="hidden" name="bookId" value="{{$book->id}}">
                    <input type="hidden" name="title" value="{{$book->title}}">
                    <input type="hidden" name="author" value="{{$book->user->name}}">
                    <input type="hidden" name="price" value="{{$book->price}}">
                    <input type="hidden" name="userId" value="{{ auth()->user() }}">
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input class="form-control" name="name" type="text" id="name" placeholder="Введите ваше имя">
                    </div>
                    <button type="submit" class="btn btn-success">Подтвердить покупку</button>
                </form>
            </div>
        </div>
    </div>
</body>


</html>
