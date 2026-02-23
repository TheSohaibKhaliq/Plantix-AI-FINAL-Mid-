<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast an order-status change to:
 *   - The vendor who owns the order
 *   - The customer who placed the order
 *   - Admins (a shared presence channel)
 *
 * Frontend Echo listener example:
 *   Echo.private(`vendor.${vendorId}`)
 *       .listen('OrderStatusUpdated', (e) => { ... })
 */
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor.' . $this->order->vendor_id),
            new PrivateChannel('user.' . $this->order->user_id),
        ];
    }

    /**
     * Data sent to the client.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->order->status,
            'vendor_id'    => $this->order->vendor_id,
            'user_id'      => $this->order->user_id,
            'updated_at'   => $this->order->updated_at->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name (maps to Echo.listen('...') ).
     */
    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }
}
