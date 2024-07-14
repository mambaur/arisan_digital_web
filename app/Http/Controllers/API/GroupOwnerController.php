<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupOwnerController extends Controller
{
    public function initGroupOwner()
    {
        $groups = Group::paginate(50);
        foreach ($groups as $item) {
            if (!count($item->owners ?? [])) {
                GroupOwner::create([
                    'group_id' => $item->id,
                    'user_id' => $item->created_by,
                    'status_approval' => 'approved'
                ]);
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Pemilik groub berhasil di inisialisasi",
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $group_owners = GroupOwner::where('group_id', $id)->latest()->get();

        $data = [];
        foreach ($group_owners as $item) {
            $data[] = [
                'group_owner_id' => $item->id,
                'status' => $item->status_approval,
                'user' => @$item->user
            ];
        }

        return response()->json(
            ['data' => $data],
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group_id' => 'required',
            'email' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                400
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!@$user) {
            return response()->json([
                "message" => "User tidak ditemukan"
            ], 404);
        }

        GroupOwner::create([
            'group_id' => $request->group_id,
            'user_id' => $user->id,
            'status_approval' => 'requested'
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Permintaan pemilik group berhasil dikirimkan ke $request->email.",
        ], 200);
    }

    public function updateStatus($id, Request $request)
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
                400
            );
        }

        if (!in_array(strtolower($request->status), ['approved', 'rejected', 'requested'])) {
            return response()->json([
                "message" => "Status yang dikirimkan tidak sesuai"
            ], 404);
        }

        $group_owner = GroupOwner::find($id);

        if (!@$group_owner) {
            return response()->json([
                "message" => "Pemilik group tidak ditemukan"
            ], 404);
        }

        $group_owner->status_approval = strtolower($request->status);
        $group_owner->save();

        return response()->json([
            "status" => "success",
            "message" => "Status pemilik group berhasil diubah.",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group_owner = GroupOwner::find($id);

        if (!@$group_owner) {
            return response()->json([
                "message" => "Pemilik group tidak ditemukan"
            ], 404);
        }

        $group_owner->delete();

        return response()->json([
            "status" => "success",
            "message" => "Pemilik group berhasil dihapus.",
        ], 200);
    }
}
