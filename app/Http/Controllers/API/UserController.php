<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function sendEmailToResetPass(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $user = User::where('email', $request->email)->first();

        if($user){
            $user->remember_token = Hash::make($request->email);
            try {
                Mail::to('victor.smtp.dev@gmail.com')->send(new ResetPassword($user->remember_token));

                $user->save();

                // return response('{status: "Email enviado"}', 200);
                return response()->json([
                    'status' => 'Email com o token foi enviado',
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'error' => "Houve um erro inesperado"
                ], 500);
            }
        }

        return response()->json([
            'error' => "Houve um erro inesperado"
        ]);
    }

    public function resetPass(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $user = User::where('remember_token', $request->token)->first();

        if($user){
            $user->password = Hash::make($request->password);
            $user->remember_token = null;

            if($user->save()){
                return response()->json([
                    'status' => 'Senha foi redefinida.',
                ]);                
            } else{
                return response()->json([
                    'error' => "Houve um erro inesperado"
                ], 500);
            }
        }

        return response()->json([
            'error' => "Houve um erro inesperado"
        ], 500);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $login = User::where('email', $request->email)->first();

        if ($login && Hash::check($request->password, $login->password)) {
            return response()->json([
                'status' => 'Usuário logado com sucesso.',
                'data' => $login,
            ]); 
        } else {
            return response()->json([
                'error' => "Dados inválidos!"
            ], 500);
        }
    }

    public function registerUser(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'password' => 'required',
        //     'cpf' => 'required',
        //     'email' => 'required',
        // ]);

        // if ($validator->fails())
        // {
        //     $messages = $validator->messages();
        //     return response()->json([
        //         'error' => $messages,
        //     ], 500);
        // }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->cpf = $request->cpf;
        $user->roles_id = 1;
        $user->password = Hash::make($request->password);

        if($user->save()){
            return response()->json([
                'status' => 'Usuário criado com sucesso.',
                'data' => $user,
            ]);                
        } else{
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }
    }
}
