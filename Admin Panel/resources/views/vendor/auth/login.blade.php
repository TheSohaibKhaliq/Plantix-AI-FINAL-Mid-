<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendor Login — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a3c34 0%, #2e7d32 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            background: #fff; border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 100%; max-width: 420px;
            padding: 2.5rem;
        }
        .login-card .brand { text-align: center; margin-bottom: 2rem; }
        .login-card .brand i { font-size: 3rem; color: #2e7d32; }
        .login-card .brand h4 { font-weight: 700; margin-top: .5rem; }
        .btn-vendor { background: #2e7d32; color: #fff; border: none; }
        .btn-vendor:hover { background: #1b5e20; color: #fff; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <i class="bi bi-shop"></i>
        <h4>Vendor Panel</h4>
        <p class="text-muted small">Sign in to manage your store</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus placeholder="vendor@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-vendor w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
        </button>
    </form>

    <div class="text-center mt-3">
        <p class="text-muted small mb-2">Don't have an account?</p>
        <a href="{{ route('vendor.register') }}" class="text-decoration-none text-success fw-semibold">
            <i class="bi bi-shop me-1"></i>Register Your Store
        </a>
    </div>

    <div class="text-center mt-3 pt-3 border-top">
        <p class="text-muted small mb-2">Forgot your password?</p>
        <a href="{{ route('vendor.password.forgot') }}" class="text-decoration-none text-success small">
            <i class="bi bi-key me-1"></i>Reset Password
        </a>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('home') }}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Back to website
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
