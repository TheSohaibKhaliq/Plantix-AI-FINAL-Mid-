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
        <i class="bi bi-shield-lock fs-1 text-success"></i>
        <h5 class="fw-bold mt-2">Set New Password</h5>
    </div>
    @if($errors->any())
        <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('expert.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">New Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-expert w-100 py-2 fw-semibold">
            <i class="bi bi-check-circle me-1"></i>Reset Password
        </button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
