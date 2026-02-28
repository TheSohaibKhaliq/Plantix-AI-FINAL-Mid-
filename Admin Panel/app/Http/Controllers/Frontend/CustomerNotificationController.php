<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CustomerNotificationController
 *
 * Lists and manages real-time notifications for the authenticated customer.
 * Notifications are stored in the `real_time_notifications` table via \App\Models\Notification.
 */
class CustomerNotificationController extends Controller
{
    /**
     * List all notifications for the authenticated user.
     * Route: GET /notifications
     */
    public function index(): View
    {
        $user          = auth('web')->user();
        $notifications = Notification::where('recipient_id', $user->id)
                                     ->latest('sent_at')
                                     ->paginate(20);

        $unreadCount = Notification::where('recipient_id', $user->id)
                                   ->where('read', false)
                                   ->count();

        return view('customer.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read.
     * Route: POST /notifications/{id}/read
     */
    public function markRead(int $id): RedirectResponse
    {
        $user = auth('web')->user();

        Notification::where('id', $id)
                    ->where('recipient_id', $user->id)
                    ->update(['read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     * Route: POST /notifications/read-all
     */
    public function markAllRead(): RedirectResponse
    {
        $user = auth('web')->user();

        Notification::where('recipient_id', $user->id)
                    ->where('read', false)
                    ->update(['read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
