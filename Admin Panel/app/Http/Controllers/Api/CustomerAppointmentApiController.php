<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Services\Shared\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAppointmentApiController extends Controller
{
    public function __construct(private readonly AppointmentService $service) {}

    // ── List appointments ─────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()
            ->appointments()
            ->with('expert.user')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success'      => true,
            'appointments' => $appointments->map(fn ($a) => $this->apptPayload($a)),
            'meta'         => [
                'current_page' => $appointments->currentPage(),
                'last_page'    => $appointments->lastPage(),
                'total'        => $appointments->total(),
            ],
        ]);
    }

    // ── Available experts ─────────────────────────────────────────────────────
    public function experts(): JsonResponse
    {
        $experts = Expert::with('user')->available()->get()->map(fn ($e) => [
            'id'           => $e->id,
            'name'         => optional($e->user)->name,
            'speciality'   => $e->speciality,
            'bio'          => $e->bio,
            'fee'          => $e->consultation_fee,
            'rating'       => $e->rating,
            'avatar'       => optional($e->user)->profile_photo
                                ? asset('storage/' . $e->user->profile_photo)
                                : null,
        ]);

        return response()->json(['success' => true, 'data' => $experts]);
    }

    // ── Book appointment ──────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'expert_id'    => 'nullable|exists:experts,id',
            'scheduled_at' => 'required|date|after:now',
            'notes'        => 'nullable|string|max:500',
        ]);

        $appointment = $this->service->book($request->user(), $data);

        return response()->json([
            'success'     => true,
            'message'     => 'Appointment booked successfully.',
            'appointment' => $this->apptPayload($appointment->load('expert.user')),
        ], 201);
    }

    // ── Show appointment ──────────────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $appt = $request->user()
            ->appointments()
            ->with('expert.user', 'statusHistory')
            ->findOrFail($id);

        return response()->json(['success' => true, 'appointment' => $this->apptPayload($appt)]);
    }

    // ── Cancel appointment ────────────────────────────────────────────────────
    public function cancel(Request $request, int $id): JsonResponse
    {
        $appt = $request->user()
            ->appointments()
            ->where('status', 'pending')
            ->findOrFail($id);

        $this->service->cancel($appt, 'Cancelled by customer.');

        return response()->json(['success' => true, 'message' => 'Appointment cancelled.']);
    }

    // ── Reschedule appointment ────────────────────────────────────────────────
    public function reschedule(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notes'        => 'nullable|string|max:500',
        ]);

        $appt = $request->user()
            ->appointments()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->findOrFail($id);

        $this->service->reschedule($appt, $data);

        return response()->json([
            'success'     => true,
            'message'     => 'Appointment rescheduled.',
            'appointment' => $this->apptPayload($appt->fresh()->load('expert.user')),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function apptPayload($appt): array
    {
        return [
            'id'           => $appt->id,
            'status'       => $appt->status,
            'scheduled_at' => $appt->scheduled_at?->toISOString(),
            'notes'        => $appt->notes,
            'expert'       => [
                'id'   => optional($appt->expert)->id,
                'name' => optional(optional($appt->expert)->user)->name,
            ],
            'created_at'   => $appt->created_at?->toISOString(),
        ];
    }
}
