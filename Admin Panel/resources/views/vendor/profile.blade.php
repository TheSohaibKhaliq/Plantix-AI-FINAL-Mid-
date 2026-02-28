@extends('vendor.layouts.app')
@section('title', 'My Profile')
@section('page-title', 'Profile & Store Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge-fill me-2 text-success"></i>Profile & Store Settings</h4>
        <span class="text-muted small fw-medium mt-1 d-block">Manage your personal details and store configuration</span>
    </div>
</div>

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
    <div class="col-12 mt-2">
        <ul class="nav nav-pills custom-pills mb-2" id="profileTabs" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active rounded-pill px-4 py-2 fw-medium shadow-sm transition-all" data-bs-toggle="pill" data-bs-target="#personal" type="button" role="tab">
                    <i class="bi bi-person-fill me-2"></i>Personal Info
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 fw-medium shadow-sm transition-all" data-bs-toggle="pill" data-bs-target="#store" type="button" role="tab">
                    <i class="bi bi-shop me-2"></i>Store Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 fw-medium shadow-sm transition-all text-danger" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                    <i class="bi bi-shield-lock-fill me-2"></i>Password & Security
                </button>
            </li>
        </ul>
        
        <style>
            .custom-pills .nav-link {
                background-color: #fff;
                color: #6c757d;
                border: 1px solid #dee2e6;
            }
            .custom-pills .nav-link:hover {
                background-color: #f8f9fa;
                color: #212529;
            }
            .custom-pills .nav-link.active {
                background-color: var(--bs-success);
                color: #fff;
                border-color: var(--bs-success);
            }
            .custom-pills .nav-link.text-danger:not(.active) {
                color: #dc3545 !important;
                border-color: rgba(220, 53, 69, 0.2);
                background-color: rgba(220, 53, 69, 0.05);
            }
            .custom-pills .nav-link.text-danger.active {
                background-color: #dc3545;
                color: #fff !important;
                border-color: #dc3545;
            }
            .transition-all {
                transition: all 0.3s ease;
            }
        </style>
    </div>

    <div class="col-12">
        <div class="tab-content" id="profileTabsContent">

            {{-- ══ TAB 1: Personal Info ═══════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary fs-4"></i>Personal Details</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-4">
                                <div class="col-md-3 text-center mb-4 mb-md-0 pt-2">
                                    <div class="position-relative d-inline-block">
                                        @if ($user->profile_photo)
                                            <img src="{{ Storage::url($user->profile_photo) }}"
                                                 class="rounded-circle shadow-sm border border-4 border-white mb-3" width="140" height="140"
                                                 style="object-fit:cover;" alt="Profile Photo">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex
                                                        align-items-center justify-content-center shadow-sm border border-4 border-white mb-3"
                                                 style="width:140px;height:140px;font-size:3.5rem;font-weight:700;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <label class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 mb-3 me-2 shadow" 
                                               style="width:36px; height:36px; padding:6px; cursor:pointer;" title="Change Photo">
                                            <i class="bi bi-camera-fill"></i>
                                            <input type="file" name="profile_photo" accept="image/*"
                                                   class="d-none" onchange="previewPhoto(this)">
                                        </label>
                                    </div>
                                    <div class="text-muted small fw-medium mt-1">Recommended size: 300x300px</div>
                                </div>
                                <div class="col-md-9 px-md-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $user->name) }}" placeholder="Enter your full name" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-envelope text-muted"></i></span>
                                                <input type="email" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 text-muted" value="{{ $user->email }}" readonly>
                                            </div>
                                            <div class="form-text small mt-1"><i class="bi bi-info-circle me-1"></i>Contact admin to change email.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-telephone text-muted"></i></span>
                                                <input type="text" name="phone" class="form-control form-control-lg fs-6 rounded-end-3 bg-light border-0 @error('phone') is-invalid @enderror"
                                                       value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000">
                                            </div>
                                            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Account Role</label>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fs-6 fw-bold mt-1 d-inline-block">
                                                <i class="bi bi-person-badge me-1"></i>{{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-4 border-top">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-check-circle me-2"></i>Update Personal Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ TAB 2: Store Info ════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="store" role="tabpanel">
                <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shop me-2 text-success fs-4"></i>Store Information</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @if ($vendor)
                            <form action="{{ route('vendor.profile.store.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-4 mb-5">
                                    {{-- Store logo & cover --}}
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-2">Primary Store Logo</label>
                                        <div class="d-flex align-items-center gap-3 bg-light p-3 rounded-3 border">
                                            @if ($vendor->image)
                                                <img src="{{ Storage::url($vendor->image) }}"
                                                     class="rounded-3 bg-white border shadow-sm" height="70" width="70" style="object-fit:contain;" alt="Store Logo">
                                            @else
                                                <div class="rounded-3 bg-white border shadow-sm d-flex align-items-center justify-content-center text-muted" style="height:70px;width:70px;">
                                                    <i class="bi bi-image fs-3"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <input type="file" name="image" accept="image/*" class="form-control form-control-sm border-0 @error('image') is-invalid @enderror">
                                                <div class="form-text small mt-1">Recommended size: 500x500px</div>
                                            </div>
                                        </div>
                                        @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-2">Store Cover Photo</label>
                                        <div class="d-flex flex-column gap-2 bg-light p-3 rounded-3 border">
                                            @if ($vendor->cover_photo)
                                                <img src="{{ Storage::url($vendor->cover_photo) }}"
                                                     class="rounded-3 bg-white shadow-sm w-100" height="70" style="object-fit:cover;" alt="Cover">
                                            @else
                                                <div class="rounded-3 bg-white shadow-sm w-100 d-flex align-items-center justify-content-center text-muted" style="height:70px;">
                                                    <i class="bi bi-image fs-3"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <input type="file" name="cover_photo" accept="image/*" class="form-control form-control-sm border-0 @error('cover_photo') is-invalid @enderror">
                                                <div class="form-text small mt-1">Recommended size: 1200x400px</div>
                                            </div>
                                        </div>
                                        @error('cover_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12 my-1"></div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Store Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-shop text-muted"></i></span>
                                            <input type="text" name="title" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('title') is-invalid @enderror"
                                                   value="{{ old('title', $vendor->title) }}" placeholder="Your store's public name" required>
                                        </div>
                                        @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Store Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-telephone text-muted"></i></span>
                                            <input type="text" name="phone" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', $vendor->phone) }}" placeholder="Customer contact number">
                                        </div>
                                        @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Store Description</label>
                                        <textarea name="description" rows="4" class="form-control fs-6 rounded-3 bg-light border-0 @error('description') is-invalid @enderror" placeholder="Describe your store, what you sell, and your mission...">{{ old('description', $vendor->description) }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Physical Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-geo-alt text-muted"></i></span>
                                            <input type="text" name="address" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('address') is-invalid @enderror"
                                                   value="{{ old('address', $vendor->address) }}" placeholder="Full physical or mailing address">
                                        </div>
                                        @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12 my-1"><hr class="text-muted opacity-25"></div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Opening Time</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-clock text-muted"></i></span>
                                            <input type="time" name="open_time" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('open_time') is-invalid @enderror"
                                                   value="{{ old('open_time', $vendor->open_time) }}">
                                        </div>
                                        @error('open_time')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Closing Time</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-clock-history text-muted"></i></span>
                                            <input type="time" name="close_time" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('close_time') is-invalid @enderror"
                                                   value="{{ old('close_time', $vendor->close_time) }}">
                                        </div>
                                        @error('close_time')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Delivery Fee</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3 fw-bold">{{ config('plantix.currency_symbol', 'PKR') }}</span>
                                            <input type="number" step="0.01" name="delivery_fee"
                                                   class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('delivery_fee') is-invalid @enderror"
                                                   value="{{ old('delivery_fee', $vendor->delivery_fee) }}" placeholder="0.00">
                                        </div>
                                        @error('delivery_fee')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Minimum Order Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 rounded-start-3 fw-bold">{{ config('plantix.currency_symbol', 'PKR') }}</span>
                                            <input type="number" step="0.01" name="min_order_amount"
                                                   class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('min_order_amount') is-invalid @enderror"
                                                   value="{{ old('min_order_amount', $vendor->min_order_amount) }}" placeholder="0.00">
                                        </div>
                                        @error('min_order_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 bg-light mb-4 border shadow-sm">
                                    <div class="row text-center g-3">
                                        <div class="col-md-4 border-end">
                                            <div class="small text-uppercase fw-bold text-muted mb-2">Store Rating</div>
                                            <div class="fw-bold fs-4 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-star-fill text-warning me-2 fs-5"></i>
                                                {{ number_format($vendor->rating, 1) }}
                                            </div>
                                            <div class="text-muted small mt-1">Based on {{ $vendor->review_count }} reviews</div>
                                        </div>
                                        <div class="col-md-4 border-end">
                                            <div class="small text-uppercase fw-bold text-muted mb-2">Approval Status</div>
                                            <div class="mt-2">
                                                @if ($vendor->is_approved)
                                                    <span class="badge rounded-pill bg-success px-4 py-2 shadow-sm border border-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                @else
                                                    <span class="badge rounded-pill bg-warning text-dark px-4 py-2 shadow-sm border border-warning"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-uppercase fw-bold text-muted mb-2">Operational Status</div>
                                            <div class="mt-2">
                                                @if ($vendor->is_active)
                                                    <span class="badge rounded-pill border border-success text-success bg-success bg-opacity-10 px-4 py-2 shadow-sm"><i class="bi bi-shop-window me-1"></i>Active</span>
                                                @else
                                                    <span class="badge rounded-pill border border-danger text-danger bg-danger bg-opacity-10 px-4 py-2 shadow-sm"><i class="bi bi-door-closed me-1"></i>Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4 pt-4 border-top">
                                    <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i>Update Store Setup
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
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px; max-width:600px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-danger fs-4"></i>Password Settings</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('vendor.profile.password') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold small mb-1">Current Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" name="current_password"
                                           class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('current_password') is-invalid @enderror" placeholder="Enter current password" required>
                                </div>
                                @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <hr class="text-muted opacity-25 my-4">

                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold small mb-1">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-key text-muted"></i></span>
                                    <input type="password" name="password"
                                           class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('password') is-invalid @enderror" placeholder="Enter a strong new password" required>
                                </div>
                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div class="form-text small mt-2"><i class="bi bi-shield-check me-1 text-success"></i>Minimum 8 characters, mix of cases and numbers.</div>
                            </div>
                            <div class="mb-5">
                                <label class="form-label text-muted text-uppercase fw-bold small mb-1">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-3"><i class="bi bi-key-fill text-muted"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3" placeholder="Re-type your new password" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end text-end mt-2 pt-2 border-top">
                                <button type="submit" class="btn btn-danger rounded-pill px-5 py-2 mt-3 fw-bold shadow-sm">
                                    <i class="bi bi-shield-lock me-2"></i>Apply New Password
                                </button>
                            </div>
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
