<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discard;
use App\Models\Person;
use App\Models\Residuum;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'error' => $messages,
            ], 500);
        }

        try {
            DB::transaction(function () use (
                $request
            ) {
                $person = User::where('email', $request->email)->first()->person;

                if (!$person)
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

    public function createDiscardAsUser(Request $request)
    {        
        try {
            $data = $this->getTokenData(request('token'));
            list($weight, $pointId, $iat) = $data;
            if (!$data) {
                return response()->json([
                    'error' => 'Token Inválido!'
                ], 500);
            }

            if ($this->isIatOlderThanFiveMinutes($iat)) {
                return response()->json([
                    'error' => 'Token Expirado!'
                ], 500);
            }
    
            $person = User::where('email', $request->email)->first()->person;
            if (!$person) {
                return response()->json([
                    'error' => 'Usuário não encontrado!'
                ], 404);
            }

            // tokens are unique, trying to use the same will result in error
            $token = new Token();
            $token->token = request('token');
            $token->save();

            $discard = new Discard();
            $discard->person_id = $person->id;
            $discard->point_id = $pointId;
            $discard->weight = $weight;
            $discard->residuum_id = 4; // TODO: hardcoded, mudar isso
            $discard->save();

            return response()->json([
                'status' => 'Descarte cadastrado!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                // 'error' => $th,
                'error' => 'Erro desconhecido'
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

        if (!$discard) {
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
        if (!$discard) {
            return response()->json(['error' => 'Descarte não encontrado!'], 404);
        }

        if ($discard->delete()) {
            return response()->json(['status' => 'Descarte removido!'], 200);
        } else {
            return response()->json([
                'error' => "Houve um erro inesperado!"
            ], 500);
        }
    }

    public function listUserDiscards(Request $request)
    {
        $personId = Person::where('user_id', $request->user()->id)->first()->id;

        $userDiscards = Discard::with(['residuum', 'point'])->where('person_id', $personId)->get();
        $totalWeightDiscarded = 0;
        $summaryByResiduum = [];

        // Initializes count and weight by residuum
        $residuumTypes = Residuum::orderBy('name')->get();
        foreach ($residuumTypes as $residuum) {
            $summaryByResiduum[$residuum->name]['count'] = 0;
            $summaryByResiduum[$residuum->name]['weight'] = 0;
        }

        // Sums the total discarded weight and the count and weight per residuum discarded
        foreach ($userDiscards as $discard) {
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

    private function getTokenData($token)
    {
        $secretKey = env('DISCARD_SECRET');

        list($headerEncoded, $payloadEncoded, $signature) = explode('.', $token);
        $payload = json_decode(base64_decode($payloadEncoded));

        $computedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secretKey, true);
        $computedSignatureBase64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($computedSignature));

        if ($computedSignatureBase64 !== $signature) {
            return null;
        }
    
        $weight = $payload->weight;
        $pointId = $payload->point_id;
        $iat = $payload->iat;

        return [
            $weight,
            $pointId,
            $iat
        ];
    }

    private function isIatOlderThanFiveMinutes($iat)
    {
        $currentTimestamp = time();
        return ($currentTimestamp - $iat) > (60 * 5);
    }
}