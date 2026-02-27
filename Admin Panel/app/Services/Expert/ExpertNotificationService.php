<?php

namespace App\Services\Expert;

use App\Models\Expert;
use App\Models\ExpertNotificationLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ExpertNotificationService
 *
 * Manages expert-scoped in-app notification logs.
 * Laravel's built-in notifiable system writes to the `notifications` table;
 * this service provides additional typed log management for the expert panel.
 */
class ExpertNotificationService
{
    public const TYPE_APPOINTMENT_NEW      = 'appointment.new';
    public const TYPE_APPOINTMENT_UPDATE   = 'appointment.update';
    public const TYPE_FORUM_MENTION        = 'forum.mention';
    public const TYPE_FORUM_REPLY          = 'forum.reply';
    public const TYPE_ADMIN_ANNOUNCEMENT   = 'admin.announcement';
    public const TYPE_PROFILE_APPROVED     = 'profile.approved';

    /**
     * Create a new notification log entry for an expert.
     */
    public function notify(
        Expert $expert,
        string $type,
        string $title,
        string $body = '',
        array $data = [],
        ?int $relatedId = null
    ): ExpertNotificationLog {
        return ExpertNotificationLog::create([
            'expert_id'  => $expert->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'data'       => $data,
            'related_id' => $relatedId,
            'is_read'    => false,
        ]);
    }

    /**
     * Paginated list of notifications for panel display.
     */
    public function listForExpert(Expert $expert, bool $unreadOnly = false): LengthAwarePaginator
    {
        $query = ExpertNotificationLog::where('expert_id', $expert->id)
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->paginate(20);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(ExpertNotificationLog $log, Expert $expert): void
    {
        if ((int) $log->expert_id !== (int) $expert->id) {
            throw new \DomainException('Access denied to this notification.');
        }

        $log->markAsRead();
    }

    /**
     * Mark all unread notifications for an expert as read.
     */
    public function markAllRead(Expert $expert): int
    {
        return ExpertNotificationLog::where('expert_id', $expert->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Count unread notifications (used in nav badge).
     */
    public function unreadCount(Expert $expert): int
    {
        return ExpertNotificationLog::where('expert_id', $expert->id)
            ->where('is_read', false)
            ->count();
    }
}
