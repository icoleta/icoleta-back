<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'license' => 'required',
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

        $company = new Company();
        $company->trading_name = request('trading_name');
        $company->company_name = request('company_name');
        $company->cnpj = request('cnpj');
        $company->phone = request('phone');
        $company->email = request('email');
        $company->password = Hash::make(request('password'));
        $company->save();
    }
}
