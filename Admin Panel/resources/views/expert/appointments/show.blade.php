@extends('expert.layouts.app')
@section('title', 'Appointment #' . $appointment->id)
@section('page-title', 'Appointment Details')

@section('content')
<div class="row g-4">
    {{-- Main details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-check me-2 text-success"></i>Appointment #{{ $appointment->id }}
                </h6>
                <span class="badge bg-{{ $appointment->status_badge }} px-3 py-2">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small">Farmer</div>
                        <div class="fw-semibold">{{ $appointment->user->name }}</div>
                        <div class="text-muted small">{{ $appointment->user->email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Scheduled</div>
                        <div class="fw-semibold">{{ $appointment->scheduled_at?->format('D, d M Y') }}</div>
                        <div class="text-muted small">{{ $appointment->scheduled_at?->format('H:i') }} ({{ $appointment->duration_minutes }} min)</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Topic</div>
                        <div>{{ $appointment->topic ?? 'General Consultation' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Fee / Payment</div>
                        <div>PKR {{ number_format($appointment->fee) }}
                            <span class="badge bg-{{ $appointment->payment_status === 'paid' ? 'success' : 'warning' }} ms-1">
                                {{ ucfirst($appointment->payment_status) }}
                            </span>
                        </div>
                    </div>
                    @if($appointment->notes)
                    <div class="col-12">
                        <div class="text-muted small">Farmer Notes</div>
                        <div class="bg-light p-2 rounded small">{{ $appointment->notes }}</div>
                    </div>
                    @endif
                    @if($appointment->meeting_link)
                    <div class="col-12">
                        <div class="text-muted small">Meeting Link</div>
                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-success">
                            <i class="bi bi-camera-video me-1"></i>{{ $appointment->meeting_link }}
                        </a>
                    </div>
                    @endif
                    @if($appointment->reject_reason)
                    <div class="col-12">
                        <div class="text-muted small">Rejection Reason</div>
                        <div class="alert alert-danger py-2 mb-0 small">{{ $appointment->reject_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>Status History</h6>
            </div>
            <div class="card-body p-0">
                @forelse($appointment->statusHistory as $log)
                <div class="d-flex align-items-start p-3 border-bottom">
                    <i class="bi bi-arrow-right-circle text-success me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="small">
                            <span class="badge bg-secondary me-1">{{ ucfirst($log->from_status) }}</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="badge bg-success ms-1">{{ ucfirst($log->to_status) }}</span>
                            by <strong>{{ $log->changedBy?->name }}</strong>
                        </div>
                        @if($log->notes)
                            <div class="text-muted small mt-1">{{ $log->notes }}</div>
                        @endif
                        <div class="text-muted" style="font-size:.7rem">
                            {{ $log->changed_at?->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-3 text-muted small text-center">No status changes yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Action Panel --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2 text-secondary"></i>Actions</h6>
            </div>
            <div class="card-body d-grid gap-2">

                {{-- Accept --}}
                @if($appointment->canBeAccepted())
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#acceptModal">
                    <i class="bi bi-check-circle me-1"></i>Accept Appointment
                </button>
                @endif

                {{-- Reject --}}
                @if($appointment->canBeRejected())
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-1"></i>Reject Appointment
                </button>
                @endif

                {{-- Complete --}}
                @if($appointment->canBeCompleted())
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completeModal">
                    <i class="bi bi-check2-all me-1"></i>Mark Completed
                </button>
                @endif

                {{-- Reschedule --}}
                @if($appointment->canBeRescheduled())
                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                    <i class="bi bi-calendar-plus me-1"></i>Propose Reschedule
                </button>
                @endif

                <a href="{{ route('expert.appointments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Accept Modal --}}
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('expert.appointments.accept', $appointment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold"><i class="bi bi-check-circle text-success me-2"></i>Accept Appointment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meeting Link <span class="text-muted small">(optional)</span></label>
                        <input type="url" name="meeting_link" class="form-control" placeholder="https://meet.google.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Accept</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('expert.appointments.reject', $appointment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Reject Appointment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4"
                                  placeholder="Please explain why you cannot take this appointment..." required minlength="10"></textarea>
                        <div class="form-text">This reason will be sent to the farmer.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Complete Modal --}}
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('expert.appointments.complete', $appointment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-primary"><i class="bi bi-check2-all me-2"></i>Mark as Completed</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Session Notes <span class="text-muted small">(optional)</span></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Summary of the consultation session..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark Completed</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Reschedule Modal --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('expert.appointments.reschedule', $appointment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-warning"><i class="bi bi-calendar-plus me-2"></i>Propose Reschedule</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="proposed_datetime" class="form-control" required
                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason <span class="text-muted small">(optional)</span></label>
                        <textarea name="reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white">Send Reschedule Request</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
