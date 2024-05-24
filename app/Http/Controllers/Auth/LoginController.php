<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as dologin;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();
        if ($user) {
            if (!count(@$user->getRoleNames() ?? [])) {
                session()->flash('error', 'You are not permitted to log in to this page');
                return redirect()->route('login')->withInput();
            }

            foreach (@$user->getRoleNames() as $role) {
                if (!in_array($role, ['admin'])) {
                    session()->flash('error', 'You are not permitted to log in to this page');
                    return redirect()->route('login')->withInput();
                }
            }
        }

        return $this->doLogin($request);
    }
}
