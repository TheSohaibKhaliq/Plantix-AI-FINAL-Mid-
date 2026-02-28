<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Expert;
use App\Services\Shared\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $service,
    ) {}

    public function index(Request $request): View
    {
        $query = Appointment::with(['user', 'expert.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('expert_id')) {
            $query->where('expert_id', $request->expert_id);
        }

        $appointments = $query->paginate(20)->withQueryString();
        $experts      = Expert::with('user')->available()->get();
        $statuses     = ['pending', 'confirmed', 'completed', 'cancelled'];

        return view('admin.appointments.index', compact('appointments', 'experts', 'statuses'));
    }

    public function show(int $id): View
    {
        $appointment = Appointment::with(['user', 'expert.user'])->findOrFail($id);
        $experts     = Expert::with('user')->available()->get();

        return view('admin.appointments.show', compact('appointment', 'experts'));
    }

    public function confirm(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'expert_id'   => 'nullable|exists:experts,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $appointment = Appointment::findOrFail($id);
        $this->service->confirm($appointment, $request->expert_id, $request->admin_notes);

        return back()->with('success', 'Appointment confirmed.');
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $appointment = Appointment::findOrFail($id);
        $this->service->cancel($appointment, $request->reason);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function complete(int $id): RedirectResponse
    {
        $appointment = Appointment::findOrFail($id);
        $this->service->complete($appointment);

        return back()->with('success', 'Appointment marked as completed.');
    }
}

