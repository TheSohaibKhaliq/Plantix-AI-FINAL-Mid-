<?php

namespace App\Services\Shared;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;

/**
 * NotificationService
 *
 * Direct replacement for the FCM code scattered across controllers
 * (previously in OrderController::sendNotification using raw curl).
 * All FCM logic is now centralised here.
 */
class NotificationService
{
    private ?string $accessToken = null;

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    public function sendToUser(User $user, string $title, string $body = '', array $data = []): bool
    {
        if (empty($user->fcm_token)) {
            return false;
        }

        return $this->sendFcm($user->fcm_token, $title, $body, $data);
    }

    public function sendToMany(array $users, string $title, string $body = '', array $data = []): void
    {
        foreach ($users as $user) {
            if ($user instanceof User && !empty($user->fcm_token)) {
                $this->sendFcm($user->fcm_token, $title, $body, $data);
            }
        }
    }

    public function sendOrderStatusNotification(
        User   $user,
        string $orderNumber,
        string $status,
        string $orderId
    ): bool {
        $messages = [
            'accepted'        => "Your order #{$orderNumber} has been accepted!",
            'rejected'        => "Your order #{$orderNumber} was rejected.",
            'preparing'       => "Your order #{$orderNumber} is being prepared.",
            'driver_assigned' => "A driver has been assigned to your order #{$orderNumber}.",
            'picked_up'       => "Your order #{$orderNumber} has been picked up.",
            'delivered'       => "Your order #{$orderNumber} has been delivered. Enjoy!",
            'cancelled'       => "Your order #{$orderNumber} has been cancelled.",
        ];

        $body = $messages[$status] ?? "Order #{$orderNumber} status updated to {$status}.";

        return $this->sendToUser($user, 'Order Update', $body, [
            'order_id' => $orderId,
            'status'   => $status,
        ]);
    }

    // -------------------------------------------------------------------------
    // FCM Implementation (same Google Client approach as original, now clean)
    // -------------------------------------------------------------------------

    private function sendFcm(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $projectId = config('services.firebase.project_id');

        if (empty($projectId)) {
            Log::warning('NotificationService: FIREBASE_PROJECT_ID not configured.');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return false;
        }

        $payload = [
            'message' => [
                'notification' => ['title' => $title, 'body'  => $body],
                'token'        => $fcmToken,
                'data'         => array_map('strval', $data),
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    $payload,
                );

            if (!$response->successful()) {
                Log::error('FCM send failed', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'token'   => substr($fcmToken, 0, 20) . '...',
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('FCM exception: ' . $e->getMessage());
            return false;
        }
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $credentialsPath = storage_path('app/firebase/credentials.json');

        if (!file_exists($credentialsPath)) {
            Log::warning('NotificationService: Firebase credentials.json not found at ' . $credentialsPath);
            return null;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $tokenData         = $client->getAccessToken();
            $this->accessToken = $tokenData['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('FCM token fetch failed: ' . $e->getMessage());
            return null;
        }

        return $this->accessToken;
    }
}


