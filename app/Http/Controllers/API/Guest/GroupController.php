<?php

namespace App\Http\Controllers\API\Guest;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($code)
    {
        $group = Group::where('code', $code)->first();
        if (!$group) {
            return response()->json([
                "status" => "failed",
                "message" => "Data group tidak ditemukan.",
            ], 200);
        }

        $data = [];
        $total_balance = $this->totalBalanceArisan($group);

        $members = Member::where('group_id', $group->id)->latest()->get();

        $total_targets = count($group->members) * $group->dues;
        $total_not_dues = $total_targets - $total_balance;

        $unpaid_member = Member::where('group_id', $group->id)->whereNull('date_paid')->first();
        $get_reward = Member::where('group_id', $group->id)->where('is_get_reward', 0)->first();

        $data = [
            'id' => $group->id,
            'name' => $group->name,
            'code' => $group->code,
            'periods_type' => $group->periods_type,
            'periods_date' => $group->periods_date->format('Y-m-d'),
            'periods_date_en' => $group->periods_date->format('d F Y'),
            'dues' => $group->dues,
            'target' => $group->target,
            'notes' => $group->notes,
            'status' => $group->status,
            'created_by' => $group->created_by,
            'total_balance' => $total_balance,
            'total_not_dues' => $total_not_dues,
            'members' => $members,
            'is_shuffle' => $unpaid_member ? false : ($get_reward ? true : false)
        ];

        return response()->json([
            "status" => "success",
            "message" => "Data group berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    private function totalBalanceArisan(Group $group)
    {
        $total = 0;
        foreach ($group->members as $item) {
            if ($item->status_paid == 'paid') {
                $total += $group->dues;
            }
        }

        return $total;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
