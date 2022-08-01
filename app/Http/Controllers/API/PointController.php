<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $points = Point::all();
        
        return response()->json([
            'data' => $points,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        
        if(!$user){
            return response()->json([
                'error' => 'Email não cadastrado!',
            ], 400);
        }

        $point = new Point();
        $point->name = $request->name;
        $point->hours = $request->hours;
        $point->items = $request->items;
        $point->company_id = $user->company->id;

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $point = Point::where('id', $id)->first();
        
        if(!$point){
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        return response()->json([
            'data' => $point,
        ]);
    }

    /**
     * Display all points from a user.
     * 
     * @param Request $request
     * @return Json 
     */
    public function showUserPoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CompanyEmail' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }
        
        $user = User::where('email', $request->CompanyEmail)->first();
        
        if(!$user){
            return response()->json([
                'error' => 'Email não cadastrado!',
            ], 400);
        }
        $points = Point::where('company_id', $user->company->id)->get();

        return response()->json([
            'data' => $points,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
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

        $point = Point::where('id', $id)->first();
        
        if(!$point){
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }
        
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $point = Point::where('id', $id)->first();
        
        if(!$point){
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        if($point->delete()) {
            return response()->json([
                'status' => 'Ponto removido com sucesso.',
            ]);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }
    }
}
