<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discard;
use App\Models\Person;
use App\Models\Residuum;
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

    public function listUserDiscards(Request $request) {
        $personId = Person::where('user_id', $request->user()->id)->first()->id;
        
        $userDiscards = Discard::with(['residuum', 'point'])->where('person_id', $personId)->get();
        $totalWeightDiscarded = 0;
        $summaryByResiduum = [];
        
        // Initializes count and weight by residuum
        $residuumTypes = Residuum::orderBy('name')->get();
        foreach($residuumTypes as $residuum) {
            $summaryByResiduum[$residuum->name]['count'] = 0;
            $summaryByResiduum[$residuum->name]['weight'] = 0;
        }

        // Sums the total discarded weight and the count and weight per residuum discarded
        foreach($userDiscards as $discard) {
            $totalWeightDiscarded += $discard->weight;
            $summaryByResiduum[$discard->residuum->name]['count'] += 1;
            $summaryByResiduum[$discard->residuum->name]['weight'] += $discard->weight;
        }

        return response()->json([
            'discardsCount' => $userDiscards->count(),
            'totalWeightDiscarded' => $totalWeightDiscarded,
            'summaryByResiduum' => $summaryByResiduum,
            'discards' => $userDiscards,
        ]);
    }
}
