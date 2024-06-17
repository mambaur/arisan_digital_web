<?php

namespace App\Http\Controllers;

use App\Notifications\ChargeNotification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function root()
    {
        return view('index');
    }

    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return view('errors.404');
    }

    public function testNotification(Request $request)
    {
        auth()->user()->notify(new ChargeNotification('Hello world', 'Notification Description'));
    }
}
