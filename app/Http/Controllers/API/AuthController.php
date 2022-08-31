<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
              'email' => 'required|email',
              'name' => 'required',
              'google_id' => 'required',
              'device_name' => 'required',
            ]
        );
      
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $user = User::where('email', $request->email)->first();
        if(!$user){
            // Register User
            $user = User::create([
                "google_id" => $request->google_id,
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make("12345")
            ]);
        }

        $token = $user->createToken($request->device_name ?? "android")->plainTextToken;

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Login berhasil.',
                'data' => [
                    'token' => $token,
                ],
            ],
            200
        );
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                "status" => "success",
                "message" => "Data user berhasil didapatkan.",
                "data" => [
                    "id" => $request->user()->id,
                    "name" => $request->user()->name,
                    "email" => $request->user()->email,
                ]
            ], 200);
        }

        return response()->json([
            "status" => "failed",
            "message" => "Data user tidak ditemukan."
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Anda berhasil logout.',
            ],
            200
        );
    }
}
