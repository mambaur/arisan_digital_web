<?php

namespace App\Http\Controllers\API\V2\Groups;


use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupOwner;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * List Groups
     *
     * @param  int  $id => User ID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => ['nullable'],
            'limit' => ['nullable'],
            'type' => ['nullable', 'string'], // invitation | owned | null
        ]);

        $groups = Group::latest();

        $user_id = $request->user()->id;
        $email = $request->user()->email;

        if($request->type == 'owned'){
            $groups->with(['owners'])->whereHas('owners', function($query) use($user_id){
                $query->where('user_id', $user_id);
            });;
        }else if($request->type == 'invitation'){
            $groups->with(['members'])->whereHas('members', function ($query) use ($email) {
                $query->where('email', $email)->where('status_active', 'pending');
            });
        }else{
            $groups->with(['members'])->whereHas('members', function ($q) use ($email) {
                $q->where('email', $email);
            });
        }

        $groups = $groups->paginate($request->limit ?? 10);

        $data = [];
        foreach ($groups as $item) {
            $total_member = $item->members()->select('id')->where('status_active', 'active')->count();
            $total_winner = $item->members()->select('id')->where('status_active', 'active')->where('is_get_reward', 1)->count();
            $member = $item->members()->select('id')->where('user_id', $user_id)->first();

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
                'total_member' => $total_member,
                'total_winner' => $total_winner,
                'created_by' => $item->created_by,
                'is_owned' => in_array($user_id, $item->owners()->pluck('user_id')->toArray()),
                'user_member_id' => @$member->id,
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
        return $group->members()
            ->where('status_paid', 'paid')
            ->count() * $group->dues;
    }

    
    /**
     * Generate Owners Groups
     */
    public function generateOwner(Request $request)
    {
        $groups = Group::get();
        foreach ($groups as $item) {
            $owner = GroupOwner::where('group_id', $item->id)->where('user_id', $item->created_by)->first();

            if(!$owner){
                GroupOwner::create([
                    'group_id' => $item->id,
                    'user_id' => $item->created_by,
                ]);
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Generate group owner success",
        ], 200);
    }
    
    /**
     * Generate UserID Members
     */
    public function generateUserIdMembers(Request $request)
    {
        $members = Member::get();
        foreach ($members as $item) {
            if($item->user_id){
                continue;
            }

            $user = User::where('email', $item->email)->first();

            if($user){
                $item->user_id = $user->id;
                $item->save();
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Generate user id member success",
        ], 200);
    }


    /**
     * List Group Members
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function memberGroup(Request $request, $id)
    {
        $members = Member::where('group_id', $id)->where('name', 'like', "%$request->q%")->orderBy('name', 'asc')->get();
        $data = [];
        $ownerIds = Group::find($id)->owners()->pluck('user_id')->toArray();
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
            "message" => "Data member group berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    /**
     * Create New Group
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
            "code" => Group::generateUniqueKey(),
            "periods_type" => $request->periods_type,
            "periods_date" => $request->periods_date,
            "dues" => $request->dues,
            "target" => $request->target,
            "created_by" => $request->user()->id,
            "notes" => $request->notes,
        ]);

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

        GroupOwner::create([
            'user_id' => $request->user()->id,
            'group_id' => $group->id,
        ]);

        DB::commit();

        return response()->json([
            "status" => "success",
            "message" => "Group $request->name berhasil ditambahkan.",
        ], 200);
    }

    /**
     * Detail Group
     *
     * @param  int  $id => Group ID
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
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

        $total_balance = $this->totalBalanceArisan($group);

        $total_targets = count($group->members) * $group->dues;
        $total_not_dues = $total_targets - $total_balance;

        $unpaid_member = $group->members()->where('group_id', $group->id)->whereNull('date_paid')->first();
        $get_reward = $group->members()->where('group_id', $group->id)->where('is_get_reward', 0)->first();
        $total_member = $group->members()->select('id')->where('status_active', 'active')->count();
        $total_winner = $group->members()->select('id')->where('status_active', 'active')->where('is_get_reward', 1)->count();

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
            'is_owned' => in_array($request->user()->id, $group->owners()->pluck('user_id')->toArray()),
            'total_balance' => $total_balance,
            'total_not_dues' => $total_not_dues,
            'total_member' => $total_member,
            'total_winner' => $total_winner,
            'is_shuffle' => $unpaid_member ? false : ($get_reward ? true : false),
            'created_at' => $group->created_at->format('Y-m-d'),
        ];

        return response()->json([
            "status" => "success",
            "message" => "Data group berhasil didapatkan.",
            "data" => $data
        ], 200);
    }

    /**
     * Update Group
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
     * Update Group Status
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
     * Update Group Notes
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
     * Update Group Period Date
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
     * Remove Group
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