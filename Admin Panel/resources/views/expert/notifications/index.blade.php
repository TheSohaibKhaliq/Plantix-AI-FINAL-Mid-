@extends('expert.layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-bell me-2 text-success fs-4"></i>Notification Center
            @if($unreadCount > 0)
                <span class="badge rounded-pill bg-danger ms-2 px-3 py-2 fs-6 shadow-sm">{{ $unreadCount }} New</span>
            @endif
        </h5>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('expert.notifications.read-all') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold shadow-sm">
                <i class="bi bi-check2-all me-1"></i>Mark All as Read
            </button>
        </form>
        @endif
    </div>
    <div class="list-group list-group-flush">
        @forelse($notifications->items() as $notif)
        <div class="list-group-item border-bottom px-4 py-4 {{ !$notif->is_read ? 'bg-success bg-opacity-10' : 'hover-bg-light' }}">
            <div class="d-flex align-items-start gap-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm
                            {{ !$notif->is_read ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                     style="width:50px;height:50px; font-size:1.3rem;">
                    @php
                        $icon = match(true) {
                            str_starts_with($notif->type, 'appointment') => 'bi-calendar-check',
                            str_starts_with($notif->type, 'forum')       => 'bi-chat-dots',
                            str_starts_with($notif->type, 'admin')       => 'bi-megaphone',
                            default                                       => 'bi-bell',
                        };
                    @endphp
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="fw-bold fs-6 {{ !$notif->is_read ? 'text-dark' : 'text-secondary' }}">
                            {{ $notif->title }}
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted text-uppercase fw-bold small">
                                <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                            </span>
                            @if(!$notif->is_read)
                                <form method="POST" action="{{ route('expert.notifications.read', $notif) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-circle shadow-sm"
                                            style="width:28px; height:28px; padding:0;" title="Mark as read">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if($notif->body)
                        <p class="text-muted mb-2 mt-2 {{ !$notif->is_read ? 'fw-medium text-dark' : '' }}" style="line-height:1.5;">{{ $notif->body }}</p>
                    @endif
                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-1 fs-6 mt-1">
                        <i class="bi bi-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $notif->type)) }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="p-5 text-center my-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width:100px; height:100px;">
                <i class="bi bi-bell-slash fs-1 text-success opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark">No Notifications</h4>
            <p class="text-muted">You're all caught up! There are no alerts right now.</p>
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer bg-white p-4" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px;">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
