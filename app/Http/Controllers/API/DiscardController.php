<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discard;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discard = Discard::all();

        return response()->json(['data' => $discard], 200);
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
            'cpf' => 'required',
            'weight' => 'required',
            'point_id' => 'required',
            'residuum_id' => 'required',
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
                $person = Person::where('cpf', $request->cpf)->first();

                if(!$person)
                    return response()->json([
                        'error' => 'Usuário não encontrado!'
                    ], 404);

                $discard = new Discard();
                $discard->person_id = $person->id;
                $discard->point_id = $request->point_id;
                $discard->residuum_id = $request->residuum_id;
                $discard->weight = $request->weight;
                $discard->save();
    
                return response()->json([
                    'data' => $discard,
                    'status' => 'Descarte cadastrado!'
                ], 201);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Houve um erro inesperado!'
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
        $discard = Discard::where('id', $id)->first();
        
        if(!$discard){
            return response()->json(['error' => 'Descarte não encontrado!'], 404);
        }

        return response()->json(['data' => $discard], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discard = Discard::find($id);
        if(!$discard){
            return response()->json(['error' => 'Descarte não encontrado!'], 404);
        }

        if($discard->delete()) {
            return response()->json(['status' => 'Descarte removido!'], 200);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado!"
            ], 500);
        }
    }
}
