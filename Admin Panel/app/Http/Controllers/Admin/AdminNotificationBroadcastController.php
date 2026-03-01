<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin Notification Broadcast Controller
 * Section 5 – Admin Flow: Notification Broadcast (Section 14 Trigger Map)
 *
 * Allows admin to send bulk in-app (+ optional email) notifications
 * to all users or a specific role group.
 * Jobs are chunked (100 per dispatch) to avoid memory overload.
 */
class AdminNotificationBroadcastController extends Controller
{
    private const CHUNK_SIZE = 100;

    /**
     * Show the broadcast compose form.
     * GET /admin/notifications/broadcast
     */
    public function index(): View
    {
        return view('admin.notifications.broadcast', [
            'targets' => [
                'all'       => 'All Users',
                'customers' => 'Customers Only',
                'vendors'   => 'Vendors Only',
                'experts'   => 'Experts Only',
            ],
        ]);
    }

    /**
     * Dispatch the broadcast in chunked jobs.
     * POST /admin/notifications/broadcast
     *
     * body: { title, body, target[all|customers|vendors|experts], action_url?, send_email? }
     */
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'      => 'required|string|max:120',
            'body'       => 'required|string|max:500',
            'target'     => 'required|in:all,customers,vendors,experts',
            'action_url' => 'nullable|url|max:300',
            'send_email' => 'nullable|boolean',
        ]);

        $query = User::query()->where('active', true);

        match ($data['target']) {
            'customers' => $query->where('role', 'user'),
            'vendors'   => $query->where('role', 'vendor'),
            'experts'   => $query->whereIn('role', ['expert', 'agency_expert']),
            default     => null, // all
        };

        $totalDispatched = 0;
        $sendEmail       = (bool) ($data['send_email'] ?? false);
        $notifData       = [
            'title'      => $data['title'],
            'body'       => $data['body'],
            'action_url' => $data['action_url'] ?? null,
            'admin_id'   => auth('admin')->id(),
        ];

        $query->select('id')->chunk(self::CHUNK_SIZE, function ($users) use ($notifData, $sendEmail, &$totalDispatched) {
            $userIds = $users->pluck('id')->all();

            dispatch(new \App\Jobs\SendBroadcastNotificationChunk($userIds, $notifData, $sendEmail));

            $totalDispatched += count($userIds);
        });

        Log::info("Admin broadcast dispatched", [
            'target'  => $data['target'],
            'total'   => $totalDispatched,
            'admin'   => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.notifications.broadcast.history')
            ->with('success', "Broadcast queued for {$totalDispatched} user(s). Jobs will process in background.");
    }

    /**
     * Show history of past broadcasts (from a log table or notification stats).
     * GET /admin/notifications/broadcast/history
     */
    public function history(): View
    {
        // Aggregate last 50 admin-originated broadcast notifications grouped by created_at day
        $history = \App\Models\Notification::where('type', AdminBroadcastNotification::class)
            ->latest()
            ->take(50)
            ->get()
            ->groupBy(fn ($n) => $n->created_at->format('Y-m-d'))
            ->map(fn ($group) => [
                'date'  => $group->first()->created_at->format('M j, Y'),
                'count' => $group->count(),
                'title' => data_get(json_decode($group->first()->data, true), 'title', '—'),
            ])
            ->values();

        return view('admin.notifications.broadcast-history', compact('history'));
    }
}
