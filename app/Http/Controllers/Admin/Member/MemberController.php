<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Admin\Member\DataGrid\MemberDataGrid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MemberDataGrid $grid, Request $request)
    {
        return view('admin.members.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(MemberDataGrid $grid, Request $request)
    {
        return $grid->render();
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
}
