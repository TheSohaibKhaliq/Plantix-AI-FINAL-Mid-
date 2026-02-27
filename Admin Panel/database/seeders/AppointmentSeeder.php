<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = DB::table('users')->where('role', 'user')->get(['id', 'name']);
        $experts = DB::table('experts')->get(['id', 'user_id', 'hourly_rate']);

        if ($farmers->isEmpty() || $experts->isEmpty()) {
            $this->command->warn('Missing farmers or experts. Run UsersSeeder and ExpertsSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Realistic appointment topics for agricultural consultations
        $topics = [
            'Cotton Leaf Curl Virus management and whitefly control',
            'Wheat rust identification and emergency fungicide schedule',
            'Rice blast disease — neck rot prevention before heading',
            'Fertilizer plan for low-phosphorus soil — Rabi season',
            'Drip irrigation system design for 15-acre mango orchard',
            'Integrated pest management for vegetables in polytunnel',
            'Sugar cane replanting advice after red rot incidence',
            'Soil salinity reclamation using gypsum and leaching',
            'Organic farming certification requirements in Punjab',
            'Crop insurance claim documentation for flood damage',
            'Maize hybrid selection for high-temperature Kharif season',
            'Water logging problem in clay soil — drainage installation',
            'Post-harvest grain storage and fumigation advisory',
            'Tomato early blight control in rainy season',
            'Groundwater quality impact on rice germination',
        ];

        $meetingLinks = [
            'https://meet.google.com/abc-defg-hij',
            'https://zoom.us/j/1234567890',
            'https://teams.microsoft.com/l/meetup-join/test',
            null,  // offline appointments
        ];

        $appointments   = [];
        $statusHistory  = [];
        $notifications  = [];

        $farmerList = $farmers->values();
        $expertList = $experts->values();

        // Each expert gets 2–4 appointments
        foreach ($expertList as $expert) {
            $numAppts = rand(2, 4);

            for ($a = 0; $a < $numAppts; $a++) {
                $farmer       = $farmerList[($expert->id + $a) % $farmerList->count()];
                $topic        = $topics[($expert->id * 2 + $a) % count($topics)];
                $duration     = [30, 45, 60, 90][array_rand([30, 45, 60, 90])];
                $fee          = round($expert->hourly_rate * ($duration / 60), 0);
                $scheduledAt  = Carbon::now()->subDays(rand(-14, 60)); // past and future

                // Status distribution (realistic)
                $statusPool = [
                    'completed', 'completed', 'completed',
                    'confirmed', 'confirmed',
                    'pending',
                    'cancelled',
                ];
                $status = $statusPool[array_rand($statusPool)];

                $paymentStatus = match ($status) {
                    'completed' => 'paid',
                    'confirmed' => (rand(0, 1) ? 'paid' : 'unpaid'),
                    'cancelled' => (rand(0, 1) ? 'refunded' : 'unpaid'),
                    default     => 'unpaid',
                };

                $acceptedAt   = ($status !== 'pending')  ? $scheduledAt->copy()->subDays(rand(1, 7)) : null;
                $completedAt  = ($status === 'completed') ? $scheduledAt->copy()->addMinutes($duration) : null;
                $rejectedAt   = ($status === 'cancelled') ? $scheduledAt->copy()->subDays(rand(1, 3)) : null;
                $rejectReason = ($status === 'cancelled') ? 'Expert unavailable at requested time.' : null;
                $meetingLink  = in_array($status, ['confirmed', 'completed']) ? $meetingLinks[array_rand($meetingLinks)] : null;

                $appointments[] = [
                    'user_id'          => $farmer->id,
                    'expert_id'        => $expert->id,
                    'scheduled_at'     => $scheduledAt->toDateTimeString(),
                    'duration_minutes' => $duration,
                    'status'           => $status,
                    'notes'            => 'Consultation: ' . $topic,
                    'admin_notes'      => null,
                    'fee'              => $fee,
                    'payment_status'   => $paymentStatus,
                    'topic'            => $topic,
                    'meeting_link'     => $meetingLink,
                    'reschedule_requested_at' => null,
                    'accepted_at'      => $acceptedAt?->toDateTimeString(),
                    'rejected_at'      => $rejectedAt?->toDateTimeString(),
                    'completed_at'     => $completedAt?->toDateTimeString(),
                    'reject_reason'    => $rejectReason,
                    'created_at'       => $scheduledAt->copy()->subDays(rand(3, 14))->toDateTimeString(),
                    'updated_at'       => $now,
                ];
            }
        }

        foreach (array_chunk($appointments, 50) as $chunk) {
            DB::table('appointments')->insert($chunk);
        }

        // Reload to get IDs for status history
        $insertedAppts = DB::table('appointments')->orderBy('id')->get();
        $adminId       = DB::table('users')->where('role', 'admin')->value('id') ?? 1;

        foreach ($insertedAppts as $appt) {
            // Always create an initial "pending" history entry
            $createdAt = Carbon::parse($appt->created_at);

            $statusHistory[] = [
                'appointment_id' => $appt->id,
                'changed_by'     => $appt->user_id,
                'from_status'    => '',
                'to_status'      => 'pending',
                'notes'          => 'Appointment request submitted by farmer.',
                'changed_at'     => $createdAt->toDateTimeString(),
                'created_at'     => $createdAt->toDateTimeString(),
                'updated_at'     => $createdAt->toDateTimeString(),
            ];

            if ($appt->status !== 'pending') {
                $statusHistory[] = [
                    'appointment_id' => $appt->id,
                    'changed_by'     => $appt->expert_id,    // expert's user_id via expert table
                    'from_status'    => 'pending',
                    'to_status'      => in_array($appt->status, ['cancelled']) ? 'cancelled' : 'confirmed',
                    'notes'          => in_array($appt->status, ['cancelled']) ? 'Expert declined: unavailable.' : 'Expert confirmed appointment.',
                    'changed_at'     => $appt->accepted_at ?? $createdAt->addHours(2)->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            if ($appt->status === 'completed') {
                $statusHistory[] = [
                    'appointment_id' => $appt->id,
                    'changed_by'     => $adminId,
                    'from_status'    => 'confirmed',
                    'to_status'      => 'completed',
                    'notes'          => 'Session completed successfully.',
                    'changed_at'     => $appt->completed_at ?? $now->toDateTimeString(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        foreach (array_chunk($statusHistory, 100) as $chunk) {
            DB::table('appointment_status_history')->insert($chunk);
        }

        // Expert notification logs: new appointment notification per expert
        $specialtiesMap = DB::table('experts')->pluck('specialty', 'id');

        foreach ($insertedAppts as $appt) {
            $notifications[] = [
                'expert_id'  => $appt->expert_id,
                'type'       => 'new_appointment',
                'title'      => 'New Consultation Request',
                'body'       => 'A farmer has requested a consultation: ' . substr($appt->topic ?? 'Agricultural advice', 0, 60) . '...',
                'data'       => json_encode(['appointment_id' => $appt->id, 'farmer_id' => $appt->user_id]),
                'related_id' => $appt->user_id,
                'is_read'    => in_array($appt->status, ['completed', 'confirmed', 'cancelled']),
                'read_at'    => in_array($appt->status, ['completed', 'confirmed']) ? $now->toDateTimeString() : null,
                'created_at' => $appt->created_at,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($notifications, 100) as $chunk) {
            DB::table('expert_notification_logs')->insert($chunk);
        }

        $this->command->info(sprintf(
            'AppointmentSeeder: %d appointments, %d status history, %d notification logs seeded.',
            count($appointments),
            count($statusHistory),
            count($notifications)
        ));
    }
}
