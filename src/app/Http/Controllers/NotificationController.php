<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->appNotifications()
            ->paginate(20);

        return view('user.notifications', compact('notifications'));
    }

    public function read(Request $request, AppNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->update(['read_at' => now()]);

        return back();
    }
}
