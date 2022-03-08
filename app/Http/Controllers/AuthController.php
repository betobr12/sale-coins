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

    protected function register(Request $request)
    {
        $cpf_cnpj = preg_replace( '/[^0-9]/', '', $request->cpf_cnpj);

        $helpers           = new Helpers;
        $helpers->cpf_cnpj = $cpf_cnpj;

        if ($helpers->validateCPF() == false) {
            return response()->json(array("error"=>"CPF invalido"));
        }

        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:11'],
            'email'     => ['required', 'string', 'email', 'max:255','unique:users'],
            'password'  => ['required', 'string', 'min:4'],
        ],[
            'name.required'         => 'Nome do usuário obrigatorio',
            'name.max'              => 'Caractere maximo para o nome foi ultrapassado',
            'phone.required'        => 'Telefone é obrigatorio',
            'phone.max'             => 'Caractere maximo para o telefone foi ultrapassado',
            'email.required'        => 'Email obrigatorio',
            'email.unique'          => 'Esse email foi cadastrado para outro usuário',
            'email.max'             => 'Caractere maximo para o email foi ultrapassado',
            'email.email'           => 'Email invalido',
            'password.required'     => 'Senha obrigatória',
            'password.min'          => 'É necessario mais caracteres para senha',
           ]
        );

        if ($validator->fails()) {
            return response()->json(array("error"=>$validator->errors()->first()));
        }

        if (User::where('email','=',$request->email)->first()) {
            return response()->json(array("error" => "Esse email foi cadastrado para outro usuário"));
        }

        if (User::where('cpf_cnpj','=',$cpf_cnpj)->first()) {
            return response()->json(array("error" => "Esse CPF/CNPJ foi cadastrado para outro usuário"));
        }

        if ($user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'cpf_cnpj'       => $cpf_cnpj,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
        ])) {
            $user->save();
            $user->token = $user->createToken($request->email)->accessToken;
            return response()->json(array("success" => "Usuário registrado com sucesso", "user" => $user));
        }
        return response()->json(array("error"=>"Erro ao registrar o usuário"));
    }
}
