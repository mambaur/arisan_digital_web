<?php

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Admin\Group\DataGrid\GroupDataGrid;
use App\Http\Controllers\Controller;
use App\Models\ArisanHistory;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(GroupDataGrid $grid, Request $request)
    {
        return $grid->render();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        return view('admin.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'dues' => 'required',
            'periods_date' => 'required',
            'periods_type' => 'required',
        ]);

        Group::create([
            'created_by' => $request->user_id,
            'name' => $request->name,
            'code' => Group::generateUniqueKey(),
            'periods_type' => $request->periods_type,
            'periods_date' => $request->periods_date,
            'dues' => $request->dues ? str_replace(',', '', $request->dues) : 0,
            'target' => $request->target ? str_replace(',', '', $request->target) : 0,
            'notes' => $request->notes,
            'status' => $request->status ? 'active' : 'inactive',
        ]);

        session()->flash('success', 'Group has been successfully created');
        return redirect()->route('groups');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = Group::find($id);
        if (!@$group) return abort(404);

        return view('admin.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'dues' => 'required',
            'periods_date' => 'required',
            'periods_type' => 'required',
        ]);

        $group = Group::find($id);
        if (!@$group) return abort(404);

        $group->created_by = $request->user_id;
        $group->name = $request->name;
        $group->periods_type = $request->periods_type;
        $group->periods_date = $request->periods_date;
        $group->dues = $request->dues ? str_replace(',', '', $request->dues) : 0;
        $group->target = $request->target ? str_replace(',', '', $request->target) : 0;
        $group->notes = $request->notes;
        $group->status = $request->status ? 'active' : 'inactive';
        $group->save();

        session()->flash('success', 'Group has been successfully updated');
        return redirect()->route('groups');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::find($id);
        if (!@$group) return abort(404);

        DB::beginTransaction();
        @$group->delete();
        Member::where('group_id', $id)->delete();
        ArisanHistory::where('group_id', $id)->delete();
        DB::commit();

        session()->flash('success', 'Group has been successfully deleted');
        return redirect()->route('groups');
    }
}
