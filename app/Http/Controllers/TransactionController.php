<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Services\Email;
use App\Services\GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{

    protected function get(Request $request, Transaction $transaction)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }
        $transaction->id = $request->id;
        return response()->json($transaction->getTransactions());
    }

    protected function new(Request $request, Transaction $transaction)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        $validator = Validator::make($request->all(), [
            'net_value'          => ['required','numeric'],
            'payment_method_id'  => ['required'],
            'currency_id'        => ['required'],
        ],[
            'net_value.required' => 'Valor para transação é obrigatório.',
            'net_value.numeric'  => 'Valor é numérico.',
        ]);

        if ($validator->fails()) {
            return response()->json(array("error" => $validator->errors()->first()));
        }

        if (!$currency = Currency::where('id', '=', $request->currency_id)->first()) {
            return response()->json(["error" => "Moeda informada não existe, por favor tente mais tarde"]);
        }

        if (number_format($request->net_value, 2, '.', '') <= 3700.00) {
            $tax_convertion = number_format($request->net_value * 2.00 / 100, 2, '.', '');
        }

        if (number_format($request->net_value, 2, '.', '') > 3700.00) {
            $tax_convertion = number_format($request->net_value * 1.00 / 100, 2, '.', '');
        }

        if (number_format($request->net_value, 2, '.', '') <= 900.00 || number_format($request->net_value, 2, '.', '') >= 900000.00) {
            return response()->json(array("error" => "O Valor deve ser maior que R$ 900,00 e menor que R$ 900.000,00"));
        }

        $paymentMethod      = new PaymentMethod();
        $paymentMethod->id  = $request->payment_method_id;
        $paymentMethod      = $paymentMethod->getPaymentMethod()[0];
        $tax_value          = number_format($request->net_value * $paymentMethod->percentage / 100, 2, '.', '');

        $guzzleHttp = new GuzzleHttp();
        $guzzleHttp->url = "https://economia.awesomeapi.com.br/json/all/";
        $guzzleHttp->currence_first =  $currency->description;

        if (!$dataResultHttp =  $guzzleHttp->getHttp()) {
            return response()->json(["error" => "Ocorreu uma falha no nosso fornecedor de serviço, por favor tente mais tarde"]);
        }

        foreach ($dataResultHttp->data as $data) {
            $dataCoin['code'] = $data->code;
            $dataCoin['bid'] = $data->bid;
        }

        $coinResult = (object) $dataCoin;

        if (!$transaction->create([
            'uuid' => Str::orderedUuid(),
            'user_id' => $user->id,
            'status_id' => 3,
            'payment_method_id' => $request->payment_method_id,
            'currency_amount' => number_format(number_format($request->net_value - $tax_convertion - $tax_value, 2, '.', '') / number_format($coinResult->bid, 2, '.', ''),2 , '.', ''),
            'currency_id' => $request->currency_id,
            'value' => number_format($request->net_value - $tax_convertion - $tax_value, 2, '.', ''),
            'net_value' => $request->net_value,
            'tax_payment' => $tax_value,
            'tax_conversion' => $tax_convertion,
            'confirmad_date_at' => null,
            'created_at' => \Carbon\Carbon::now()
        ])) {
            return response()->json(["error" => "Não foi possível incluir a transação, por favor tente mais tarde"]);
        }

        $this->title            = 'Transação incluida Aguardando a Sua Aprovação';
        $this->nameReceiver     = $user->name;
        $this->emailReceiver    = $user->email;
        $this->bladePage        = 'email.email';

        if (!$this->sendEmail($this)) {
            return response()->json(["error" => "Poxa, ocorreu um erro ao enviar a mensagem, por favor tente novamente mais tarde"]);
        }

        return response()->json([
            "success" => "Transação incluída com sucesso, favor confirme o pagamento",
            "data"    => $transaction->getTransactions()[0]
        ]);
    }

    protected function sendEmail()
    {
        Mail::to($this->emailReceiver)->send(new Email($this));
        return true;
    }

    protected function confirmPayment(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        if (!$transaction = Transaction::where('id', '=', $request->id)->where('uuid', '=', $request->uuid)->where('user_id', '=', $user->id)->whereNull('deleted_at')->first()) {
            return response()->json(["error" => "Não foi possível localizar a transação, por favor tente mais tarde"]);
        }

        $transaction->confirmad_date_at  = \Carbon\Carbon::now();
        $transaction->status_id          = 2;

        if ($transaction->save()) {

            $this->title            = 'O pagamento da sua transação foi confirmado com sucesso';
            $this->nameReceiver     = $user->name;
            $this->emailReceiver    = $user->email;
            $this->bladePage        = 'email.email';

            if (!$this->sendEmail($this)) {
                return response()->json(["error" => "Poxa, ocorreu um erro ao enviar a mensagem, por favor tente novamente mais tarde"]);
            }
            return response()->json(["success" => "Pagamento confirmado com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível confirmar o pagamento, por favor tente mais tarde"]);
    }
}
