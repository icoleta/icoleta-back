<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'course_id' => 'required',
            // 'semester_id' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        try {
            DB::transaction(function() use (
                $request
            ) {
                $user = new User();
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();

                $person = new Person();
                $person->name = $request->name;
                $person->course_id = 1;
                $person->semester_id = 1;
                $person->user_id = $user->id;
                $person->save();
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Houve um erro inesperado.'
            ], 500);
        }

        return response(null, 201);
    }

    public function index() {
        $people = Person::all();
        return response()->json($people);
    }

}
