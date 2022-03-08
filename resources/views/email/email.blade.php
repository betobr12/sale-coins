<!DOCTYPE html>
<html>
<head>
    <title>{{ $data->title }}</title>
</head>
    <body>
        <h1>{{ $data->title }}</h1>
        <p>Olá {{ $data->nameReceiver }} este é um e-mail da confirmação da sua transação:</p>
        <p>E-mail: {{ $data->emailReceiver }}</p>
        <p>Por favor não responda este e-mail</p>
    </body>
</html>
