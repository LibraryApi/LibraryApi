<b>Список книг</b>
<ul>
    @foreach ($books as $book)
        <i><b>Название:</b> {{ $book->title }}</i><br>
        <i><b>Описание:</b> {{ $book->description }}</i><br>
        <i><b>Автор:</b> {{ $book->author }}</i><br><br>
    @endforeach

</ul>
