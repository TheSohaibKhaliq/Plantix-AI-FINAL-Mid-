@extends('expert.layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Expert Profile')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-success fs-5"></i>Profile Overview</h6>
                <a href="{{ route('expert.profile.edit') }}" class="btn btn-sm btn-success rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i>Edit Profile
                </a>
            </div>
        <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 mb-4 p-3 bg-light rounded-4 border">
                    @if($expert->avatar)
                        <img src="{{ Storage::url($expert->avatar) }}"
                             class="rounded-circle shadow-sm" style="width:90px;height:90px;object-fit:cover; border:3px solid #fff;">
                    @else
                        <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-success text-white"
                             style="width:90px;height:90px;font-size:2.5rem;font-weight:700; border:3px solid #fff;">
                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">{{ $expert->user->name }}</h4>
                        <div class="text-secondary mb-2 fw-medium fs-6">{{ $expert->specialty }}</div>
                        @if($profile)
                            <span class="badge rounded-pill me-2 px-3 py-2"
                                  style="background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9">
                                {{ ucfirst($profile->account_type) }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $profile->status_badge }} px-3 py-2">
                                {{ ucfirst($profile->approval_status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    @if($profile?->agency_name)
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Agency / Company</div>
                        <div class="fw-semibold text-dark fs-6">{{ $profile->agency_name }}</div>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Specialization</div>
                        <div class="fw-semibold text-dark">{{ $profile?->specialization ?? $expert->specialty ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Experience</div>
                        <div class="fw-semibold text-dark">{{ $profile?->experience_years ?? 0 }} years</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Hourly Rate</div>
                        <div class="fw-semibold text-dark">PKR {{ number_format($expert->hourly_rate) }}</div>
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
        <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success fs-5"></i>Specializations</h6>
            </div>
            <div class="card-body p-4">
                @forelse($specializations as $spec)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium text-dark">{{ $spec->name }}</span>
                    <span class="badge rounded-pill px-3 py-2"
                          style="background:{{ $spec->level === 'expert' ? '#e8f5e9' : ($spec->level === 'intermediate' ? '#fff3e0' : '#e3f2fd') }};
                                 color:{{ $spec->level === 'expert' ? '#2e7d32' : ($spec->level === 'intermediate' ? '#e65100' : '#1565c0') }};">
                        {{ ucfirst($spec->level) }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted opacity-50 py-3">
                    <i class="bi bi-tags p-3 fs-3 d-block"></i>
                    <p class="small mb-0">No specializations added.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Contact info --}}
        <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-telephone me-2 text-info fs-5"></i>Contact Info</h6>
            </div>
            <div class="card-body p-4 fs-6">
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
