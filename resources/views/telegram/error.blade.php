<b>Новая ошибка</b>

<b>Текст:</b> <i>{{$e->getMessage()}}</i>

<b>Файл:</b> <i>{{$e->getFile()}}</i>

<b>Строка:</b> <i>{{$e->getLine()}}</i>

@if(Auth::user())
Пользователь: {{auth()->user()->name()}}
@endif
