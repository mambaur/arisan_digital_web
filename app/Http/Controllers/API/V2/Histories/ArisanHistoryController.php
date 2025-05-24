<?php

namespace App\Http\Controllers\API\V2\Histories;

use App\Http\Controllers\Controller;
use App\Models\ArisanHistory;
use App\Models\ArisanHistoryDetail;
use App\Models\ArisanHistoryWinner;
use App\Models\Group;
use App\Models\Member;
use App\Notifications\ArisanNotification;
use App\NotificationType\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArisanHistoryController extends Controller
{
    /**
     * List Arisan Histories
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $arisan_histories = ArisanHistory::with([
            'member' => function ($query) {
                $query->withTrashed();
            }
        ])->where('group_id', $id)->latest()->get();

        $data = [];
        foreach ($arisan_histories as $item) {
            $history_details = [];
            foreach (@$item->arisanHistoryDetails ?? [] as $row) {
                $history_details[] = [
                    "id" => $row->id,
                    "member" => @$row->member ? [
                        "id" => @$row->member->id,
                        "name" => @$row->member->name,
                        "no_telp" => @$row->member->no_telp,
                        "email" => @$row->member->email,
                        "gender" => @$row->member->gender
                    ] : null,
                    "status_paid" => $row->status_paid,
                    "nominal_paid" => $row->nominal_paid,
                    "date_paid" => $row->date_paid->format('d F Y')
                ];
            }

            $winners = [];
            foreach (@$item->winners ?? [] as $row) {
                if(@$row->member){
                    $winners[] = [
                        "id" => @$row->member->id,
                        "name" => @$row->member->name,
                        "no_telp" => @$row->member->no_telp,
                        "email" => @$row->member->email,
                        "gender" => @$row->member->gender
                    ];
                }
            }

            $data[] = [
                "id" => $item->id,
                "winner" => @$item->member ? [
                    "id" => @$item->member->id,
                    "name" => @$item->member->name,
                    "no_telp" => @$item->member->no_telp,
                    "email" => @$item->member->email,
                    "gender" => @$item->member->gender
                ] : null,
                "winners" => $winners,
                "date" => $item->date->format('d F Y'),
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
     * Create New Arisan History (Delete Soon)
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

        $group = Group::find($request->group_id);
        $periods_day = 7; // default weekly
        if ($group->periods_type == 'monthly') {
            $periods_day = 30; // per bulan
        }
        if ($group->periods_type == 'annual') {
            $periods_day = 365; // per tahun
        }
        $group->periods_date = $group->periods_date->addDays($periods_day);
        $group->save();

        Member::find($request->member_id)->update([
            'is_get_reward' => 1
        ]);

        $members = Member::where('group_id', $arisan_history->group_id)->get();
        foreach ($members as $item) {
            ArisanHistoryDetail::create([
                "arisan_history_id" => $arisan_history->id,
                "member_id" => $item->id,
                "status_active" => $item->status_active,
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
     * Init Arisan History Winner
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initWinner(Request $request)
    {
        DB::beginTransaction();

        $arisan_histories = ArisanHistory::doesntHave('winners')->paginate(1000);

        foreach ($arisan_histories ?? [] as $item) {
            ArisanHistoryWinner::create([
                'arisan_history_id' => $item->id,
                'member_id' => $item->member_id,
            ]);
        }

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Init winner berhasil: ". count($arisan_histories ?? []),
        ], 200);
    }

    /**
     * Create New Arisan History
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWinner(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group_id' => 'required',
            'member_ids' => 'required|array',
            'member_ids.*' => 'required',
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

        $member_ids = @$request->member_ids ?? [];

        DB::beginTransaction();

        $arisan_history = ArisanHistory::create([
            "group_id" => $request->group_id,
            // "member_id" => $request->member_id,
            "date" => $request->date,
            "notes" => $request->notes
        ]);

        $group = Group::find($request->group_id);
        $periods_day = 7; // default weekly
        if ($group->periods_type == 'monthly') {
            $periods_day = 30; // per bulan
        }
        if ($group->periods_type == 'annual') {
            $periods_day = 365; // per tahun
        }
        $group->periods_date = $group->periods_date->addDays($periods_day);
        $group->save();

        foreach ($member_ids as $member_id) {
            ArisanHistoryWinner::create([
                'arisan_history_id' => $arisan_history->id,
                'member_id' => $member_id,
            ]);

            $member_winner = Member::find($member_id);
            if(@$member_winner){
                @$member_winner->update([
                    'is_get_reward' => 1
                ]);

                if(@$member_winner->user){
                    try {
                        $data = [
                            'member' => $member_winner,
                            'group' => @$group,
                        ];
                        $member_winner->user->notify(new ArisanNotification("Selamat! Kamu menang arisan! 🎉", "Kocokan di grup $group->name sudah selesai, dan kamu keluar sebagai pemenangnya. Siap-siap terima dana arisan ya!", NotificationType::GROUP_WINNER, $data));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

        }

        $members = Member::where('group_id', $arisan_history->group_id)->get();
        foreach ($members as $item) {
            ArisanHistoryDetail::create([
                "arisan_history_id" => $arisan_history->id,
                "member_id" => $item->id,
                "status_active" => $item->status_active,
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
     * Detail Arisan History
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $arisan_history = ArisanHistory::with([
            'member' => function ($query) {
                $query->withTrashed();
            }
        ])->where('id', $id)->first();
    
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
        foreach (@$arisan_history->arisanHistoryDetails ?? [] as $row) {
            $history_details[] = [
                "id" => $row->id,
                "member" => @$row->member ? [
                    "id" => @$row->member->id,
                    "name" => @$row->member->name,
                    "no_telp" => @$row->member->no_telp,
                    "email" => @$row->member->email,
                    "gender" => @$row->member->gender
                ] : null,
                "status_paid" => $row->status_paid,
                "status_active" => $row->status_active,
                "nominal_paid" => $row->nominal_paid,
                "date_paid" => $row->date_paid->format('d F Y')
            ];
        }

        

        $winners = [];
        foreach (@$arisan_history->winners ?? [] as $row) {
            if(@$row->member){
                $winners[] = [
                    "id" => @$row->member->id,
                    "name" => @$row->member->name,
                    "no_telp" => @$row->member->no_telp,
                    "email" => @$row->member->email,
                    "gender" => @$row->member->gender
                ];
            }
        }

        $data = [
            "id" => $arisan_history->id,
            "winner" => @$arisan_history->member ? [
                "id" => @$arisan_history->member->id,
                "name" => @$arisan_history->member->name,
                "no_telp" => @$arisan_history->member->no_telp,
                "email" => @$arisan_history->member->email,
                "gender" => @$arisan_history->member->gender
            ] : null,
            "winners" => $winners,
            "date" => $arisan_history->date->format('d F Y'),
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
     * Delete Arisan History
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
        ArisanHistoryWinner::where('arisan_history_id', $id)->delete();
        $arisan_history->delete();

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Data riwayat arisan berhasil dihapus.",
        ], 200);
    }
}
