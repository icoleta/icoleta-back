<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Residuum;
use Illuminate\Http\Request;

class ResiduumController extends Controller
{
    public function index() {
        $residuum = Residuum::all();
        return response()->json($residuum);
    }

    public function store(Request $request) {
        $residuum = new Residuum();
        $residuum->name = $request->name;
        $residuum->save();

        return response('', 201);
    }

    public function edit(Request $request, $id) {
        $residuum = Residuum::find($id);

        if(!$residuum) {
            return response()->json([
                'error' => 'Entidade não encontrada.'
            ], 404);
        }

        $residuum->name = $request->name;
        $residuum->save();
        
        return response(null, 204);
    }

    public function delete($id) {
        $residuum = Residuum::find($id);

        if(!$residuum) {
            return response()->json([
                'error' => 'Entidade não encontrada.'
            ], 404);
        }

        $residuum->delete();
        return response(null, 204);
    }
}
