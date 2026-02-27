<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Expert Panel') — {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-bg:     #0d2b1f;
            --sidebar-text:   #a5d6a7;
            --sidebar-active: #388e3c;
            --sidebar-width:  260px;
            --accent:         #2e7d32;
        }
        body { background: #f1f5f0; font-family: 'Nunito', sans-serif; }

        /* ── Sidebar ──────────────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100%;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            overflow-y: auto; z-index: 1040;
        }
        .sidebar .brand {
            padding: 1.5rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar .brand a { color: #fff; font-size: 1.15rem; font-weight: 700; text-decoration: none; }
        .sidebar .brand small { display: block; color: var(--sidebar-text); font-size: .72rem; margin-top: .2rem; }
        .sidebar .nav-link {
            color: var(--sidebar-text); padding: .6rem 1.2rem;
            border-radius: .35rem; margin: 2px 8px;
            transition: background .15s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--sidebar-active); color: #fff;
        }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .nav-section {
            color: rgba(255,255,255,.4); font-size: .68rem;
            font-weight: 700; letter-spacing: .1em;
            padding: 1rem 1.2rem .3rem; text-transform: uppercase;
        }

        /* ── Main content ─────────────────────────────────────────────── */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #dde5d8;
            padding: .75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar .page-title { font-weight: 700; margin: 0; font-size: 1rem; }
        .page-body { padding: 1.5rem; }

        /* ── Expert badge ─────────────────────────────────────────────── */
        .expert-badge {
            background: #e8f5e9; color: #2e7d32;
            border: 1px solid #c8e6c9;
            border-radius: .4rem; font-size: .7rem;
            padding: .2rem .55rem; font-weight: 600;
        }

        /* ── Notification bell ────────────────────────────────────────── */
        .notif-bell { position: relative; }
        .notif-bell .badge-count {
            position: absolute; top: -5px; right: -5px;
            background: #e53935; color: #fff;
            border-radius: 50%; width: 18px; height: 18px;
            font-size: .65rem; display: flex; align-items: center; justify-content: center;
        }

        @yield('extra-css')
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
<nav class="sidebar">
    <div class="brand">
        <a href="{{ route('expert.dashboard') }}">
            <i class="bi bi-person-badge me-2"></i>Expert Panel
        </a>
        <small>
            @if(isset($currentExpert) && $currentExpert->profile?->account_type === 'agency')
                <i class="bi bi-building me-1"></i>Agency Account
            @else
                <i class="bi bi-mortarboard me-1"></i>Agricultural Expert
            @endif
        </small>
    </div>

    <ul class="nav flex-column mt-2">

        {{-- Navigation --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('expert.dashboard') ? 'active' : '' }}"
               href="{{ route('expert.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        <div class="nav-section">Consultations</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('expert.appointments.*') ? 'active' : '' }}"
               href="{{ route('expert.appointments.index') }}">
                <i class="bi bi-calendar-check me-2"></i>Appointments
            </a>
        </li>

        <div class="nav-section">Community</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('expert.forum.*') ? 'active' : '' }}"
               href="{{ route('expert.forum.index') }}">
                <i class="bi bi-chat-left-dots me-2"></i>Forum
            </a>
        </li>

        <div class="nav-section">Account</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('expert.notifications.*') ? 'active' : '' }}"
               href="{{ route('expert.notifications.index') }}">
                <i class="bi bi-bell me-2"></i>Notifications
                @php $unread = isset($currentExpert) ? $currentExpert->notificationLogs()->where('is_read',false)->count() : 0; @endphp
                @if($unread > 0)
                    <span class="badge bg-danger ms-1" style="font-size:.65rem">{{ $unread }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('expert.profile.*') ? 'active' : '' }}"
               href="{{ route('expert.profile.show') }}">
                <i class="bi bi-person-circle me-2"></i>My Profile
            </a>
        </li>
    </ul>

    {{-- Logout --}}
    <div class="p-3" style="position: absolute; bottom: 0; width: 100%;">
        <form action="{{ route('expert.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-left me-1"></i>Logout
            </button>
        </form>
    </div>
</nav>

{{-- ── Main Content ─────────────────────────────────────────────────────── --}}
<div class="main-content">

    {{-- Topbar --}}
    <div class="topbar">
        <h5 class="page-title">@yield('page-title', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-3">
            {{-- Notification bell --}}
            <a href="{{ route('expert.notifications.index') }}" class="notif-bell text-secondary text-decoration-none">
                <i class="bi bi-bell fs-5"></i>
                @if(($unread ?? 0) > 0)
                    <span class="badge-count">{{ $unread }}</span>
                @endif
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="expert-badge"><i class="bi bi-shield-check me-1"></i>Expert</span>
                <span class="text-muted small">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth('expert')->user()->name ?? 'Expert' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
