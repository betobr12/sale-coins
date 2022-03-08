<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Resources\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected function login(Request $request)
    {

        if (strlen($cpf_cnpj = preg_replace( '/[^0-9]/', '', $request->cpf_cnpj)) != 14 && strlen($cpf_cnpj = preg_replace( '/[^0-9]/', '', $request->cpf_cnpj)) != 11) {
            return response()->json(array("error"=>"CPF ou CNPJ invalido"));
        }

        $helper           = new Helpers;
        $helper->cpf_cnpj = $cpf_cnpj;

        if (strlen($cpf_cnpj) == 11) {
            if ($helper->validateCPF() == false) {
                return response()->json(array("error"=>"CPF inválido"));
            }
        }

        if (strlen($cpf_cnpj) == 14) {
            if ($helper->validateCNPJ() == false) {
                return response()->json(array("error"=>"CNPJ inválido"));
            }
        }

        $validator = Validator::make($request->all(),[
            'password' => ['required', 'string'],
        ],[
            'password.required'     => 'Senha obrigatória. ',
        ]);

        if ($validator->fails()) {
            return response()->json(array("error"=>$validator->errors()->first()));
        }

        if (Auth::attempt(['cpf_cnpj' => $cpf_cnpj, 'password' => $request->password])) {
            $user = auth()->user();
            $user->access_token = $user->createToken($cpf_cnpj)->accessToken;
            return response()->json(array("success" => "Usuário logado com sucesso", "data" => $user));
        }
        return response()->json(array("error" => "Usuário ou senha incorreto"));
    }


}
