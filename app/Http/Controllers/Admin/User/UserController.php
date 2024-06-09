<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Admin\User\DataGrid\UserDataGrid;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(UserDataGrid $grid, Request $request)
    {
        return $grid->render();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserSearchData(Request $request)
    {
        $search = $request->search;
        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })
            ->limit(15)->get();

        $data = [];

        foreach ($users as $item) {
            $data[] =  [
                'id' => $item->id,
                'value' =>
                $item->name .
                    ' (' .
                    $item->email .
                    ')',
            ];
        }

        return $data;
    }
}
