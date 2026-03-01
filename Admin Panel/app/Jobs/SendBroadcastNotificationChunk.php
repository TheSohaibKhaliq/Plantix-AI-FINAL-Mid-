<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Processes one chunk (up to 100 users) of an admin broadcast.
 * Dispatched by AdminNotificationBroadcastController::send() in a loop.
 *
 * Queue: notifications
 * Tries: 3 with exponential backoff
 */
class SendBroadcastNotificationChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    /**
     * @param array<int>  $userIds   Slice of user IDs to notify
     * @param array       $data      title, body, action_url, admin_id
     * @param bool        $sendEmail Whether to also send via email
     */
    public function __construct(
        private readonly array $userIds,
        private readonly array $data,
        private readonly bool  $sendEmail = false,
    ) {}

    public function handle(): void
    {
        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            try {
                $user->notify(
                    new AdminBroadcastNotification($this->data, $this->sendEmail)
                );
            } catch (\Throwable $e) {
                Log::error("Broadcast notification failed for user {$user->id}: " . $e->getMessage());
            }
        }
    }

    public function backoff(): array
    {
        return [30, 60, 120]; // seconds before each retry
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendBroadcastNotificationChunk job failed', [
            'user_ids' => $this->userIds,
            'error'    => $exception->getMessage(),
        ]);
    }
}
