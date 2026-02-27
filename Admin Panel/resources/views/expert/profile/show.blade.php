@extends('expert.layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Expert Profile')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-success"></i>Profile Overview</h6>
                <a href="{{ route('expert.profile.edit') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-pencil me-1"></i>Edit Profile
                </a>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-4 mb-4">
                    @if($expert->avatar)
                        <img src="{{ Storage::url($expert->avatar) }}"
                             class="rounded-circle" style="width:80px;height:80px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white"
                             style="width:80px;height:80px;font-size:2rem;font-weight:700">
                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-1">{{ $expert->user->name }}</h5>
                        <div class="text-muted small">{{ $expert->specialty }}</div>
                        @if($profile)
                            <span class="badge me-1"
                                  style="background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9">
                                {{ ucfirst($profile->account_type) }}
                            </span>
                            <span class="badge bg-{{ $profile->status_badge }}">
                                {{ ucfirst($profile->approval_status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    @if($profile?->agency_name)
                    <div class="col-sm-6">
                        <div class="text-muted small">Agency / Company</div>
                        <div class="fw-semibold">{{ $profile->agency_name }}</div>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <div class="text-muted small">Specialization</div>
                        <div>{{ $profile?->specialization ?? $expert->specialty ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Experience</div>
                        <div>{{ $profile?->experience_years ?? 0 }} years</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Hourly Rate</div>
                        <div>PKR {{ number_format($expert->hourly_rate) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Location</div>
                        <div>{{ $profile?->city }}, {{ $profile?->country }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Availability</div>
                        <div>
                            <i class="bi bi-circle-fill me-1 {{ $expert->is_available ? 'text-success' : 'text-secondary' }}"
                               style="font-size:.5rem"></i>
                            {{ $expert->is_available ? 'Available for consultations' : 'Currently unavailable' }}
                        </div>
                    </div>
                    @if($profile?->certifications)
                    <div class="col-12">
                        <div class="text-muted small">Certifications</div>
                        <div>{{ $profile->certifications }}</div>
                    </div>
                    @endif
                    @if($expert->bio)
                    <div class="col-12">
                        <div class="text-muted small">Bio</div>
                        <div>{{ $expert->bio }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Specializations --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success"></i>Specializations</h6>
            </div>
            <div class="card-body">
                @forelse($specializations as $spec)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">{{ $spec->name }}</span>
                    <span class="badge"
                          style="background:{{ $spec->level === 'expert' ? '#e8f5e9' : ($spec->level === 'intermediate' ? '#fff3e0' : '#e3f2fd') }};
                                 color:{{ $spec->level === 'expert' ? '#2e7d32' : ($spec->level === 'intermediate' ? '#e65100' : '#1565c0') }};
                                 font-size:.65rem">
                        {{ ucfirst($spec->level) }}
                    </span>
                </div>
                @empty
                <p class="text-muted small mb-0">No specializations added yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Contact info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-telephone me-2 text-info"></i>Contact Info</h6>
            </div>
            <div class="card-body small">
                <div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $expert->user->email }}</div>
                <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $profile?->contact_phone ?? $expert->user->phone ?? '—' }}</div>
                @if($profile?->website)
                <div class="mb-2">
                    <i class="bi bi-globe me-2 text-muted"></i>
                    <a href="{{ $profile->website }}" target="_blank" class="text-success">{{ $profile->website }}</a>
                </div>
                @endif
                @if($profile?->linkedin)
                <div>
                    <i class="bi bi-linkedin me-2 text-muted"></i>
                    <a href="{{ $profile->linkedin }}" target="_blank" class="text-success">LinkedIn</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
