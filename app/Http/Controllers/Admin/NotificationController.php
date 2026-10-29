<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Cet espace est réservé aux administrateurs.');

        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(4)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Cet espace est réservé aux administrateurs.');

        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();

        return back()->with('status', 'Notification marquée comme lue.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Cet espace est réservé aux administrateurs.');

        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Request $request, string $notificationId): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Cet espace est réservé aux administrateurs.');

        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->delete();

        return back()->with('status', 'Notification supprimée.');
    }
}