<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function registerCompany(Request $request) {    
        $validator = Validator::make($request->all(), [
            'trading_name' => 'required',
            'company_name' => 'required',
            'cnpj' => 'required',
            'phone' => 'required',
            // 'license' => 'required',
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
            $company->trading_name = $request->trading_name;
            $company->company_name = $request->company_name;
            $company->cnpj = $request->cnpj;
            $company->phone = $request->phone;
            $company->userId = $user->id;
            $company->save();
        
            if($company->save()){
                return response()->json([
                    'status' => 'Usuário criado com sucesso.',
                    'data' => $user,
                ]);                
            } else{
                return response()->json([
                    'error' => "Houve um erro inesperado"
                ], 500);
            }            
        });
    }

    public function createPoint(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'hours' => 'required',
            'items' => 'required',
            'companyEmail' => 'required',
            // 'license' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $user = User::where('email', $request->companyEmail)->first();
        $company = Company::where('userId', $user->id)->first();

        $point = new Point();
        $point->name = $request->name;
        $point->hours = $request->hours;
        $point->items = $request->items;
        $point->company_id = $company->id;

        if($point->save()) {
            return response()->json([
                'status' => 'Ponto criado com sucesso.',
                'data' => $point,
            ]);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }
    }
}
