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
            'cpf' => 'required|unique:people,cpf',
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
                $user->isCompany = false;
                $user->password = Hash::make($request->password);
                $user->save();
    
                $person = new Person();
                $person->name = $request->name;
                $person->cpf = $request->cpf;
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

    public function makeVolunteer($personId) {
        $person = Person::find($personId);
        if(!$person) {
            return response()->json([
                'error' => 'Usuário não encontrado.'
            ], 404);
        }
        
        $user = User::find($person->user_id);

        if(!$user) {
            return response()->json([
                'error' => 'Usuário não encontrado.'
            ], 404);
        }
        if($user->isCompany) {
            return response()->json([
                'error' => 'Entidade não pode ter o papel de voluntário.'
            ], 404);
        }
        
        $volunteerRoleId = Role::where('name', 'volunteer')->first()->id;
        
        $user->role_id = $user->role_id == $volunteerRoleId ? null : $volunteerRoleId;
        $user->save();
        
        return response(null, 204);
    }
}
