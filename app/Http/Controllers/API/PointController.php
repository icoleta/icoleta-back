<?php

namespace App\Http\Controllers\API;

use App\Models\Point;
use App\Http\Controllers\Controller;

class PointController extends Controller
{
    public function listAll() {
        $points = Point::all();
        return response()->json([
            'data' => $points,
        ]);
    }
}
