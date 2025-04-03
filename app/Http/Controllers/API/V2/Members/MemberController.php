<?php

namespace App\Http\Controllers\API\V2\Members;

use App\Http\Controllers\Controller;
use App\Mail\Remainder;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * List All Members
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::latest()->get();

        return response()->json([
            "status" => "success",
            "message" => "Get data members success.",
            "data" => $members
        ], 200);
    }

    /**
     * Create New Member
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'group_id' => 'required',
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

        Member::create([
            "group_id" => $request->group_id,
            "name" => $request->name,
            "no_telp" => $request->no_telp,
            "no_whatsapp" => $request->no_whatsapp,
            "email" => $request->email,
            "gender" => $request->gender,
            // "date_paid" => $request->date_paid,
            "status_paid" => 'unpaid',
            // "nominal_paid" => $request->nominal_paid,
            "status_active" => 'active',
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Member baru berhasil ditambahkan.",
        ], 200);
    }

    /**
     * Detail Member
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        return response()->json([
            "status" => "success",
            "message" => "Get data member detail success.",
            "data" => [
                "id" => $id,
                "group" => [
                    "id" => $member->group->id,
                    "name" => $member->group->name,
                    'code' => $member->group->code,
                    'periods_type' => $member->group->periods_type,
                    'periods_date' => $member->group->periods_date,
                    'dues' => $member->group->dues,
                    'target' => $member->group->target,
                    'notes' => $member->group->notes,
                    'status' => $member->group->status,
                ],
                "name" => $member->name,
                "no_telp" => $member->no_telp,
                "no_whatsapp" => $member->no_whatsapp,
                "email" => $member->email,
                "gender" => $member->gender,
                "date_paid" => $member->date_paid,
                "status_paid" => $member->status_paid,
                "nominal_paid" => $member->nominal_paid,
                "status_active" => $member->status_active,
            ]
        ], 200);
    }

    /**
     * Update Member
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
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

        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        $member->update([
            "name" => $request->name,
            "no_telp" => $request->no_telp,
            "no_whatsapp" => $request->no_whatsapp,
            "gender" => $request->gender,
            "email" => $request->email,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Data anggota berhasil diupdate.",
        ], 200);
    }

    /**
     * Update Member Status Paid
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatusPaid(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            // 'date_paid' => 'required',
            'status_paid' => 'required',
            // 'nominal_paid' => 'required',
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

        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        $member->update([
            "date_paid" => $request->date_paid,
            "status_paid" => $request->status_paid,
            // "nominal_paid" => $request->nominal_paid,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Status anggota berhasil diupdate.",
        ], 200);
    }

    /**
     * Update Member Activity Status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatusActive(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'status_active' => 'required',
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

        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        $member->update([
            "status_active" => $request->status_active,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Status aktif anggota berhasil diupdate.",
        ], 200);
    }

    /**
     * Reset Member Paid Status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetStatusPaid($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        $member->update([
            "date_paid" => null,
            "status_paid" => 'unpaid',
            "nominal_paid" => null,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Status anggota berhasil direset.",
        ], 200);
    }

    /**
     * Remove Member
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data anggota tidak ditemukan.',
                ],
                200
            );
        }

        $member = Member::destroy($id);

        return response()->json([
            "status" => "success",
            "message" => "Anggota berhasil dihapus.",
        ], 200);
    }

    /**
     * Send Mail Reminder to Member
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $group_id
     * @return \Illuminate\Http\Response
     */
    public function mailReminder($group_id)
    {
        $members = Member::where('group_id', $group_id)->whereNull('date_paid')->get();

        if (!count($members)) {
            return response()->json([
                "status" => "failed",
                "message" => "Tidak ada email yang harus dikirimkan.",
            ], 200);
        }

        $data = [];
        foreach ($members as $item) {
            $data[] = $item->email;

            // Mail::to($item->email)->send(new Remainder($item->group, $item));
            try {
                Mail::to($item->email)->send(new Remainder($item->group, $item));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Email berhasil di kirim.",
            "data" => $data,
        ], 200);
    }
}
