<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appt    = $this->appointment;
        $dateStr = \Carbon\Carbon::parse($appt->appointment_date)->format('l, F j, Y');
        $time    = $appt->appointment_time
            ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A')
            : 'TBD';

        return (new MailMessage())
            ->subject("Appointment Confirmed — {$dateStr}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your appointment has been **confirmed**!")
            ->line("**Date:** {$dateStr}")
            ->line("**Time:** {$time}")
            ->when($appt->expert, fn ($m) => $m->line("**Expert:** {$appt->expert->name}"))
            ->action('View Appointment', route('appointment.details', $appt->id))
            ->line('Please be available at the scheduled time. You may cancel up to 24 hours before.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'appointment_confirmed',
            'appointment_id' => $this->appointment->id,
            'message'        => "Your appointment on {$this->appointment->appointment_date} is confirmed.",
            'date'           => $this->appointment->appointment_date,
        ];
    }
}
