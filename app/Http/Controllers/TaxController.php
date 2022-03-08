<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaxController extends Controller
{
    protected function get(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        if ($user->access_level == 2) {
            return response()->json(["error" => "Você não tem acesso a essa operação"]);
        }

        $tax = new Tax();
        $tax->id = $request->id;
        $tax->uuid = $request->uuid;
        return response()->json($tax->getTax());
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
            'percentage'   => ['required','numeric'],
            'description'  => ['required','string'],
        ],[
            'percentage.required' => 'Parametro porcentagem é obrigatório.',
            'percentage.numeric'  => 'Parametro porcentagem é numérico.',
        ]);

        if ($validator->fails()) {
            return response()->json(array("error" => $validator->errors()->first()));
        }

        if (Tax::create([
            'uuid' => Str::orderedUuid(),
            'description' => $request->description,
            'percentage' => $request->percentage,
            'created_at' => \Carbon\Carbon::now(),
        ])) {
            return response()->json(["success" => "Taxa cadastrada com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível cadastrar a taxa"]);
    }

    protected function update(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(["error" => "Usuário não autenticado"]);
        }

        if ($user->access_level == 2) {
            return response()->json(["error" => "Você não tem acesso a essa operação"]);
        }

        if (!$tax = Tax::where('id', '=', $request->id)->where('uuid', '=', $request->uuid)->first()) {
            return response()->json(["error" => "Não foi possível localizar a taxa, por favor tente mais tarde"]);
        }

        $tax->percentage  = $request->percentage;

        if ($tax->save()) {
            return response()->json(["success" => "Taxa alterada com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível alterar a taxa, por favor tente mais tarde"]);
    }
}
