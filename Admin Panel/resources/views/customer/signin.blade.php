@extends('layouts.frontend')

@section('title', 'Sign In | Plantix-AI')

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
                        <h3 class="fw-bold text-dark" style="font-size: 24px;">Welcome Back</h3>
                        <p class="text-muted" style="font-size: 15px;">Sign in to your Plantix-AI account</p>
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

                    @if (session('success'))
                        <div class="alert alert-success mb-4" style="border-radius: var(--agri-radius-sm);">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-agri @error('email') is-invalid @enderror"
                                   placeholder="Enter your email" value="{{ old('email') }}" required
                                   autocomplete="email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark" style="font-size: 14px;">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-agri @error('password') is-invalid @enderror"
                                   placeholder="Enter your password" required
                                   autocomplete="current-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer; font-size:14px; color: var(--agri-text-muted);">
                                <input type="checkbox" name="remember" value="1" style="width:16px;height:16px;">
                                Remember me
                            </label>
                            <a href="{{ route('password.forgot') }}" class="text-success text-decoration-none" style="font-size: 14px; font-weight: 500;">Forgot password?</a>
                        </div>

                        <button class="btn-agri btn-agri-primary w-100 mb-4" type="submit" style="font-size: 16px; padding: 12px;">
                            Sign In
                        </button>
                    </form>

                    <p class="text-center text-muted mb-2" style="font-size: 15px;">
                        Don't have an account? <a href="{{ route('signup') }}" class="text-success text-decoration-none fw-bold">Create one</a>
                    </p>

                    <hr style="border-color: #e0e0e0;">

                    <div class="text-center p-3" style="background: #f0f9f0; border-radius: 10px; border: 1px solid #c8e6c9;">
                        <p class="mb-1 fw-semibold text-dark" style="font-size: 14px;">
                            <i class="fas fa-user-tie text-success me-1"></i> Are you an agricultural expert?
                        </p>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <a href="{{ route('expert.login') }}" class="btn-agri btn-agri-outline" style="font-size: 13px; padding: 7px 16px; display: inline-block; border: 2px solid var(--agri-primary); color: var(--agri-primary); text-decoration: none; border-radius: 8px;">
                                Expert Sign In
                            </a>
                            <a href="{{ route('expert.register') }}" class="btn-agri" style="font-size: 13px; padding: 7px 16px; display: inline-block; background: var(--agri-primary); color: #fff; text-decoration: none; border-radius: 8px; border: 2px solid var(--agri-primary);">
                                Register as Expert
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
