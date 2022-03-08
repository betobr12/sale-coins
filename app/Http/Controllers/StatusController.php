<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StatusController extends Controller
{

    protected function get(Request $request)
    {
        $status = new Status();
        $status->id = $request->id;
        $status->uuid = $request->uuid;
        return response()->json($status->getStatus());
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

        if (Status::create([
            'uuid' => Str::orderedUuid(),
            'description' => $request->description,
            'created_at' => \Carbon\Carbon::now(),
        ])) {
            return response()->json(["success" => "Status cadastrado com sucesso"]);
        }
        return response()->json(["error" => "Não foi possível cadastrar o status"]);
    }
}
