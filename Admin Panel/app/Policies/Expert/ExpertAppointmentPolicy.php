<?php

namespace App\Policies\Expert;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ExpertAppointmentPolicy
 *
 * Gates around appointment status transitions for the expert guard.
 */
class ExpertAppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * An expert can accept only their own pending/requested appointments.
     */
    public function accept(User $user, Appointment $appointment): bool
    {
        return $this->ownsAppointment($user, $appointment)
            && $appointment->canBeAccepted();
    }

    /**
     * An expert can reject only their own pending/requested appointments.
     */
    public function reject(User $user, Appointment $appointment): bool
    {
        return $this->ownsAppointment($user, $appointment)
            && $appointment->canBeRejected();
    }

    /**
     * An expert can complete only confirmed/accepted appointments.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        return $this->ownsAppointment($user, $appointment)
            && $appointment->canBeCompleted();
    }

    /**
     * An expert can reschedule only confirmed/accepted appointments.
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        return $this->ownsAppointment($user, $appointment)
            && $appointment->canBeRescheduled();
    }

    /**
     * An expert can view only their own appointments.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $this->ownsAppointment($user, $appointment);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function ownsAppointment(User $user, Appointment $appointment): bool
    {
        return $user->expert && (int) $appointment->expert_id === (int) $user->expert->id;
    }
}
