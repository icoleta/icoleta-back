<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function store(Request $request) {    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|unique:companies,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        try {
            DB::transaction(function() use(
                $request
            ) {
                $companyRoleId = Role::where('name', 'company')->first()->id;
                
                $user = new User();
                $user->email = $request->email;
                $user->role_id = $companyRoleId;
                $user->password = Hash::make($request->password);
                $user->save();

                $company = new Company();
                $company->name = $request->name;
                $company->phone = $request->phone;
                $company->user_id = $user->id;
                $company->save();
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Houve um erro inesperado."
            ], 500);
        }

        return response(null, 201);
    }

    public function index() {
        $companies = Company::all();
        return response()->json($companies);
    }

    public function show($id) {
        $company = Company::find($id);
        if(!$company) {
            return response()->json([
                'error' => "Entidade não encontrada."
            ], 404);
        }

        return response()->json($company);
    }

    public function verify($companyId) {
        $company = Company::find($companyId);
        if(!$company) {
            return response()->json([
                'error' => "Entidade não encontrada."
            ], 404);
        }

        $company->verified = $company->verified ? false : true;
        $company->save();

        return response(null, 204);
    }
}
