<?php

namespace App\Http\Controllers\API;

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
            'cnpj' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        DB::transaction(function () use (
            $request,
        ) {
            $user = new User();
            $user->email = $request->email;
            $user->isCompany = true;
            $user->password = Hash::make($request->password);
            $user->save();

            $company = new Company();
            $company->name = $request->name;
            $company->cnpj = $request->cnpj;
            $company->phone = $request->phone;
            $company->user_id = $user->id;
        
            if($company->save()){
                return response()->json([
                    'status' => 'Criado com sucesso.',
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'error' => "Houve um erro inesperado."
                ], 500);
            }
        });
    }
}
