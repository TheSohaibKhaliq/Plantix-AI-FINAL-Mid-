<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentReschedule;
use App\Models\Expert;
use App\Notifications\AppointmentRescheduledNotification;
use App\Services\Shared\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CustomerAppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $service,
    ) {}

    public function index(): View
    {
        $user         = auth('web')->user();
        $appointments = $user->appointments()->with('expert.user')->latest()->paginate(10);

        return view('customer.appointments', compact('appointments'));
    }

    public function create(): View
    {
        $experts = Expert::with('user')->available()->get();
        return view('customer.appointment-book', compact('experts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'expert_id'    => 'nullable|exists:experts,id',
            'scheduled_at' => 'required|date|after:now',
            'notes'        => 'nullable|string|max:500',
        ]);

        $user = auth('web')->user();
        $this->service->book($user, $request->validated());

        return redirect()->route('appointments')
                         ->with('success', 'Appointment booked! You will receive a confirmation email.');
    }

    public function show(int $id): View
    {
        $user        = auth('web')->user();
        $appointment = $user->appointments()
            ->with(['expert.user', 'reschedules' => fn ($q) => $q->where('status', 'pending')])
            ->findOrFail($id);

        return view('customer.appointment-details', compact('appointment'));
    }

    public function cancel(int $id): RedirectResponse
    {
        $user        = auth('web')->user();
        $appointment = $user->appointments()->where('status', 'pending')->findOrFail($id);

        $this->service->cancel($appointment, 'Cancelled by customer.');

        return back()->with('success', 'Appointment cancelled.');
    }

    /**
     * Customer accepts or rejects an expert's reschedule proposal.
     * Section 6 – Reschedule Logic: POST /appointment/{id}/reschedule-response
     * Input: action[accept|reject]
     */
    public function rescheduleResponse(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        $user        = auth('web')->user();
        $appointment = $user->appointments()
            ->where('status', Appointment::STATUS_RESCHEDULED)
            ->findOrFail($id);

        $reschedule = AppointmentReschedule::where('appointment_id', $appointment->id)
            ->where('status', 'pending')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($request, $appointment, $reschedule, $user) {
            if ($request->action === 'accept') {
                // Update appointment to the new proposed time
                $appointment->update([
                    'scheduled_at' => $reschedule->proposed_scheduled_at,
                    'status'       => Appointment::STATUS_ACCEPTED,
                ]);
                $reschedule->update(['status' => 'accepted']);
            } else {
                // Reject: revert appointment to confirmed/accepted, keep original time
                $appointment->update(['status' => Appointment::STATUS_ACCEPTED]);
                $reschedule->update(['status' => 'rejected']);
            }
        });

        // Notify the expert about the customer's decision
        if ($appointment->expert?->user) {
            try {
                $appointment->expert->user->notify(
                    new AppointmentRescheduledNotification(
                        $appointment->fresh(),
                        $reschedule->fresh(),
                        $request->action === 'accept' ? 'accepted' : 'rejected'
                    )
                );
            } catch (\Throwable $e) {
                Log::warning('Reschedule response notification to expert failed: ' . $e->getMessage());
            }
        }

        $message = $request->action === 'accept'
            ? 'Reschedule accepted. Your appointment has been updated.'
            : 'Reschedule rejected. Your appointment remains at the original time.';

        return redirect()->route('appointment.details', $id)->with('success', $message);
    }
}

