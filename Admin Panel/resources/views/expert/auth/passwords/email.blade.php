<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg,#1a3c34,#2e7d32); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { border-radius:1rem; box-shadow:0 20px 60px rgba(0,0,0,.3); max-width:430px; width:100%; padding:2.5rem; }
        .btn-expert { background:#1b5e20; color:#fff; border:none; }
        .btn-expert:hover { background:#134418; color:#fff; }
    </style>
</head>
<body>
<div class="card bg-white">
    <div class="text-center mb-4">
        <i class="bi bi-person-badge fs-1 text-success"></i>
        <h5 class="fw-bold mt-2">Forgot Password</h5>
        <p class="text-muted small">Enter your expert email to receive a reset link</p>
    </div>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('expert.password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn btn-expert w-100 py-2 fw-semibold">
            <i class="bi bi-send me-1"></i>Send Reset Link
        </button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('expert.login') }}" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>Back to login</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
