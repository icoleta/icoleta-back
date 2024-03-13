<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
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
                $feedback = new Feedback();
                $feedback->name = request('name');
                $feedback->email = request('email');
                $feedback->message = request('message');
                $feedback->save();
            });
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Houve um erro inesperado."
            ], 500);
        }

        return response(null, 201);
    }
}
