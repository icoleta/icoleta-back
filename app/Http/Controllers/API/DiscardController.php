<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discard;
use App\Models\Person;
use App\Models\User;
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
        $discards = Discard::with(['person', 'residuum', 'point'])->get();
        return response($discards, 200);
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
            'email' => 'required',
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
                $person = User::where('email', $request->email)->first()->person;

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

    public function listUserDiscards($userId) {
        $personId = Person::where('user_id', $userId)->first()->id;
        
        $userDiscards = Discard::with('residuum')->where('person_id', $personId)->get();
        $totalWeightDiscarded = 0;

        $discardsPerResiduum = [];
        $weightPerResiduum = [];

        foreach($userDiscards as $discard) {
            $totalWeightDiscarded += $discard->weight;

            // Initialize or sum residuum count
            if(!array_key_exists($discard->residuum->name, $discardsPerResiduum)) {
                $discardsPerResiduum[$discard->residuum->name] = 1;
            } else {
                $discardsPerResiduum[$discard->residuum->name] += 1;
            }

            // Initialize or sum residuum weight
            if(!array_key_exists($discard->residuum->name, $weightPerResiduum)) {
                $weightPerResiduum[$discard->residuum->name] = $discard->weight;
            } else {
                $weightPerResiduum[$discard->residuum->name] += $discard->weight;
            }
        }

        return response()->json([
            'discardsCount' => $userDiscards->count(),
            'totalWeightDiscarded' => $totalWeightDiscarded,
            'discardsPerResiduum' => $discardsPerResiduum,
            'weightPerResiduum' => $weightPerResiduum,
            'discards' => $userDiscards,
        ]);
    }
}
