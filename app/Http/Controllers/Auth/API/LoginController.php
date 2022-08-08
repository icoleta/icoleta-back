<?php

namespace App\Http\Controllers\Auth\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $credentials = $request->only('email', 'password');

        if(!auth()->attempt($credentials))
            return response()->json([
                'error' => "Dados inválidos!"
            ], 401);
        
        $token = auth()->user()->createToken($request->email);
        auth()->user()->token = $token->plainTextToken;
        return response()->json(['data' => auth()->user()]);
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
}
