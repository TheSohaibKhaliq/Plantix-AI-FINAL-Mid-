@extends('vendor.layouts.app')
@section('title', 'My Profile')
@section('page-title', 'Profile & Store Settings')

@section('content')
<div class="row g-4">

    {{-- ── Success / Error Alerts ──────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- ── Tabs ─────────────────────────────────────────────────────────────── --}}
    <div class="col-12">
        <ul class="nav nav-tabs mb-0" id="profileTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal">
                    <i class="bi bi-person me-1"></i>Personal Info
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#store">
                    <i class="bi bi-shop me-1"></i>Store Info
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security">
                    <i class="bi bi-shield-lock me-1"></i>Password & Security
                </button>
            </li>
        </ul>
    </div>

    <div class="col-12">
        <div class="tab-content" id="profileTabsContent">

            {{-- ══ TAB 1: Personal Info ═══════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="personal">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2 text-primary"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-4">
                                <div class="col-md-3 text-center">
                                    @if ($user->profile_photo)
                                        <img src="{{ Storage::url($user->profile_photo) }}"
                                             class="rounded-circle mb-2" width="100" height="100"
                                             style="object-fit:cover;" alt="Profile Photo">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex
                                                    align-items-center justify-content-center mb-2"
                                             style="width:100px;height:100px;font-size:2rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <label class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera me-1"></i>Change Photo
                                            <input type="file" name="profile_photo" accept="image/*"
                                                   class="d-none" onchange="previewPhoto(this)">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $user->name) }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                            <div class="form-text">Email cannot be changed here. Contact admin.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Phone Number</label>
                                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', $user->phone) }}">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Role</label>
                                            <input type="text" class="form-control bg-light text-capitalize" value="{{ $user->role }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ TAB 2: Store Info ════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="store">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="bi bi-shop me-2 text-success"></i>Store Information</h5>
                    </div>
                    <div class="card-body">
                        @if ($vendor)
                            <form action="{{ route('vendor.profile.store.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-4">
                                    {{-- Store logo & cover --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Store Logo</label>
                                        @if ($vendor->image)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($vendor->image) }}"
                                                     height="80" style="object-fit:contain;" alt="Store Logo">
                                            </div>
                                        @endif
                                        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Cover Photo</label>
                                        @if ($vendor->cover_photo)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($vendor->cover_photo) }}"
                                                     height="80" style="object-fit:cover;width:100%;" alt="Cover">
                                            </div>
                                        @endif
                                        <input type="file" name="cover_photo" accept="image/*" class="form-control @error('cover_photo') is-invalid @enderror">
                                        @error('cover_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                               value="{{ old('title', $vendor->title) }}" required>
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Store Phone</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', $vendor->phone) }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Description</label>
                                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $vendor->description) }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Address</label>
                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                               value="{{ old('address', $vendor->address) }}">
                                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Opening Time</label>
                                        <input type="time" name="open_time" class="form-control @error('open_time') is-invalid @enderror"
                                               value="{{ old('open_time', $vendor->open_time) }}">
                                        @error('open_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Closing Time</label>
                                        <input type="time" name="close_time" class="form-control @error('close_time') is-invalid @enderror"
                                               value="{{ old('close_time', $vendor->close_time) }}">
                                        @error('close_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Delivery Fee ({{ config('plantix.currency_symbol') }})</label>
                                        <input type="number" step="0.01" name="delivery_fee"
                                               class="form-control @error('delivery_fee') is-invalid @enderror"
                                               value="{{ old('delivery_fee', $vendor->delivery_fee) }}">
                                        @error('delivery_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Min. Order Amount ({{ config('plantix.currency_symbol') }})</label>
                                        <input type="number" step="0.01" name="min_order_amount"
                                               class="form-control @error('min_order_amount') is-invalid @enderror"
                                               value="{{ old('min_order_amount', $vendor->min_order_amount) }}">
                                        @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="p-3 rounded bg-light mb-4">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="small text-muted">Rating</div>
                                            <div class="fw-bold">
                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                {{ number_format($vendor->rating, 1) }}
                                                <span class="text-muted small">({{ $vendor->review_count }} reviews)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Status</div>
                                            @if ($vendor->is_approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending Approval</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Active</div>
                                            @if ($vendor->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="bi bi-save me-1"></i>Update Store Info
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No store profile is linked to your account. Please contact the admin to set up your store.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══ TAB 3: Security ═════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="security">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-danger"></i>Change Password</h5>
                    </div>
                    <div class="card-body" style="max-width:480px;">
                        <form action="{{ route('vendor.profile.password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Minimum 8 characters, mixed case + numbers.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-key me-1"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>{{-- end tab-content --}}
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = input.closest('.col-md-3').querySelector('img, .rounded-circle');
            if (img && img.tagName === 'IMG') img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
