<!DOCTYPE html>
<html>
<head>
    <title>{{ $data->title }}</title>
</head>
    <body>
        <h1>{{ $data->title }}</h1>
        <p>Olá {{ $data->nameReceiver }} este é um e-mail da confirmação da sua cotação:</p>
        <p>E-mail: {{ $data->emailReceiver }}</p>

        <p>Valor: R$ {{number_format($data->transactionData->net_value, 2, ',', '.') }}</p>
        <p>Taxa de pagamento: R$ {{number_format($data->transactionData->tax_payment, 2, ',', '.')}}</p>
        <p>Taxa de conversão: R$ {{number_format($data->transactionData->tax_conversion, 2, ',', '.') }}</p>
        <p>Valor com impostos: R$ {{number_format($data->transactionData->value, 2, ',', '.') }}</p>
        <p>Moeda cotada: {{$data->codeCoin }}</p>
        <p>Quantidade da moeda cotada: {{$data->transactionData->currency_amount }}</p>

        <p>Por favor não responda este e-mail</p>
    </body>
</html>
