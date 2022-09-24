<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login by Google
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

        if ($user) {
            if ($user->google_id != $request->google_id) {
                return response()->json(
                    [
                        'status' => 'failed',
                        'message' => 'Login gagal, data kredensial akun anda tidak cocok.'
                    ],
                    200
                );
            }

            if ($user->name != $request->name) {
                $user->name = $request->name;
                $user->save();
            }

            if ($user->photo_url != $request->photo_url) {
                $user->photo_url = $request->photo_url;
                $user->save();
            }
        } else {
            // Register User
            $user = User::create([
                "google_id" => $request->google_id,
                "name" => $request->name,
                "email" => $request->email,
                "photo_url" => $request->photo_url,
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
     * Login Manual
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginManual(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Email atau password kamu salah.',
                    'data' => null,
                ],
                200
            );
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Akun kamu masih belum aktif, silahkan cek pesan masuk email kamu untuk melakukan verifikasi.',
                    'data' => null,
                ],
                200
            );
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
     * Register Akun
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required',
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'photo_url' => 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
            'password' => Hash::make($request->password),
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Register berhasil, silahkan cek email untuk verifikasi akun kamu.',
                'data' => null
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
                    "photo_url" => $request->user()->photo_url,
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

    /**
     * Handle a request verification email for user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ],
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
        if (!$user->hasVerifiedEmail()) {
            $key = 'send-email.' . $user->id;
            $max = 1;
            $decay = 300;

            if (RateLimiter::tooManyAttempts($key, $max)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json(
                    [
                        'status' => 'failed',
                        'message' =>
                        'Kamu telah melakukan request verifikasi sebelumnya, silahkan cek pesan masuk email kamu, termasuk di spam dan folder promosi. Kamu dapat mengirimkan request verifikasi kembali setelah ' .
                            $seconds .
                            ' detik',
                        'data' => null,
                    ],
                    200
                );
            } else {
                RateLimiter::hit($key, $decay);
                $user->sendEmailVerificationNotification();
                return response()->json(
                    [
                        'status' => 'success',
                        'message' =>
                        'Email verifikasi telah dikirimkan, silahkan cek pesan masuk email kamu untuk melakukan verifikasi.',
                        'data' => null,
                    ],
                    200
                );
            }
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Akun kamu sudah aktif.',
            ],
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ],
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
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = Password::broker()->sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json(
                [
                    'status' => 'success',
                    'message' =>
                    'Link reset password berhasil dikirimkan ke email kamu, silahkan cek pesan masuk email kamu.',
                ],
                200
            )
            : response()->json(
                [
                    'status' => 'failed',
                    'message' =>
                    'Kamu telah meminta reset password beberapa waktu yang lalu, silakan periksa email kamu kembali.',
                ],
                200
            );
    }
}
