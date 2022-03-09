<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Resources\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected function register(Request $request)
    {
        if (strlen($cpf_cnpj = preg_replace( '/[^0-9]/', '', $request->cpf_cnpj)) != 14 && strlen($cpf_cnpj = preg_replace( '/[^0-9]/', '', $request->cpf_cnpj)) != 11) {
            return response()->json(array("error"=>"CPF ou CNPJ inválido"));
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

        if (User::where('email','=',$request->email)->first()) {
            return response()->json(array("error" => "Esse e-mail foi cadastrado para outro usuário"));
        }

        if (User::where('cpf_cnpj','=', $cpf_cnpj)->first()) {
            return response()->json(array("error" => "Esse CPF/CNPJ foi cadastrado para outro usuário"));
        }

        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255','unique:users'],
            'password'  => ['required', 'string', 'min:4'],
        ],[
            'name.required'         => 'Nome do usuário obrigatório',
            'name.max'              => 'Caractere maximo para o nome foi ultrapassado',
            'email.required'        => 'Email obrigatório',
            'email.unique'          => 'Esse email foi cadastrado para outro usuário',
            'email.max'             => 'Caractere maximo para o email foi ultrapassado',
            'email.email'           => 'Email invalido',
            'password.required'     => 'Senha obrigatória',
            'password.min'          => 'É necessario mais caracteres para senha',
           ]
        );

        if ($validator->fails()) {
            return response()->json(array("error" => $validator->errors()->first()));
        }

        if ($user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'cpf_cnpj'  => $cpf_cnpj,
            'password'  => Hash::make($request->password),
        ])) {
            $user->save();
            $user->access_token = $user->createToken($request->email)->accessToken;
            return response()->json(array(
                "success" => "Usuário registrado com sucesso",
                "user" => $user
            ));
        }
        return response()->json(array("error" => "Erro ao registrar o usuário"));
    }

    protected function update(Request $request)
    {
        $user = $request->user();
        $data = $request->all();

        if (isset($data['password'])) {
            $validator = Validator::make($data, [
                'password' => ['string', 'min:8','confirmed'],
            ],[
                'password.min'          => 'É necessário mais caracteres para senha',
                'password.confirmed'    => 'Confirme sua senha com a mesma digitada anteriormente',
               ]
            );

            if ($validator->fails()){
                return response()->json(array("error" => $validator->errors()->first()));
            }
        }

        if ($user = User::where('id','=',$user->id)->first()) {
            $user->name         = $request->name ? $request->name : $user->name;
            $user->email        = $request->email ? $request->email : $user->email;

            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if (!$user->save()){
                return response()->json(array("error" => "Ocorreu uma falha ao alterar as informações, tente mais tarde"));
            }

            $user->access_token = $user->createToken($request->email)->accessToken;

            return response()->json(array(
                "success" => "Usuário alterado com sucesso",
                "user" => $user)
            );
        }
        return response()->json(array("error" => "Não foi possivel alterar o seu cadastro"));
    }
}
