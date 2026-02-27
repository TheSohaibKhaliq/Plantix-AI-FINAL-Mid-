<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\ExpertNotificationLog;
use App\Services\Expert\ExpertNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ExpertNotificationController
 *
 * Handles listing, reading, and clearing expert-scoped notifications.
 */
class ExpertNotificationController extends Controller
{
    public function __construct(
        private readonly ExpertNotificationService $service
    ) {}

    private function currentExpert(): \App\Models\Expert
    {
        return auth('expert')->user()->expert;
    }

    public function index(): View
    {
        $expert        = $this->currentExpert();
        $notifications = $this->service->listForExpert($expert);
        $unreadCount   = $this->service->unreadCount($expert);

        return view('expert.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(ExpertNotificationLog $notification): RedirectResponse
    {
        $this->service->markRead($notification, $this->currentExpert());

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(): RedirectResponse
    {
        $count = $this->service->markAllRead($this->currentExpert());

        return back()->with('success', "{$count} notifications marked as read.");
    }

    /**
     * JSON endpoint for the nav-bar badge (unread count).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => $this->service->unreadCount($this->currentExpert()),
        ]);
    }
}
