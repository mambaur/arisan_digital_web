<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::latest()->paginate(10);

        return response()->json([
            "status" => "success",
            "message" => "Get data members success.",
            "data" => $members
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
            // "date_paid" => $request->date_paid,
            "status_paid" => 'unpaid',
            // "nominal_paid" => $request->nominal_paid,
            "status_active" => 'active',
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Member baru berhasil ditambahkan.",
        ], 201);
    }

    /**
     * Display the specified resource.
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
                "group_id" => $member->group_id,
                "name" => $member->name,
                "no_telp" => $member->no_telp,
                "no_whatsapp" => $member->no_whatsapp,
                "email" => $member->email,
                "date_paid" => $member->date_paid,
                "status_paid" => $member->status_paid,
                "nominal_paid" => $member->nominal_paid,
                "status_active" => $member->status_active,
            ]
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
            "email" => $request->email,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Data anggota berhasil diupdate.",
        ], 201);
    }

    /**
     * Update status paid the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatusPaid(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'date_paid' => 'required',
            'status_paid' => 'required',
            'nominal_paid' => 'required',
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
            "nominal_paid" => $request->nominal_paid,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Status anggota berhasil diupdate.",
        ], 201);
    }

    /**
     * Reset status paid the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetStatusPaid(Request $request, $id)
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
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
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
}
