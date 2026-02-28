<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Shared\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued job to deliver an in-app (database-channel) notification when an
 * order status changes.
 *
 * Dispatched from:  OrderService::updateStatus()
 * Queue:            "notifications" (configure in config/queue.php)
 */
class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int   $tries  = 3;
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
            $user = $this->order->user;

            if (! $user) {
                Log::warning('SendOrderNotification: order has no user', ['order_id' => $this->order->id]);
                return;
            }

            $notificationService->sendOrderStatusNotification(
                $user,
                (string) $this->order->order_number,
                (string) $this->order->status,
                (string) $this->order->id,
            );
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


