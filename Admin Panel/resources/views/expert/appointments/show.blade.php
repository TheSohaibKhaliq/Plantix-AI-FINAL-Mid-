@extends('expert.layouts.app')
@section('title', 'Appointment #' . $appointment->id)
@section('page-title', 'Appointment Details')

@section('content')
<div class="row g-4 mb-4">
    {{-- Main details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4 hover-card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-check me-2 text-success"></i>Appointment #{{ $appointment->id }}
                </h6>
                <span class="badge rounded-pill bg-{{ $appointment->status_badge }} px-3 py-2">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Farmer</div>
                        <div class="fw-semibold fs-6">{{ $appointment->user->name }}</div>
                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $appointment->user->email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Scheduled</div>
                        <div class="fw-semibold fs-6"><i class="bi bi-calendar3 me-1 text-success"></i>{{ $appointment->scheduled_at?->format('D, d M Y') }}</div>
                        <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $appointment->scheduled_at?->format('H:i') }} ({{ $appointment->duration_minutes }} min)</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Topic</div>
                        <div class="fw-medium">{{ $appointment->topic ?? 'General Consultation' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Fee / Payment</div>
                        <div class="fw-semibold">PKR {{ number_format($appointment->fee) }}
                            <span class="badge rounded-pill bg-{{ $appointment->payment_status === 'paid' ? 'success' : 'warning' }} ms-2">
                                {{ ucfirst($appointment->payment_status) }}
                            </span>
                        </div>
                    </div>
                    @if($appointment->notes)
                    <div class="col-12 mt-4">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Farmer Notes</div>
                        <div class="bg-light p-3 rounded-3 small border">{{ $appointment->notes }}</div>
                    </div>
                    @endif
                    @if($appointment->meeting_link)
                    <div class="col-12 mt-4">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Meeting Link</div>
                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-camera-video me-2"></i>Join Meeting
                        </a>
                    </div>
                    @endif
                    @if($appointment->reject_reason)
                    <div class="col-12 mt-4">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Rejection Reason</div>
                        <div class="alert alert-danger py-2 mb-0 small"><i class="bi bi-exclamation-triangle me-2"></i>{{ $appointment->reject_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status History --}}
        <div class="card border-0 shadow-sm hover-card">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>Status History</h6>
            </div>
            <div class="card-body p-0">
                @forelse($appointment->statusHistory as $log)
                <div class="d-flex align-items-start p-3 border-bottom hover-bg-light">
                    <i class="bi bi-arrow-right-circle text-success fs-5 me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="small">
                            <span class="badge rounded-pill bg-secondary me-1">{{ ucfirst($log->from_status) }}</span>
                            <i class="bi bi-arrow-right text-muted mx-1"></i>
                            <span class="badge rounded-pill bg-success ms-1">{{ ucfirst($log->to_status) }}</span>
                            <span class="ms-2 text-muted">by <strong>{{ $log->changedBy?->name }}</strong></span>
                        </div>
                        @if($log->notes)
                            <div class="text-secondary small mt-2 bg-light p-2 rounded">{{ $log->notes }}</div>
                        @endif
                        <div class="text-muted mt-2" style="font-size:.70rem">
                            <i class="bi bi-clock me-1"></i>{{ $log->changed_at?->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-muted small text-center">
                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-light"></i>
                    No status changes yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Action Panel --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm hover-card">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2 text-secondary"></i>Actions</h6>
            </div>
            <div class="card-body p-4 d-grid gap-3">

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

                <hr class="my-2 border-secondary opacity-25">

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
