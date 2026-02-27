@extends('expert.layouts.app')
@section('title', 'Appointments')
@section('page-title', 'Appointment Management')

@section('content')
{{-- Stats row --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total','key'=>'total','icon'=>'bi-calendar','color'=>'success'],
        ['label'=>'Pending','key'=>'pending','icon'=>'bi-hourglass-split','color'=>'warning'],
        ['label'=>'Upcoming','key'=>'upcoming','icon'=>'bi-calendar-event','color'=>'info'],
        ['label'=>'Completed','key'=>'completed','icon'=>'bi-check-circle','color'=>'success'],
        ['label'=>'Rejected','key'=>'rejected','icon'=>'bi-x-circle','color'=>'danger'],
    ] as $s)
    <div class="col">
        <div class="card border-0 shadow-sm text-center py-3">
            <i class="bi {{ $s['icon'] }} text-{{ $s['color'] }} fs-4"></i>
            <div class="fw-bold fs-5 mt-1">{{ $stats[$s['key']] }}</div>
            <div class="text-muted small">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['requested','pending','accepted','confirmed','rescheduled','completed','rejected','cancelled'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('expert.appointments.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Appointment List --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Farmer</th>
                        <th>Topic</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Fee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments->items() as $appt)
                    <tr>
                        <td class="text-muted small">{{ $appt->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $appt->user->name }}</div>
                            <div class="text-muted small">{{ $appt->user->email }}</div>
                        </td>
                        <td>{{ Str::limit($appt->topic ?? 'General Consultation', 30) }}</td>
                        <td class="small">{{ $appt->scheduled_at?->format('d M Y') }}<br>
                            <span class="text-muted">{{ $appt->scheduled_at?->format('H:i') }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $appt->status_badge }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td>PKR {{ number_format($appt->fee) }}</td>
                        <td>
                            <a href="{{ route('expert.appointments.show', $appt) }}"
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x d-block fs-2 mb-2"></i>No appointments found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $appointments->appends($filters)->links() }}
    </div>
    @endif
</div>
@endsection
