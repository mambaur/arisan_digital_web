<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ArisanHistory;
use App\Models\ArisanHistoryDetail;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArisanHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $arisan_histories = ArisanHistory::where('group_id', $id)->latest()->get();
        $data = [];
        foreach ($arisan_histories as $item) {
            $history_details = [];
            foreach ($item->arisanHistoryDetails as $row) {
                $history_details[] = [
                    "id" => $row->id,
                    "member" => [
                        "id" => $row->member->id,
                        "name" => $row->member->name,
                        "no_telp" => $row->member->no_telp,
                        "email" => $row->member->email
                    ],
                    "status_paid" => $row->status_paid,
                    "nominal_paid" => $row->nominal_paid,
                    "date_paid" => $row->date_paid
                ];
            }

            $data[] = [
                "id" => $item->id,
                "winner" => [
                    "id" => $item->member->id,
                    "name" => $item->member->name,
                    "no_telp" => $item->member->no_telp,
                    "email" => $item->member->email
                ],
                "date" => $item->date,
                "notes" => $item->notes,
                "arisan_history_details" => $history_details
            ];
        }

        return response()->json([
            "status" => "success",
            "message" => "Data riwayat arisan berhasil didapatkan.",
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
            'group_id' => 'required',
            'member_id' => 'required',
            'date' => 'required',
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

        $arisan_history = ArisanHistory::create([
            "group_id" => $request->group_id,
            "member_id" => $request->member_id,
            "date" => $request->date,
            "notes" => $request->notes
        ]);

        Member::find($request->member_id)->update([
            'is_get_reward' => 1
        ]);

        $members = Member::where('group_id', $arisan_history->group_id)->get();
        foreach ($members as $item) {
            ArisanHistoryDetail::create([
                "arisan_history_id" => $arisan_history->id,
                "member_id" => $item->id,
                "status_paid" => $item->status_paid,
                "nominal_paid" => $item->nominal_paid,
                "date_paid" => $item->date_paid
            ]);

            $item->update([
                "date_paid" => null,
                "status_paid" => 'unpaid',
                "nominal_paid" => null,
            ]);
        }

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Hasil pemenang arisan berhasil disimpan.",
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $arisan_history = ArisanHistory::find($id);
        if (!$arisan_history) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data riwayat arisan tidak ditemukan.',
                ],
                200
            );
        }

        $history_details = [];
        foreach ($arisan_history->arisan_history_details as $row) {
            $history_details[] = [
                "id" => $row->id,
                "member" => [
                    "id" => $row->member->id,
                    "name" => $row->member->name,
                    "no_telp" => $row->member->no_telp,
                    "email" => $row->member->email
                ],
                "status_paid" => $row->status_paid,
                "nominal_paid" => $row->nominal_paid,
                "date_paid" => $row->date_paid
            ];
        }

        $data = [
            "id" => $arisan_history->id,
            "winner" => [
                "id" => $arisan_history->member->id,
                "name" => $arisan_history->member->name,
                "no_telp" => $arisan_history->member->no_telp,
                "email" => $arisan_history->member->email
            ],
            "date" => $arisan_history->date,
            "notes" => $arisan_history->notes,
            "arisan_history_details" => $history_details
        ];

        return response()->json([
            "status" => "success",
            "message" => "Data riwayat arisan berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $arisan_history = ArisanHistory::find($id);
        if (!$arisan_history) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Data riwayat arisan tidak ditemukan.',
                ],
                200
            );
        }

        DB::beginTransaction();

        ArisanHistoryDetail::where('arisan_history_id', $id)->delete();
        $arisan_history->delete();

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Data riwayat arisan berhasil dihapus.",
        ], 200);
    }
}
