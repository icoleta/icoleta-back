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
            'cnpj' => 'required',
            'name' => 'required',
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
        $company->name = request('name');
        $company->email = request('email');
        $company->cnpj = request('cnpj');
        $company->password = Hash::make(request('password'));
        $company->save();
    }
}
