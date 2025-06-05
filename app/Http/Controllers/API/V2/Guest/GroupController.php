<?php

namespace App\Http\Controllers\API\V2\Guest;

use App\Constants\MemberStatusActive;
use App\Constants\MemberStatusPaid;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Detail Guest Group.
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

        $members = Member::where('group_id', $group->id)->where('status_active', MemberStatusActive::ACTIVE)->latest()->get();

        $total_targets = count($group->members()->where('status_active', MemberStatusActive::ACTIVE)->whereIn('status_paid', [MemberStatusPaid::UNPAID, MemberStatusPaid::PAID])->get()) * $group->dues;
        $total_not_dues = $total_targets - $total_balance;

        $unpaid_member = Member::where('group_id', $group->id)->where('status_active', MemberStatusActive::ACTIVE)->where('status_paid', MemberStatusPaid::UNPAID)->first();
        $get_reward = Member::where('group_id', $group->id)->where('status_active', MemberStatusActive::ACTIVE)->where('status_paid', MemberStatusPaid::PAID)->where('is_get_reward', 0)->first();

        $data = [
            'id' => $group->id,
            'name' => $group->name,
            'code' => $group->code,
            'periods_type' => $group->periods_type,
            'periods_date' => $group->periods_date->format('Y-m-d'),
            'periods_date_en' => $group->periods_date->format('d F Y'),
            'dues' => (int) $group->dues,
            'target' => (int) $group->target,
            'notes' => $group->notes,
            'status' => $group->status,
            'created_by' => (int) $group->created_by,
            'total_balance' => (int) $total_balance,
            'total_not_dues' => (int) $total_not_dues,
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
        return $group->members()
            ->where('status_active', MemberStatusActive::ACTIVE)
            ->where('status_paid', MemberStatusPaid::PAID)
            ->count() * $group->dues;
    }
}
