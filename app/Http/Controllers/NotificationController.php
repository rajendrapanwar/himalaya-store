<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $admin = $request->routeIs('admin.*');

        return view('notifications.index', [
            'notifications' => $notifications,
            'admin' => $admin,
            'routePrefix' => $admin ? 'admin.notifications' : 'notifications',
        ]);
    }

    public function update(Request $request, string $notification)
    {
        $item = $this->notification($request, $notification);
        $item->markAsRead();

        if ($request->boolean('open') && filled($item->data['action_url'] ?? null)) {
            return redirect()->to($item->data['action_url']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $notification)
    {
        $this->notification($request, $notification)->delete();

        return back()->with('success', 'Notification removed.');
    }

    private function notification(Request $request, string $id): DatabaseNotification
    {
        return $request->user()->notifications()->whereKey($id)->firstOrFail();
    }
}
