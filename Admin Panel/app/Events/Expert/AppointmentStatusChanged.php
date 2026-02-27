<?php

namespace App\Events\Expert;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired every time an appointment's status changes.
 * Listeners handle notifications to both expert and farmer.
 */
class AppointmentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Appointment $appointment,
        public readonly User        $changedBy,
        public readonly string      $newStatus,
    ) {}
}
