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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2 text-success"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $expert->user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="{{ $expert->user->email }}" disabled>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $profile?->contact_phone ?? $expert->user->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Hourly Rate (PKR)</label>
                            <input type="number" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror"
                                   value="{{ old('hourly_rate', $expert->hourly_rate) }}" min="0">
                            @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Bio</label>
                            <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror"
                                      placeholder="Describe your expertise and experience...">{{ old('bio', $expert->bio) }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control"
                                   value="{{ old('city', $profile?->city) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control"
                                   value="{{ old('country', $profile?->country) }}">
                        </div>
                        <div class="col-sm-4">
                            <div class="form-check mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="is_available"
                                       name="is_available" value="1"
                                       {{ old('is_available', $expert->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_available">Available for appointments</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professional Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-briefcase me-2 text-success"></i>Professional Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($expert->user->role === 'agency_expert')
                        <div class="col-12">
                            <label class="form-label fw-semibold">Agency / Company Name</label>
                            <input type="text" name="agency_name" class="form-control @error('agency_name') is-invalid @enderror"
                                   value="{{ old('agency_name', $profile?->agency_name) }}">
                            @error('agency_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Area of Specialization</label>
                            <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror"
                                   value="{{ old('specialization', $profile?->specialization ?? $expert->specialty) }}"
                                   placeholder="e.g. Crop Science, Plant Pathology">
                            @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Years of Experience</label>
                            <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror"
                                   value="{{ old('experience_years', $profile?->experience_years) }}" min="0" max="60">
                            @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Certifications &amp; Qualifications</label>
                            <textarea name="certifications" rows="3" class="form-control @error('certifications') is-invalid @enderror"
                                      placeholder="List your academic degrees, certifications, or professional memberships...">{{ old('certifications', $profile?->certifications) }}</textarea>
                            @error('certifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $profile?->website) }}" placeholder="https://...">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">LinkedIn Profile</label>
                            <input type="url" name="linkedin" class="form-control"
                                   value="{{ old('linkedin', $profile?->linkedin) }}" placeholder="https://linkedin.com/in/...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Specializations --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success"></i>Specialization Tags</h6>
                    <button type="button" class="btn btn-sm btn-outline-success" id="addSpecBtn">
                        <i class="bi bi-plus me-1"></i>Add
                    </button>
                </div>
                <div class="card-body">
                    <div id="specializationsContainer">
                        @forelse($specializations as $i => $spec)
                        <div class="row g-2 mb-2 spec-row">
                            <div class="col-7">
                                <input type="text" name="specializations[{{ $i }}][name]"
                                       class="form-control form-control-sm"
                                       value="{{ $spec->name }}" placeholder="e.g. Wheat Diseases">
                            </div>
                            <div class="col-4">
                                <select name="specializations[{{ $i }}][level]" class="form-select form-select-sm">
                                    <option value="beginner"  {{ $spec->level==='beginner'?'selected':'' }}>Beginner</option>
                                    <option value="intermediate" {{ $spec->level==='intermediate'?'selected':'' }}>Intermediate</option>
                                    <option value="expert"    {{ $spec->level==='expert'?'selected':'' }}>Expert</option>
                                </select>
                            </div>
                            <div class="col-1 d-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-spec">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div id="emptySpecNote" class="text-muted small">No tags added. Click "Add" to add expertise tags.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-4">
            {{-- Avatar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-camera me-2 text-success"></i>Profile Photo</h6>
                </div>
                <div class="card-body text-center">
                    @if($expert->avatar)
                        <img src="{{ Storage::url($expert->avatar) }}" id="avatarPreview"
                             class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white mx-auto mb-3"
                             id="avatarPlaceholder" style="width:80px;height:80px;font-size:2rem;font-weight:700">
                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                        </div>
                        <img id="avatarPreview" class="rounded-circle mb-3 d-none"
                             style="width:80px;height:80px;object-fit:cover" src="">
                    @endif
                    <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm @error('avatar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif">
                    <div class="form-text">Max 2MB, JPG/PNG</div>
                    @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lock me-2 text-warning"></i>Change Password</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-sm @error('current_password') is-invalid @enderror">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-sm @error('new_password') is-invalid @enderror">
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-sm">
                    </div>
                    <div class="form-text">Leave blank to keep current password.</div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2 me-1"></i>Save Changes
                </button>
                <a href="{{ route('expert.profile.show') }}" class="btn btn-outline-secondary">Cancel</a>
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
    row.className = 'row g-2 mb-2 spec-row';
    row.innerHTML = `
        <div class="col-7"><input type="text" name="specializations[${specCount}][name]" class="form-control form-control-sm" placeholder="e.g. Wheat Diseases"></div>
        <div class="col-4">
            <select name="specializations[${specCount}][level]" class="form-select form-select-sm">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="expert">Expert</option>
            </select>
        </div>
        <div class="col-1 d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-spec"><i class="bi bi-trash"></i></button>
        </div>`;
    container.appendChild(row);
    specCount++;
    row.querySelector('.remove-spec').addEventListener('click', () => row.remove());
});
document.querySelectorAll('.remove-spec').forEach(btn => btn.addEventListener('click', () => btn.closest('.spec-row').remove()));
</script>
@endpush
@endsection
