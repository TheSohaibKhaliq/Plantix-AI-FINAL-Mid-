<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Services\Shared\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $appointment = $user->appointments()->with('expert.user')->findOrFail($id);

        return view('customer.appointment-details', compact('appointment'));
    }

    public function cancel(int $id): RedirectResponse
    {
        $user        = auth('web')->user();
        $appointment = $user->appointments()->where('status', 'pending')->findOrFail($id);

        $this->service->cancel($appointment, 'Cancelled by customer.');

        return back()->with('success', 'Appointment cancelled.');
    }
}

