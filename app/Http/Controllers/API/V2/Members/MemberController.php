<?php

namespace App\Http\Controllers\API\V2\Members;

use App\Constants\MemberStatusActive;
use App\Constants\MemberStatusPaid;
use App\Constants\NotificationType;
use App\Http\Controllers\Controller;
use App\Mail\Remainder;
use App\Models\Group;
use App\Models\Member;
use App\Models\User;
use App\Notifications\ArisanNotification;
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
    public function index(Request $request, $group_id)
    {
        $validate = Validator::make($request->all(), [
            'page' => 'nullable',
            'limit' => 'nullable',
            'status_active' => 'nullable',
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

        $members = Member::where('group_id', $group_id)->latest()->orderBy('name', 'asc');

        if($request->status_active){
            $members->where('status_active', $request->status_active);
        }

        $members = $members->paginate($request->limit ?? 10);

        $data = [];
        $ownerIds = Group::find($group_id)->owners()->pluck('user_id')->toArray();
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
                "status" => $item->status_active,
                "nominal_paid" => $item->nominal_paid,
                "status_active" => $item->status_active,
                "can_delete" => !in_array($item->user_id, $ownerIds),
                "is_get_reward" => $item->is_get_reward
            ];
        }

        return response()->json([
            "status" => "success",
            "message" => "Get data members success.",
            "data" => $data
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
            'no_telp' => 'nullable',
            'no_whatsapp' => 'nullable',
            'email' => 'nullable',
            'gender' => 'nullable',
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
            "status_paid" => MemberStatusPaid::UNPAID,
            "status_active" => MemberStatusActive::ACTIVE,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Member baru berhasil ditambahkan.",
        ], 200);
    }
    
    /**
     * Create New Member by User Code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeByUserCode(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_code' => 'required',
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

        $user = User::where('code', $request->user_code)->first();
        if(!$user) return abort(404, 'Pengguna tidak ditemukan');

        $member = Member::where('user_id', $user->id)->where('group_id', $request->group_id)->first();
        if($member) return abort(400, 'Anggota sudah didaftarkan');

        $member = Member::create([
            "group_id" => $request->group_id,
            "user_id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "status_paid" => MemberStatusPaid::UNPAID,
            "status_active" => MemberStatusActive::REQUEST_INVITATION,
        ]);

        try {
            $data = [
                'member' => $member,
                'group' => @$member->group,
            ];
            $user_sender = auth()->user();
            $user->notify(new ArisanNotification("Kamu diajak gabung arisan nih!", "$user_sender->name ngajak kamu masuk ke grup arisan {$member->group->name}. Mau ikutan nggak? ", NotificationType::MEMBER_INVITATION_REQUEST, $data));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            "status" => "success",
            "message" => "Member baru berhasil ditambahkan. Permintaan anggota telah dikirimkan",
        ], 200);
    }
    
    /**
     * Create New Member by Group Code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeByGroupCode(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group_code' => 'required',
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

        $user = auth()->user();
        $group = Group::where('code', $request->group_code)->first();
        if(!$group) return abort(404, 'Grub tidak ditemukan');

        $member = Member::where('user_id', $user->id)->where('group_id', $group->id)->first();
        if($member) return abort(400, 'Anda sudah didaftarkan di grub');

        $member = Member::create([
            "group_id" => $group->id,
            "user_id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "status_paid" => MemberStatusPaid::UNPAID,
            "status_active" => MemberStatusActive::REQUEST_JOIN,
        ]);

        try {
            $data = [
                'member' => $member,
                'group' => @$group,
            ];

            foreach ($group->owners() ?? [] as $item) {
                $item->user->notify(new ArisanNotification("Boleh gabung nggak nih?", "$user->name mau jadi bagian dari arisan $group->name. Cek dulu dan kasih keputusan, ya", NotificationType::MEMBER_JOIN_REQUEST, $data));
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            "status" => "success",
            "message" => "Permintaan anggota member anda berhasil dikirimkan, mohon tunggu pengelola arisan menyetujui permintaan anda.",
        ], 200);
    }
    
    /**
     * Create New Member by User Email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeByUserEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
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

        $user = User::where('email', $request->email)->first();
        if(!$user) return abort(404, 'Pengguna tidak ditemukan');

        $member = Member::where('user_id', $user->id)->where('group_id', $request->group_id)->first();
        if($member) return abort(400, 'Anggota sudah didaftarkan');

        $member = Member::create([
            "group_id" => $request->group_id,
            "user_id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "status_paid" => MemberStatusPaid::UNPAID,
            "status_active" => MemberStatusActive::REQUEST_INVITATION,
        ]);

        try {
            $data = [
                'member' => $member,
                'group' => @$member->group,
            ];
            $user_sender = auth()->user();
            $user->notify(new ArisanNotification("Kamu diajak gabung arisan nih!", "$user_sender->name ngajak kamu masuk ke grup arisan {$member->group->name}. Mau ikutan nggak? ", NotificationType::MEMBER_INVITATION_REQUEST, $data));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            "status" => "success",
            "message" => "Member baru berhasil ditambahkan. Permintaan anggota telah dikirimkan",
        ], 200);
    }

    /**
     * Count Member
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function countMember(Request $request, $group_id)
    {
        $validate = Validator::make($request->all(), [
            'status_active' => 'nullable',
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

        $count = Member::where('group_id', $group_id);
        if($request->status_active){
            $count->where('status_active', $request->status_active);
        }

        $count = $count->count();

        return response()->json([
            "status" => "success",
            "message" => "Total anggota berhasil didapatkan.",
            "data" => [
                "total" => $count,
            ],
        ]);
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
            // "email" => $request->email,
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
            'date_paid' => 'nullable',
            'status_paid' => "required|in:" . MemberStatusPaid::validation(),
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
            'status_active' => 'required|in:' . MemberStatusActive::validation(),
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
        $previous_status = $member->status_active;

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

        if(MemberStatusActive::isRequest($previous_status)){
            try {
                $data = [
                    'member' => $member,
                    'group' => @$member->group,
                    'status_active' => $request->status_active,
                ];

                $title = '';
                $description = '';

                if($request->status_active == MemberStatusActive::ACTIVE){
                    $title = "Sip, $member->name sudah join!";
                    $description = "$member->name sudah gabung di grup arisan {$member->group->name}. Semoga makin seru ya!";
                }

                if($request->status_active == MemberStatusActive::REJECT){
                    $title = "Yah, $member->name batal gabung!";
                    $description = "Sayang banget, $member->name belum bisa gabung ke grup {$member->group->name}.";
                }
                
                foreach ($member->group->owners() ?? [] as $item) {
                    $item->user->notify(new ArisanNotification($title, $description, NotificationType::MEMBER_JOIN_RESPONSE, $data));
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

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
            "status_paid" => MemberStatusPaid::UNPAID,
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

        $user = $member->user;
        $temp_member = $member;
        $temp_group = $member->group;
        $group_name = @$member->group->name;

        $member = Member::destroy($id);

        try {
            $data = [
                'member' => $temp_member,
                'group' => $temp_group,
            ];
            $user->notify(new ArisanNotification("Kamu sudah dikeluarkan dari grup arisan", "Pengelola grup $group_name sudah mengeluarkan kamu dari keanggotaan. Kalau ada pertanyaan, coba hubungi pengelola, ya!", NotificationType::MEMBER_REMOVAL, $data));
        } catch (\Throwable $th) {
            //throw $th;
        }

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
    
    /**
     * Send Payment Notification Reminder to Member
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $group_id
     * @return \Illuminate\Http\Response
     */
    public function paymentNotificationReminder($group_id)
    {
        $members = Member::where('group_id', $group_id)->where('status_active', MemberStatusActive::ACTIVE)->whereNull('date_paid')->get();

        if (!count($members)) {
            return response()->json([
                "status" => "failed",
                "message" => "Tidak ada anggota yang harus ditagih.",
            ], 200);
        }

        $data = [];
        foreach ($members as $item) {
            try {
                $data = [
                    'member' => $item,
                    'group' => @$item->group,
                ];
                $item->user->notify(new ArisanNotification("Yuk, bayar iuran arisannya!", "Jangan lupa bayar iuran arisan di grup {$item->group->name} ya. Biar arisannya tetap lancar dan tepat waktu!", NotificationType::MEMBER_PAYMENT_REMINDER, $data));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Notifikasi penagihan pembayaran iuran arisan berhasil di kirim.",
        ], 200);
    }
    
    /**
     * Send Invitation Notification Reminder to Member
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $group_id
     * @return \Illuminate\Http\Response
     */
    public function invitationNotificationReminder($member_id)
    {
        $member = Member::find($member_id);

        if (!$member) {
            return response()->json([
                "status" => "failed",
                "message" => "Anggota tidak ditemukan.",
            ], 400);
        }

        try {
            $data = [
                'member' => $member,
                'group' => @$member->group,
            ];
            $member->user->notify(new ArisanNotification("Yuk, bayar iuran arisannya!", "Jangan lupa bayar iuran arisan di grup {$member->group->name} ya. Biar arisannya tetap lancar dan tepat waktu!", NotificationType::MEMBER_INVITATION_REMINDER, $data));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            "status" => "success",
            "message" => "Notifikasi undangan anggota arisan berhasil di kirim.",
        ], 200);
    }
}
