<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['unread' => ['nullable', 'boolean']]);
        $notifications = $request->boolean('unread') ? $request->user()->unreadNotifications() : $request->user()->notifications();

        return response()->json(['data' => $notifications->latest()->paginate(30)]);
    }

    public function markRead(Request $request, string $notification)
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return response()->json(['data' => $item->fresh()]);
    }
}
