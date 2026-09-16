<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $request->user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    public function unread(Request $request)
    {
        return response()->json($request->user()->unreadNotifications()->latest()->get()->map(fn ($notification) => [
            'id' => $notification->id,
            'message' => $notification->data['message'] ?? 'New service update',
            'url' => $notification->data['url'] ?? route('notifications.index'),
        ]));
    }
}
