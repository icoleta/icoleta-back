<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Point;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    public function listAll() {
        $points = Point::all();
        return response()->json([
            'data' => $points,
        ]);
    }

    public function listPoints(Request $request) {
        $user = User::where('email', $request->CompanyEmail)->first();
        $points = Point::where('company_id', $user->company->id)->get();

        return response()->json([
            'data' => $points,
        ]);
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
        $company = $user->company;

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

    public function showPoint($id) {
        $point = Point::find($id);
        return response()->json([
            'data' => $point,
        ]);
    }

    public function editPoint(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'hours' => 'required',
            'items' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $point = Point::find($id);
        $point->name = $request->name;
        $point->hours = $request->hours;
        $point->items = $request->items;

        if($point->save()) {
            return response()->json([
                'status' => 'Ponto editado com sucesso.',
                'data' => $point,
            ]);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }
    }

    public function deletePoint($id) {
        Point::find($id)->delete();
        return response()->json([
            'status' => 'Ponto removido com sucesso.',
        ]);
    }
}
