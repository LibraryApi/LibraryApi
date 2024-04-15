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
                <h2>На сайте новый пользователь {{ $user->name }}!</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                
                <p>Использовал почту: {{ $user->email }}.</p>
        </tr>
    </table>
</body>
</html>
