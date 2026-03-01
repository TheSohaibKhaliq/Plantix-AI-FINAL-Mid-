@extends('layouts.frontend')

@section('title', 'Verify Your Email | Plantix-AI')

@section('footer')
@include('partials.footer-alt')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px); background: #f8fafc; padding: 40px 0;">
    <div class="container-agri w-100">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card-agri" style="padding: 40px; text-align: center;">

                    <div class="mb-4">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-envelope-open-text" style="font-size: 32px; color: var(--agri-primary);"></i>
                        </div>
                        <h3 class="fw-bold text-dark" style="font-size: 24px;">Verify your email</h3>
                        <p class="text-muted" style="font-size: 15px; max-width: 380px; margin: 0 auto;">
                            We sent a verification link to <strong>{{ Auth::user()->email }}</strong>.
                            Click the link in the email to activate your account.
                        </p>
                    </div>

                    {{-- Status messages --}}
                    @if (session('success'))
                        <div class="alert alert-success mb-4" style="border-radius: var(--agri-radius-sm);">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('resent') || session('status'))
                        <div class="alert alert-success mb-4" style="border-radius: var(--agri-radius-sm);">
                            <i class="fas fa-check-circle me-2"></i>
                            A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    <div style="background: #fff8e1; border: 1px solid #ffe082; border-radius: 10px; padding: 16px; margin-bottom: 24px; text-align: left;">
                        <p class="mb-1 fw-semibold" style="font-size: 14px; color: #5c4a00;">
                            <i class="fas fa-info-circle me-1"></i> Didn't receive the email?
                        </p>
                        <ul class="mb-0 text-muted" style="font-size: 13px; padding-left: 20px;">
                            <li>Check your spam or junk folder</li>
                            <li>Make sure <strong>{{ Auth::user()->email }}</strong> is correct</li>
                            <li>Wait a few minutes and check again</li>
                        </ul>
                    </div>

                    {{-- Resend form --}}
                    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn-agri btn-agri-primary w-100" style="font-size: 15px; padding: 12px;">
                            <i class="fas fa-paper-plane me-2"></i> Resend Verification Email
                        </button>
                    </form>

                    {{-- Sign out --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-agri btn-agri-outline w-100" style="font-size: 14px; padding: 10px; border: 2px solid var(--agri-border); color: var(--agri-text-muted); background: transparent; border-radius: 8px;">
                            Sign out and use a different account
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
