<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendor Registration — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a3c34 0%, #2e7d32 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .register-card {
            background: #fff; border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 100%; max-width: 500px;
            padding: 2.5rem;
            margin: 0 auto;
        }
        .register-card .brand { text-align: center; margin-bottom: 2rem; }
        .register-card .brand i { font-size: 3rem; color: #2e7d32; }
        .register-card .brand h4 { font-weight: 700; margin-top: .5rem; }
        .register-card .brand p { color: #666; font-size: 14px; }
        .btn-vendor { background: #2e7d32; color: #fff; border: none; }
        .btn-vendor:hover { background: #1b5e20; color: #fff; }
        .form-text { font-size: 12px; color: #999; }
        .input-group-text { background: #f8f9fa; border-color: #dee2e6; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="brand">
        <i class="bi bi-shop"></i>
        <h4>Register as Vendor</h4>
        <p>Set up your agricultural store</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Registration Failed</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.register') }}">
        @csrf

        {{-- Personal Information --}}
        <h6 class="fw-bold text-dark mb-3 mt-4">Personal Information</h6>

        <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus placeholder="Your full name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required placeholder="vendor@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">We'll use this to log in and send notifications.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Phone Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" required placeholder="03001234567">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">Pakistani number format (e.g., 03001234567)</div>
        </div>

        {{-- Store Information --}}
        <h6 class="fw-bold text-dark mb-3 mt-4">Store Information</h6>

        <div class="mb-3">
            <label class="form-label fw-semibold">Store Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shop"></i></span>
                <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror"
                       value="{{ old('store_name') }}" required placeholder="Your agricultural store name">
                @error('store_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">Must be unique and descriptive.</div>
        </div>

        {{-- Security Information --}}
        <h6 class="fw-bold text-dark mb-3 mt-4">Security</h6>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       required minlength="8" placeholder="Min. 8 characters">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">Use a strong password with letters, numbers, and symbols.</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
            </div>
        </div>

        {{-- Terms & Conditions --}}
        <div class="mb-4 p-3 border border-light rounded" style="background: #f8f9fa;">
            <div class="form-check">
                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" name="terms" id="terms" required>
                <label class="form-check-label small" for="terms">
                    I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a> and 
                    <a href="#" class="text-decoration-none">Privacy Policy</a>. I understand my account requires admin approval before going live.
                </label>
                @error('terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-vendor w-100 py-2 fw-semibold mb-3">
            <i class="bi bi-check-circle me-1"></i>Create Vendor Account
        </button>

        <div class="text-center">
            <p class="text-muted small mb-2">Already have an account?</p>
            <a href="{{ route('vendor.login') }}" class="text-decoration-none text-success fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In Here
            </a>
        </div>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to website
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
