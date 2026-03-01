<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $notifications = Notification::where('recipient_id', $userId)
                                    ->where('read', false)
                                    ->latest()
                                    ->get();
                                    
        return response()->json($notifications);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|string',
            'recipient_id' => 'required|string',
            'metadata' => 'nullable|array'
        ]);

        $notification = Notification::create($validated);
        
        return response()->json($notification, 201);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->update(['read' => true]);
        return response()->json($notification);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        Notification::where('recipient_id', $userId)
                   ->where('read', false)
                   ->update(['read' => true]);
                   
        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();
        return response()->json(null, 204);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('recipient_id', auth()->id())
                             ->where('read', false)
                             ->count();

        return response()->json(['success' => true, 'unread_count' => $count]);
    }
}
