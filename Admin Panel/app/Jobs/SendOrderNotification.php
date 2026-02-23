<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued job to deliver an FCM push notification when an order status changes.
 *
 * Dispatched from:  OrderService::updateStatus()
 * Queue:            "notifications" (configure in config/queue.php)
 *
 * The job intentionally does NOT use the ShouldBroadcast contract —
 * the WebSocket broadcast is handled separately via OrderStatusUpdated event.
 * This job is *only* responsible for the FCM push layer.
 */
class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retry up to 3 times before failing.
     */
    public int $tries = 3;

    /**
     * Back off delay in seconds between retries (exponential).
     */
    public array $backoff = [30, 60, 120];

    public function __construct(
        public readonly Order  $order,
        public readonly string $oldStatus,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(NotificationService $notificationService): void
    {
        try {
            $notificationService->sendOrderStatusNotification($this->order, $this->oldStatus);
        } catch (\Throwable $e) {
            Log::warning('SendOrderNotification job failed', [
                'order_id'   => $this->order->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->order->status,
                'error'      => $e->getMessage(),
            ]);

            throw $e; // let the queue retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendOrderNotification permanently failed', [
            'order_id' => $this->order->id,
            'error'    => $exception->getMessage(),
        ]);
    }
}
