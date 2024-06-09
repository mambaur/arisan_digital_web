<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $user = User::find($id);
        if (!@$user) return abort(404);

        $user->name = $request->name;
        $user->save();

        session()->flash('success', 'Your profile has been successfully updated');
        return redirect()->route('profile');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePassword(Request $request, string $id)
    {
        $request->validate(
            [
                'password' => ['required', 'string', 'min:4', 'confirmed'],
                'password_confirmation' => ['required'],
            ],
        );

        $user = User::find($id);
        if (!@$user) return abort(404);

        $user->password = Hash::make($request->password);
        $user->save();

        session()->flash('success', 'Your password has been successfully updated');
        return redirect()->route('profile');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
