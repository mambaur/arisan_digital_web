<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $id => User ID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = Group::where('created_by', $request->user()->id)->latest()->get();
        $data = [];
        foreach ($groups as $item) {
            $total_balance = $this->totalBalanceArisan($item);

            $last_paid_members = Member::where('group_id', $item->id)->where('status_paid', 'paid')->latest()->limit(3)->get();

            $members = Member::where('group_id', $item->id)->where('is_get_reward', 0)->latest()->get();

            $total_targets = count($item->members) * $item->dues;
            $total_not_dues = $total_targets - $total_balance;

            $unpaid_member = Member::where('group_id', $item->id)->whereNull('date_paid')->first();
            $get_reward = Member::where('group_id', $item->id)->where('is_get_reward', 0)->first();

            $data[] = [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'periods_type' => $item->periods_type,
                'periods_date' => $item->periods_date->format('Y-m-d'),
                'periods_date_en' => $item->periods_date->format('d F Y'),
                'dues' => $item->dues,
                'target' => $item->target,
                'notes' => $item->notes,
                'status' => $item->status,
                'created_by' => $item->created_by,
                'total_balance' => $total_balance,
                'total_not_dues' => $total_not_dues,
                'last_paid_members' => $last_paid_members,
                'members' => $members,
                'is_shuffle' => $unpaid_member ? false : ($get_reward ? true : false)
            ];
        }

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
     * Display a memberGroup of the resource.
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function memberGroup(Request $request, $id)
    {
        $members = Member::where('group_id', $id)->where('name', 'like', "%$request->q%")->orderBy('name', 'asc')->get();
        $data = [];
        foreach ($members as $item) {
            $data[] = [
                "id" => $item->id,
                "name" => $item->name,
                "no_telp" => $item->no_telp,
                "no_whatsapp" => $item->no_whatsapp,
                "email" => $item->email,
                "gender" => $item->gender,
                "date_paid" => $item->date_paid,
                "status_paid" => $item->status_paid,
                "nominal_paid" => $item->nominal_paid,
                "status_active" => $item->status_active,
                "can_delete" => $item->is_owner ? false : true,
                "is_get_reward" => $item->is_get_reward
            ];
        }

        return response()->json([
            "status" => "success",
            "message" => "Data member group berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'dues' => 'required',
            'periods_type' => 'required',
            'periods_date' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }


        DB::beginTransaction();

        $group = Group::create([
            "name" => $request->name,
            "code" => "_",
            "periods_type" => $request->periods_type,
            "periods_date" => $request->periods_date,
            "dues" => $request->dues,
            "target" => $request->target,
            "created_by" => $request->user()->id,
            "notes" => $request->notes,
        ]);

        $group->code = $group->id . $request->code;
        $group->save();

        Member::create([
            "group_id" => $group->id,
            "name" => $request->user()->name,
            "email" => $request->user()->email,
            "gender" => 'male',
            "status_paid" => 'unpaid',
            "status_active" => 'active',
            "is_owner" => true
        ]);

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Group $request->name berhasil ditambahkan.",
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        $data = [
            'id' => $group->id,
            'name' => $group->name,
            'code' => $group->code,
            'periods_type' => $group->periods_type,
            'periods_date' => $group->periods_date,
            'dues' => $group->dues,
            'target' => $group->target,
            'notes' => $group->notes,
            'status' => $group->status,
            'created_at' => $group->created_at->format('Y-m-d')
        ];

        return response()->json([
            "status" => "success",
            "message" => "Data group berhasil didapatkan.",
            "data" => $data
        ], 200);
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
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'dues' => 'required',
            'periods_type' => 'required',
            'periods_date' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        $group->update([
            "name" => $request->name,
            "periods_type" => $request->periods_type,
            "periods_date" => $request->periods_date,
            "dues" => $request->dues,
            "target" => $request->target,
            // "notes" => $request->notes,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Data group berhasil diupdate.",
        ], 200);
    }

    /**
     * Update status the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        $group->update([
            "status" => $request->status,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Status group berhasil diupdate.",
        ], 200);
    }

    /**
     * Update notes the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNotes(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'notes' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        $group->update([
            "notes" => $request->notes,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Catatan group berhasil diupdate.",
        ], 200);
    }

    /**
     * Update notes the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePeriodsDate(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'periods_date' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        $group->update([
            "periods_date" => $request->periods_date,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Tanggal kocok group arisan berhasil diupdate.",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data group tidak ditemukan.',
                ],
                200
            );
        }

        DB::beginTransaction();

        Member::where('group_id', $id)->delete();

        $group = Group::destroy($id);

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Group berhasil dihapus.",
        ], 200);
    }
}
