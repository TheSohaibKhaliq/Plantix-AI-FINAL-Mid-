<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Successful — Vendor Panel | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a3c34 0%, #2e7d32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 100%;
            max-width: 500px;
            padding: 3rem 2.5rem;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            color: #2e7d32;
            margin-bottom: 1rem;
            animation: bounceIn 0.6s ease-in-out;
        }
        .success-card h2 {
            font-weight: 700;
            color: #1b5e20;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .info-box {
            background: #f0f8f5;
            border-left: 4px solid #2e7d32;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .info-box strong {
            color: #1b5e20;
        }
        .info-box p {
            margin: 0.5rem 0 0 0;
            font-size: 14px;
            color: #555;
        }
        .btn-vendor {
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .btn-vendor:hover {
            background: #1b5e20;
            color: #fff;
        }
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
<div class="success-card">
    <div class="success-icon">
        <i class="bi bi-check-circle-fill"></i>
    </div>

    <h2>Registration Successful!</h2>
    <p>Your vendor account has been created successfully.</p>

    <div class="info-box">
        <strong><i class="bi bi-info-circle me-2"></i>Account Status</strong>
        <p>Your account is currently <strong>pending admin approval</strong>. You will receive an email notification once your account is activated.</p>
    </div>

    <div class="info-box">
        <strong><i class="bi bi-clock-history me-2"></i>What's Next?</strong>
        <p>Our admin team will review your registration within 24-48 hours. Once approved, you can log in and start managing your store.</p>
    </div>

    <div class="info-box">
        <strong><i class="bi bi-question-circle me-2"></i>Have Questions?</strong>
        <p>Contact our support team at <a href="mailto:support@plantixai.com" class="text-decoration-none text-success">support@plantixai.com</a></p>
    </div>

    <a href="{{ route('vendor.login') }}" class="btn btn-vendor w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i>Go to Login
    </a>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to website
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
