<?php

namespace App\Http\Controllers\API\V2\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                'read_at' => @$item->read_at ? @$item->read_at->format('d F Y H:i:s') : null,
                'created_at' => @$item->created_at->format('d F Y H:i:s'),
            ];
        }

        return response()->json([
            'data' => $data,
            'message' => 'Get notification data succeess'
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
            'unread_count' => $unreadCount,
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
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
