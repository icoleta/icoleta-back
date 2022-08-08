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
        return response($points, 200);
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
        ]);

        if($validator->fails())
        {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        $point = new Point();
        $point->name = $request->name;
        $point->hours = $request->hours;
        $point->phone = $request->phone;
        $point->longitude = $request->longitude;
        $point->latitude = $request->latitude;        
        $point->company_id = $request->user()->company->id;

        if($point->save()) {
            return response(null, 201);
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

        return response($point, 200);
    }

    /**
     * Display all points from a user.
     * 
     * @param Request $request
     * @return Json 
     */
    public function showCompanyPoints(Request $request)
    {
        $points = Point::where('company_id', $request->user()->company->id)->get();
        return response($points);
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

        $point = Point::find($id);
        if(!$point){
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }
        
        $point->name = $request->name;
        $point->hours = $request->hours;
        $point->phone = $request->phone;
        $point->longitude = $request->longitude;
        $point->latitude = $request->latitude;        

        if($point->save()) {
            return response(null, 204);
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
        $point = Point::find($id);
        if(!$point){
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        if($point->delete()) {
            return response(null, 204);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }
    }
}
