@extends('layouts.frontend')

@section('title', 'Sign Up | Plantix-AI')

@section('footer')
@include('partials.footer-alt')
@endsection

@section('page_scripts')@endsection

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px); background: #f8fafc; padding: 40px 0;">
    <div class="container-agri w-100">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card-agri" style="padding: 40px;">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/img/plantix-ai-logo.png') }}" alt="Plantix-AI" style="height: 48px; margin-bottom: 24px;">
                        <h3 class="fw-bold text-dark" style="font-size: 24px;">Create your account</h3>
                        <p class="text-muted" style="font-size: 15px;">Join the Plantix-AI farming community</p>
                    </div>

                    {{-- Session / validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" style="border-radius: var(--agri-radius-sm);">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-agri @error('name') is-invalid @enderror"
                                   placeholder="Your full name" value="{{ old('name') }}" required minlength="2" maxlength="100"
                                   autocomplete="name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-agri @error('email') is-invalid @enderror"
                                   placeholder="Enter your email" value="{{ old('email') }}" required
                                   autocomplete="email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="signupPassword" class="form-agri @error('password') is-invalid @enderror"
                                   placeholder="Min 8 characters, letters &amp; numbers" required minlength="8"
                                   autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-agri"
                                   placeholder="Repeat your password" required minlength="8"
                                   autocomplete="new-password">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Phone <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="tel" name="phone" class="form-agri @error('phone') is-invalid @enderror"
                                   placeholder="e.g. +92 300 1234567" value="{{ old('phone') }}" maxlength="30"
                                   autocomplete="tel">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button class="btn-agri btn-agri-primary w-100 mb-4" type="submit" style="font-size: 16px; padding: 12px;">
                            Create Account
                        </button>
                    </form>

                    <p class="text-center text-muted mb-2" style="font-size: 15px;">
                        Already have an account? <a href="{{ route('signin') }}" class="text-success text-decoration-none fw-bold">Sign in</a>
                    </p>

                    <hr style="border-color: #e0e0e0;">

                    <div class="text-center p-3" style="background: #f0f9f0; border-radius: 10px; border: 1px solid #c8e6c9;">
                        <p class="mb-1 fw-semibold text-dark" style="font-size: 14px;">
                            <i class="fas fa-user-tie text-success me-1"></i> Are you an agricultural expert?
                        </p>
                        <p class="text-muted mb-2" style="font-size: 13px;">Join as a verified expert and connect with farmers.</p>
                        <a href="{{ route('expert.register') }}" class="btn-agri btn-agri-outline" style="font-size: 14px; padding: 8px 20px; display: inline-block; border: 2px solid var(--agri-primary); color: var(--agri-primary); text-decoration: none; border-radius: 8px;">
                            Register as Expert
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
