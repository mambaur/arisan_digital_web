<?php

namespace App\Http\Controllers\API\V2\Notifications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * List Notifications
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(10);

        $data = [];
        foreach ($notifications as $item) {
            $data[] = [
                'title' => @$item->data['title'],
                'description' => @$item->data['description'],
                'type' => @$item->data['type'],
                'resource' => @$item->data['resource'],
                'read_at' => @$item->read_at ? @$item->read_at->format('d F Y H:i') : null,
                'created_at' => @$item->created_at->format('d F Y H:i'),
            ];
        }

        return response()->json([
            'data' => $data,
            'message' => 'Get notification data success'
        ], 200);
    }

    /**
     * Count Unread Notifications
     */
    public function count()
    {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'data' => ['total' => $unreadCount],
            'message' => 'Get unread notification count success'
        ], 200);
    }

    /**
     * Mark All Notifications as Read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Semua notifikasi telah ditandai sebagai terbaca'
        ], 200);
    }

    /**
     * Test notification
     */
    public function test(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'title' => 'nullable',
            'description' => 'nullable',
            'data' => 'nullable',
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
        if(!$user){
            return abort(404, "Penguna tidak ditemukan");
        }

        $user->notify(new TestNotification($request->title ?? "Test Notifikasi", $request->description ?? "Deskripsi Test Notifikasi", $request->data ?? null));

        return response()->json([
            'message' => 'Test notifikasi berhasil dikirimkan'
        ], 200);
    }
}
