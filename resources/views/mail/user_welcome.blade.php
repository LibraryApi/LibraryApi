<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приветственное письмо</title>
</head>
<body>
    <table style="width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border-collapse: collapse; border: 1px solid #ccc;">
        <tr>
            <td style="padding: 20px; background-color: #f8f8f8; text-align: center;">
                <h2>Добро пожаловать на наш сайт, {{ $user->name }}!</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Мы рады приветствовать вас на нашем сайте! Спасибо за регистрацию.</p>
                <p>Теперь вы можете войти на сайт, используя ваш адрес электронной почты: {{ $user->email }}.</p>
                <p>Если у вас возникнут вопросы или вам потребуется помощь, не стесняйтесь обращаться к нам.</p>
                <p>С наилучшими пожеланиями,</p>
                <p>Команда нашего сайта</p>
            </td>
        </tr>
    </table>
</body>
</html>
