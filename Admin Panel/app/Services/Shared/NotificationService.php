<?php

namespace App\Services\Shared;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * NotificationService
 *
 * Delivers in-app (database-channel) notifications to users.
 * No third-party push services required — records are stored in the
 * `notifications` table and surfaced through the standard
 * $user->notifications relationship.
 */
class NotificationService
{
    // -------------------------------------------------------------------------
    // Public API  (signatures unchanged — callers need no modifications)
    // -------------------------------------------------------------------------

    /**
     * Send an in-app notification to a single user.
     */
    public function sendToUser(User $user, string $title, string $body = '', array $data = []): bool
    {
        try {
            $this->store($user, $title, $body, $data);
            return true;
        } catch (\Throwable $e) {
            Log::error('NotificationService::sendToUser failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            return false;
        }
    }

    /**
     * Send an in-app notification to multiple users.
     */
    public function sendToMany(array $users, string $title, string $body = '', array $data = []): void
    {
        foreach ($users as $user) {
            if ($user instanceof User) {
                $this->sendToUser($user, $title, $body, $data);
            }
        }
    }

    /**
     * Helper specifically for order-status change notifications.
     */
    public function sendOrderStatusNotification(
        User   $user,
        string $orderNumber,
        string $status,
        string $orderId
    ): bool {
        $messages = [
            'confirmed'        => "Your order #{$orderNumber} has been confirmed!",
            'processing'       => "Your order #{$orderNumber} is being prepared.",
            'shipped'          => "Your order #{$orderNumber} is on the way.",
            'delivered'        => "Your order #{$orderNumber} has been delivered. Enjoy!",
            'cancelled'        => "Your order #{$orderNumber} has been cancelled.",
            'rejected'         => "Your order #{$orderNumber} was rejected.",
            'return_requested' => "Return request for order #{$orderNumber} received.",
            'returned'         => "Your return for order #{$orderNumber} has been processed.",
        ];

        $body = $messages[$status] ?? "Order #{$orderNumber} status updated to {$status}.";

        return $this->sendToUser($user, 'Order Update', $body, [
            'order_id' => (string) $orderId,
            'status'   => $status,
            'type'     => 'order_status',
        ]);
    }

    // -------------------------------------------------------------------------
    // Internal
    // -------------------------------------------------------------------------

    private function store(User $user, string $title, string $body, array $data): void
    {
        DatabaseNotification::create([
            'id'              => (string) Str::uuid(),
            'type'            => 'App\Notifications\GenericDatabaseNotification',
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => json_encode([
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
            ]),
            'read_at'         => null,
        ]);
    }
}


