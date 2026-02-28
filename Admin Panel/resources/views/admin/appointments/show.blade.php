@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Appointment #{{ $appointment->id }}</h5>
                    @php
                        $bc = ['pending'=>'warning','confirmed'=>'success','cancelled'=>'danger','completed'=>'info'];
                    @endphp
                    <span class="badge bg-{{ $bc[$appointment->status] ?? 'secondary' }} fs-6">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer</h6>
                            <p class="mb-1 fw-semibold">{{ $appointment->user->name ?? 'N/A' }}</p>
                            <p class="mb-1 small">{{ $appointment->user->email ?? '' }}</p>
                            <p class="mb-0 small">{{ $appointment->user->phone ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Expert</h6>
                            @if($appointment->expert)
                                <p class="mb-1 fw-semibold">{{ $appointment->expert->name }}</p>
                                <p class="mb-1 small">{{ $appointment->expert->specialization }}</p>
                                <p class="mb-0 small">Fee: {{ config('plantix.currency_symbol') }}{{ number_format($appointment->expert->fee, 2) }}</p>
                            @else
                                <p class="text-muted">Not yet assigned</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Appointment Date & Time</h6>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}
                                @if($appointment->appointment_time)
                                    at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                        @if($appointment->notes)
                        <div class="col-12">
                            <h6 class="text-muted">Customer Notes</h6>
                            <p class="mb-0">{{ $appointment->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if(in_array($appointment->status, ['pending','confirmed']))
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Actions</h6></div>
                <div class="card-body d-flex gap-2 flex-wrap">
                    @if($appointment->status === 'pending')
                    <form action="{{ route('admin.appointments.confirm', $appointment->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Assign Expert</label>
                            <select name="expert_id" class="form-select form-select-sm" required>
                                <option value="">— Select Expert —</option>
                                @foreach($experts ?? [] as $expert)
                                    <option value="{{ $expert->id }}">{{ $expert->name }} — {{ $expert->specialization }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i>Confirm & Assign
                        </button>
                    </form>
                    @endif

                    @if($appointment->status === 'confirmed')
                    <form action="{{ route('admin.appointments.complete', $appointment->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-info btn-sm text-white">
                            <i class="bi bi-star me-1"></i>Mark Completed
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('admin.appointments.cancel', $appointment->id) }}" method="POST"
                          onsubmit="return confirm('Cancel this appointment?')">
                        @csrf
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Appointments
        </a>
    </div>
</div>
@endsection
