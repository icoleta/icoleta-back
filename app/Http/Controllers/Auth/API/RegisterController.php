<?php

namespace App\Http\Controllers\Auth\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
            'cpf' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }
        
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
