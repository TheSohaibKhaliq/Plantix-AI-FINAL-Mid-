<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Panel') — {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-bg: #1a3c34;
            --sidebar-text: #c8e6c9;
            --sidebar-active: #4caf50;
            --sidebar-width: 250px;
        }
        body { background: #f4f6f9; font-family: 'Nunito', sans-serif; }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100%;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            overflow-y: auto; z-index: 1040; padding-top: 0;
        }
        .sidebar .brand { padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar .brand a { color: #fff; font-size: 1.2rem; font-weight: 700; text-decoration: none; }
        .sidebar .nav-link {
            color: var(--sidebar-text); padding: .6rem 1.2rem;
            border-radius: .25rem; margin: 2px 8px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-active); color: #fff;
        }
        .sidebar .nav-link i { width: 20px; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e0e0e0;
            padding: .75rem 1.5rem; display: flex;
            align-items: center; justify-content: space-between;
        }
        .page-body { padding: 1.5rem; }
        @yield('extra-css')
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="sidebar">
    <div class="brand">
        <a href="{{ route('vendor.dashboard') }}">
            <i class="bi bi-shop me-2"></i>Vendor Panel
        </a>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}"
               href="{{ route('vendor.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}"
               href="{{ route('vendor.products.index') }}">
                <i class="bi bi-box-seam me-2"></i>Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}"
               href="{{ route('vendor.orders.index') }}">
                <i class="bi bi-cart-check me-2"></i>Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.earnings.*') ? 'active' : '' }}"
               href="{{ route('vendor.earnings.index') }}">
                <i class="bi bi-currency-dollar me-2"></i>Earnings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.payouts.*') ? 'active' : '' }}"
               href="{{ route('vendor.payouts.index') }}">
                <i class="bi bi-wallet2 me-2"></i>Payouts
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.coupons.*') ? 'active' : '' }}"
               href="{{ route('vendor.coupons.index') }}">
                <i class="bi bi-tag me-2"></i>Coupons
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.profile') ? 'active' : '' }}"
               href="{{ route('vendor.profile') }}">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
        </li>
    </ul>
    <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
        <form action="{{ route('vendor.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-left me-1"></i>Logout
            </button>
        </form>
    </div>
</nav>

{{-- Main Content --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-person-circle me-1"></i>
                {{ auth('vendor')->user()->name ?? 'Vendor' }}
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
