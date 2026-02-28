@extends('expert.layouts.app')
@section('title', 'Appointments')
@section('page-title', 'Appointment Management')

@section('content')
{{-- Stats row --}}
<div class="row g-4 mb-4">
    @foreach([
        ['label'=>'Total','key'=>'total','icon'=>'bi-calendar','color'=>'success'],
        ['label'=>'Pending','key'=>'pending','icon'=>'bi-hourglass-split','color'=>'warning'],
        ['label'=>'Upcoming','key'=>'upcoming','icon'=>'bi-calendar-event','color'=>'info'],
        ['label'=>'Completed','key'=>'completed','icon'=>'bi-check-circle','color'=>'success'],
        ['label'=>'Rejected','key'=>'rejected','icon'=>'bi-x-circle','color'=>'danger'],
    ] as $s)
    <div class="col">
        <div class="card border-0 shadow-sm text-center py-4 hover-card" style="border-radius:16px;">
            <i class="bi {{ $s['icon'] }} text-{{ $s['color'] }} fs-2 mb-2"></i>
            <div class="fw-bolder fs-4 text-dark">{{ $stats[$s['key']] }}</div>
            <div class="text-muted small text-uppercase fw-bold">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Status Filter</label>
                <select name="status" class="form-select form-select-md rounded-3">
                    <option value="">All Statuses</option>
                    @foreach(['requested','pending','accepted','confirmed','rescheduled','completed','rejected','cancelled'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-md rounded-3" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-sm-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-md rounded-3" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-sm-auto ms-auto">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="bi bi-search me-2"></i>Filter
                </button>
                <a href="{{ route('expert.appointments.index') }}" class="btn btn-outline-secondary px-4 rounded-pill ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Appointment List --}}
<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 rounded-top-left-3">#</th>
                        <th class="py-3">Farmer Details</th>
                        <th class="py-3">Topic</th>
                        <th class="py-3">Scheduled For</th>
                        <th class="py-3">Current Status</th>
                        <th class="py-3">Fee</th>
                        <th class="py-3 text-end px-4 rounded-top-right-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments->items() as $appt)
                    <tr class="hover-bg-light">
                        <td class="text-muted small px-4">#{{ $appt->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px;">
                                    <i class="bi bi-person text-success fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $appt->user->name }}</div>
                                    <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $appt->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium text-dark">{{ Str::limit($appt->topic ?? 'General Consultation', 30) }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><i class="bi bi-calendar3 text-success me-2"></i>{{ $appt->scheduled_at?->format('d M Y') }}</div>
                            <div class="text-muted small"><i class="bi bi-clock me-2"></i>{{ $appt->scheduled_at?->format('H:i') }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $appt->status_badge }} px-3 py-2 fw-medium">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td><span class="fw-bold">PKR {{ number_format($appt->fee) }}</span></td>
                        <td class="text-end px-4">
                            <a href="{{ route('expert.appointments.show', $appt) }}"
                               class="btn btn-sm btn-outline-success rounded-pill px-3 hover-card">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                <i class="bi bi-calendar-x display-3 mb-3 text-success"></i>
                                <h5 class="fw-bold text-dark">No appointments found</h5>
                                <p class="text-muted">Adjust your filters or wait for new farm consultation requests.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
    <div class="card-footer bg-white border-top p-4" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px;">
        {{ $appointments->appends($filters)->links() }}
    </div>
    @endif
</div>
@endsection
