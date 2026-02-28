@extends('expert.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Expert Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Stats cards --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#e8f5e9">
                    <i class="bi bi-calendar-check text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Appointments</div>
                    <div class="fw-bold fs-4">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#fff8e1">
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Requests</div>
                    <div class="fw-bold fs-4">{{ $stats['pending'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#e8f5e9">
                    <i class="bi bi-calendar-event text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Upcoming</div>
                    <div class="fw-bold fs-4">{{ $stats['upcoming'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:#e8f5e9">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <div class="fw-bold fs-4">{{ $stats['completed'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Pending appointment requests --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-inbox me-2 text-warning"></i>New Appointment Requests
                </h6>
                <a href="{{ route('expert.appointments.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($requested->items() as $appt)
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <div>
                        <div class="fw-semibold">{{ $appt->user->name }}</div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i>{{ $appt->scheduled_at?->format('d M Y, H:i') }}
                            @if($appt->topic)
                                · {{ Str::limit($appt->topic, 35) }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('expert.appointments.show', $appt) }}"
                           class="btn btn-sm btn-success">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>No pending requests
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Profile summary --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-success"></i>My Profile</h6>
                <a href="{{ route('expert.profile.edit') }}" class="btn btn-sm btn-outline-success">Edit</a>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($expert->avatar)
                        <img src="{{ Storage::url($expert->avatar) }}"
                             class="rounded-circle" style="width:60px;height:60px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white"
                             style="width:60px;height:60px;font-size:1.4rem;font-weight:700">
                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold">{{ $expert->user->name }}</div>
                        <div class="text-muted small">{{ $expert->specialty }}</div>
                        @if($expert->profile)
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-size:.7rem">
                                {{ ucfirst($expert->profile->account_type) }}
                            </span>
                        @endif
                    </div>
                </div>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><i class="bi bi-geo-alt me-2 text-success"></i>{{ $expert->profile?->city ?? 'N/A' }}, {{ $expert->profile?->country ?? '' }}</li>
                    <li class="mb-1"><i class="bi bi-briefcase me-2 text-success"></i>{{ $expert->profile?->experience_years ?? 0 }} years experience</li>
                    <li class="mb-1"><i class="bi bi-currency-dollar me-2 text-success"></i>PKR {{ number_format($expert->hourly_rate) }}/hr</li>
                    <li><i class="bi bi-circle-fill me-2 {{ $expert->is_available ? 'text-success' : 'text-secondary' }}" style="font-size:.5rem"></i>
                        {{ $expert->is_available ? 'Available' : 'Unavailable' }}
                    </li>
                </ul>
            </div>
        </div>

        {{-- Notifications summary --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-bell me-2 text-primary"></i>Notifications
                    @if($unreadCount > 0)
                        <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                    @endif
                </h6>
                <a href="{{ route('expert.notifications.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-3 text-center text-muted small">
                @if($unreadCount > 0)
                    You have <strong>{{ $unreadCount }}</strong> unread notification(s).
                @else
                    <i class="bi bi-check-all me-1"></i>All caught up!
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
