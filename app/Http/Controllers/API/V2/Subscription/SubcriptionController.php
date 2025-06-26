<?php

namespace App\Http\Controllers\API\V2\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcriptionController extends Controller
{
    /**
     * Create Subscription
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'identifier' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'data' => 'nullable',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                400
            );
        }

        Subscription::create([
            'user_id' => $request->user()->id,
            'identifier' => $request->identifier,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'data' => $request->data ? null : json_encode($request->data),
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Subscription berhasil ditambahkan",
        ], 200);
    }
}
