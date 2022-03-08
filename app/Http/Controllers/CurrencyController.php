<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CurrencyController extends Controller
{
    protected function get(Request $request)
    {
        $currency = new Currency();
        $currency->id = $request->id;
        $currency->uuid = $request->uuid;
        $currency->onlyActive = $request->onlyActive;
        return response()->json($currency->getCurrency());
    }

    protected function new(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        if ($user->access_level == 2) {
            return response()->json(["error" => "Você não tem acesso a essa operação"]);
        }

        $validator = Validator::make($request->all(), [
            'description'  => ['required','string'],
        ],[
            'description.required' => 'Descrição é obrigatória.',
        ]);

        if ($validator->fails()) {
            return response()->json(array("error" => $validator->errors()->first()));
        }

        if (Currency::create([
            'uuid' => Str::orderedUuid(),
            'description' => $request->description,
            'created_at' => \Carbon\Carbon::now(),
        ])) {
            return response()->json(["success" => "Moeda cadastrada com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível cadastrar a moeda"]);
    }

    protected function delete(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        if ($user->access_level == 2) {
            return response()->json(["error" => "Você não tem acesso a essa operação"]);
        }

        if (!$currency = Currency::where('id', '=', $request->id)->where('uuid', '=', $request->uuid)->whereNull('deleted_at')->first()) {
            return response()->json(["error" => "Não foi possível localizar a moeda, por favor tente mais tarde"]);
        }

        $currency->deleted_at  = \Carbon\Carbon::now();

        if ($currency->save()) {
            return response()->json(["success" => "Moeda excluída com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível excluir a moeda, por favor tente mais tarde"]);
    }

    protected function getCurrencyHttp(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        $guzzleHttp = new GuzzleHttp();
        $guzzleHttp->url = "https://economia.awesomeapi.com.br/json/all/";
        $guzzleHttp->currence_first =  $request->description;

        if (!$dataResultHttp =  $guzzleHttp->getHttp()) {
            return response()->json(["error" => "Ocorreu uma falha no nosso fornecedor de serviço, por favor tente mais tarde"]);
        }

        foreach ($dataResultHttp->data as $data) {
            $dataCoin['code'] = $data->code;
            $dataCoin['bid'] = $data->bid;
            $dataCoin['name'] = $data->name;
            $dataCoin['codein'] = $data->codein;
        }

        $coinResult = (object) $dataCoin;

        return response()->json($coinResult);
    }

}
