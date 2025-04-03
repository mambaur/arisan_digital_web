<?php

namespace App\Http\Controllers\API\V1\Notifications;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
