@extends('expert.layouts.app')
@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
<form method="POST" action="{{ route('expert.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Left column --}}
        <div class="col-lg-8">
            {{-- Personal Info --}}
            <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person me-2 text-success fs-4"></i>Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('name') is-invalid @enderror"
                                   value="{{ old('name', $expert->user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Email Address</label>
                            <input type="email" class="form-control form-control-lg fs-6 rounded-3 bg-secondary bg-opacity-10 border-0" value="{{ $expert->user->email }}" disabled>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Phone Number</label>
                            <input type="tel" name="phone" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $profile?->contact_phone ?? $expert->user->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Hourly Rate (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success bg-opacity-10 text-success border-0 rounded-start-3 fw-bold">PKR</span>
                                <input type="number" name="hourly_rate" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('hourly_rate') is-invalid @enderror"
                                       value="{{ old('hourly_rate', $expert->hourly_rate) }}" min="0">
                            </div>
                            @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Professional Bio</label>
                            <textarea name="bio" rows="4" class="form-control fs-6 rounded-3 bg-light border-0 @error('bio') is-invalid @enderror"
                                      placeholder="Describe your expertise, background, and experience...">{{ old('bio', $expert->bio) }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">City</label>
                            <input type="text" name="city" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0"
                                   value="{{ old('city', $profile?->city) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Country</label>
                            <input type="text" name="country" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0"
                                   value="{{ old('country', $profile?->country) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1 w-100">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input fs-4 cursor-pointer" type="checkbox" role="switch" id="is_available"
                                       name="is_available" value="1"
                                       {{ old('is_available', $expert->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold mt-1 ms-2" for="is_available">Available for Bookings</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professional Info --}}
            <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-briefcase me-2 text-success fs-4"></i>Professional Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @if($expert->user->role === 'agency_expert')
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Agency / Company Name</label>
                            <input type="text" name="agency_name" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('agency_name') is-invalid @enderror"
                                   value="{{ old('agency_name', $profile?->agency_name) }}">
                            @error('agency_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Area of Specialization</label>
                            <input type="text" name="specialization" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('specialization') is-invalid @enderror"
                                   value="{{ old('specialization', $profile?->specialization ?? $expert->specialty) }}"
                                   placeholder="e.g. Crop Science, Plant Pathology">
                            @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Years of Experience</label>
                            <input type="number" name="experience_years" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('experience_years') is-invalid @enderror"
                                   value="{{ old('experience_years', $profile?->experience_years) }}" min="0" max="60">
                            @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Certifications & Qualifications</label>
                            <textarea name="certifications" rows="3" class="form-control rounded-3 bg-light border-0 @error('certifications') is-invalid @enderror"
                                      placeholder="List your academic degrees, certifications, or professional memberships...">{{ old('certifications', $profile?->certifications) }}</textarea>
                            @error('certifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Website URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-link-45deg"></i></span>
                                <input type="url" name="website" class="form-control form-control-lg fs-6 rounded-end-3 bg-light border-0"
                                       value="{{ old('website', $profile?->website) }}" placeholder="https://...">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">LinkedIn Profile</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-linkedin"></i></span>
                                <input type="url" name="linkedin" class="form-control form-control-lg fs-6 rounded-end-3 bg-light border-0"
                                       value="{{ old('linkedin', $profile?->linkedin) }}" placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Specializations --}}
            <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success fs-4"></i>Specialization Tags</h5>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="addSpecBtn">
                        <i class="bi bi-plus me-1"></i>Add Tag
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="specializationsContainer">
                        @forelse($specializations as $i => $spec)
                        <div class="row g-3 mb-3 spec-row align-items-center bg-light p-2 rounded-3 border">
                            <div class="col-7">
                                <input type="text" name="specializations[{{ $i }}][name]"
                                       class="form-control form-control-md border-0 bg-white"
                                       value="{{ $spec->name }}" placeholder="e.g. Wheat Diseases">
                            </div>
                            <div class="col-4">
                                <select name="specializations[{{ $i }}][level]" class="form-select form-select-md border-0 bg-white shadow-sm">
                                    <option value="beginner"  {{ $spec->level==='beginner'?'selected':'' }}>Beginner</option>
                                    <option value="intermediate" {{ $spec->level==='intermediate'?'selected':'' }}>Intermediate</option>
                                    <option value="expert"    {{ $spec->level==='expert'?'selected':'' }}>Expert</option>
                                </select>
                            </div>
                            <div class="col-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-spec rounded-circle shadow-sm" style="width:32px; height:32px; padding:0;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div id="emptySpecNote" class="text-center text-muted p-4">
                            <i class="bi bi-tags p-3 fs-3 d-block opacity-50"></i>
                            <p class="mb-0 text-muted">No expertise tags added. Click "Add Tag" to showcase your skills.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-4">
            {{-- Avatar --}}
            <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-camera me-2 text-success fs-4"></i>Profile Photo</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4 position-relative d-inline-block">
                        @if($expert->avatar)
                            <img src="{{ Storage::url($expert->avatar) }}" id="avatarPreview"
                                 class="rounded-circle shadow-sm border border-3 border-white" style="width:120px;height:120px;object-fit:cover">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-sm border border-3 border-white mx-auto"
                                 id="avatarPlaceholder" style="width:120px;height:120px;font-size:3rem;font-weight:700">
                                {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                            </div>
                            <img id="avatarPreview" class="rounded-circle shadow-sm border border-3 border-white d-none mx-auto"
                                 style="width:120px;height:120px;object-fit:cover" src="">
                        @endif
                        <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width:36px; height:36px; cursor:pointer; transform: translate(10%, 10%);">
                            <i class="bi bi-pencil-fill small"></i>
                        </label>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" class="d-none @error('avatar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif">
                    <div class="text-muted small fw-medium mt-2">Allowed JPG, GIF or PNG. Max size of 2MB</div>
                    @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-warning fs-4"></i>Security</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('current_password') is-invalid @enderror">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('new_password') is-invalid @enderror">
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0">
                    </div>
                    <div class="form-text mt-2 text-muted fw-medium"><i class="bi bi-info-circle me-1"></i>Leave blank to keep your current password unmodified.</div>
                </div>
            </div>

            <div class="d-grid gap-3">
                <button type="submit" class="btn btn-success rounded-pill py-3 fw-bold fs-5 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Save All Changes
                </button>
                <a href="{{ route('expert.profile.show') }}" class="btn btn-light text-dark border rounded-pill py-3 fw-bold fs-6">
                    Cancel Updates
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const url = URL.createObjectURL(this.files[0]);
        const preview = document.getElementById('avatarPreview');
        const placeholder = document.getElementById('avatarPlaceholder');
        preview.src = url;
        preview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
    }
});

// Specialization rows
let specCount = {{ count($specializations) }};
document.getElementById('addSpecBtn').addEventListener('click', function() {
    const container = document.getElementById('specializationsContainer');
    const empty = document.getElementById('emptySpecNote');
    if (empty) empty.remove();
    const row = document.createElement('div');
    row.className = 'row g-3 mb-3 spec-row align-items-center bg-light p-2 rounded-3 border';
    row.innerHTML = `
        <div class="col-7"><input type="text" name="specializations[${specCount}][name]" class="form-control form-control-md border-0 bg-white" placeholder="e.g. Wheat Diseases"></div>
        <div class="col-4">
            <select name="specializations[${specCount}][level]" class="form-select form-select-md border-0 bg-white shadow-sm">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="expert">Expert</option>
            </select>
        </div>
        <div class="col-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger remove-spec rounded-circle shadow-sm" style="width:32px; height:32px; padding:0;"><i class="bi bi-trash"></i></button>
        </div>`;
    container.appendChild(row);
    specCount++;
    row.querySelector('.remove-spec').addEventListener('click', () => row.remove());
});
document.querySelectorAll('.remove-spec').forEach(btn => btn.addEventListener('click', () => btn.closest('.spec-row').remove()));
</script>
@endpush
@endsection
