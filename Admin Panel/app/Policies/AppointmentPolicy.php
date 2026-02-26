<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id
            || $appointment->expert?->user_id === $user->id;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id
            && $appointment->status === 'pending';
    }
}
