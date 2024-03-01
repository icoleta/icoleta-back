<?php

namespace App\Http\Controllers\API;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        $points = Point::with('collectableItems')->get();
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
            'items' => 'required|array',
        ]);

        if($validator->fails())
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
                $point = new Point();
                $point->name = $request->name;
                $point->hours = $request->hours;
                $point->phone = $request->phone;
                $point->longitude = $request->longitude;
                $point->latitude = $request->latitude;        
                $point->company_id = $request->user()->company->id;
                $point->save();

                $point->collectableItems()->attach($request->items);

                if($request->image) {
                    $newImagePath = $request->image->store('images_points', 'public');
                    $oldImagePath = $point->path;
                    
                    try {
                        $point->image = $newImagePath;
                        $point->save();
                    } catch (\Throwable $th) {
                        dd($th);
                        if ($newImagePath) {
                            Storage::delete($newImagePath);
                        }
                    }
                    Storage::delete($oldImagePath);
                }
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }

        return response(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $point = Point::with('collectableItems')->where('id', $id)->first();
        
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
        $points = Point::with('collectableItems')->where('company_id', $request->user()->company->id)->get();
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
            'items' => 'required|array',
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
        

        try {
            DB::transaction(function() use(
                $request,
                $point
            ) {
                $point->name = $request->name;
                $point->hours = $request->hours;
                $point->phone = $request->phone;
                $point->longitude = $request->longitude;
                $point->latitude = $request->latitude;
                $point->save();
                $point->collectableItems()->sync($request->items);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Houve um erro inesperado"
            ], 500);
        }

        return response(null, 204);
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
