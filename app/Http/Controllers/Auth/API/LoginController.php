<?php

namespace App\Http\Controllers\Auth\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
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

        // $login = User::where('email', $request->email)->first();
        $credentials = $request->only('email', 'password');

        if(!auth()->attempt($credentials))
            return response()->json([
                'error' => "Dados inválidos!"
            ], 401);
        
        $token = auth()->user()->createToken($request->email);
        auth()->user()->token = $token->plainTextToken;
        return response()->json(['data' => auth()->user()]);
        // if ($login && Hash::check($request->password, $login->password)) {
        //     return response()->json([
        //         'status' => 'Usuário logado com sucesso.',
        //         'data' => $login,
        //     ]); 
        // } else {
        //     return response()->json([
        //         'error' => "Dados inválidos!"
        //     ], 401);
        // }
    }
}
