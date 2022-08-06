<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'cpf' => 'required',
            'course_id' => 'required',
            'semester_id' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        DB::transaction(function() use (
            $request
        ) {
            $user = new User();
            $user->email = $request->email;
            $user->isCompany = false;
            $user->password = Hash::make($request->password);
            $user->save();

            $person = new Person();
            $person->name = $request->name;
            $person->cpf = $request->cpf;
            $person->course_id = $request->course_id;
            $person->semester_id = $request->semester_id;
            $person->user_id = $user->id;

            if($person->save()) {
                return response()->json([
                    'status' => 'Criado com sucesso.',
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'error' => 'Houve um erro inesperado.'
                ], 500);
            }
        });
    }
}
