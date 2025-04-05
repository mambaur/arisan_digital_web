<?php

namespace App\Http\Controllers\API\V2\Auth;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Login by Google
     * @unauthenticated
     */
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'device_token' => ['required'],
        ]);
        // Getting the user from socialite using token from google
        $user = Socialite::driver('google')->stateless()->userFromToken($request->token);

        DB::beginTransaction();

        // Getting or creating user from db
        $userFromDb = User::firstOrCreate(
            ['email' => $user->email],
            [
                'google_id' => $request->google_id,
                'email' =>  $user->email,
                'name' => $user->name,
                "photo_url" => $request->photo_url,
                'password' => Hash::make('2y82lkskfs732lska8'),
            ]
        );

        $token = $userFromDb->createToken($request->device_name ?? 'mobile');
        $plainTextToken = $token->plainTextToken;
        $token_id = $token->accessToken->id;
        $this->storeDeviceToken($request, $request->device_token, $token_id);

        DB::commit();

        return response()->json([
            'data' => [
                'token' => $plainTextToken,
                'user' => $userFromDb
            ]
        ], 200);
    }

    /**
     * Login with Email Password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @unauthenticated
     */
    public function loginManual(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
                'device_token' => 'required',
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

        // if (!$user->hasVerifiedEmail()) {
        //     return response()->json(
        //         [
        //             'status' => 'unverified',
        //             'message' => 'Akun kamu masih belum aktif, silahkan cek pesan masuk email kamu untuk melakukan verifikasi. Jika tidak ada, silahkan cek di folder SPAM.',
        //             'data' => null,
        //         ],
        //         200
        //     );
        // }

        $token = $user->createToken($request->device_name ?? 'mobile');
        $plainTextToken = $token->plainTextToken;
        $token_id = $token->accessToken->id;
        $this->storeDeviceToken($request, $request->device_token, $token_id);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Login berhasil.',
                'data' => [
                    'token' => $plainTextToken,
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
     * @unauthenticated
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

        // $user->sendEmailVerificationNotification();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Register berhasil, silahkan login menggunakan akun baru anda.',
                'data' => null
            ],
            200
        );
    }

    /**
     * Detail User
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
     * Logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        DB::beginTransaction();
        $access_token_id = $request->user()->currentAccessToken()->id;
        $this->removeDeviceToken($access_token_id);

        $request
            ->user()
            ->currentAccessToken()
            ->delete();
        DB::commit();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Anda berhasil logout.',
            ],
            200
        );
    }

    /**
     * Resend Verification Email
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
     * Forgot Password
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

    private function storeDeviceToken(Request $request, string $device_token, string $access_token_id): void
    {
        Device::create([
            'token' => $device_token,
            'personal_access_token_id' => $access_token_id,
            'user_id' => $request->user()->id,
            'user_type' => 'App\Models\User',
        ]);
        
    }

    private function removeDeviceToken(string $access_token_id)
    {
        Device::where('personal_access_token_id', $access_token_id)->delete();
    }
}
