@extends('expert.layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="mb-0">
            @if($unreadCount > 0)
                <span class="badge bg-danger me-2">{{ $unreadCount }} unread</span>
            @endif
        </h6>
    </div>
    @if($unreadCount > 0)
    <form method="POST" action="{{ route('expert.notifications.read-all') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-success">
            <i class="bi bi-check-all me-1"></i>Mark All Read
        </button>
    </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($notifications->items() as $notif)
        <div class="list-group-item border-bottom px-4 py-3 {{ !$notif->is_read ? 'bg-light' : '' }}">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:{{ !$notif->is_read ? '#e8f5e9' : '#f5f5f5' }}">
                    @php
                        $icon = match(true) {
                            str_starts_with($notif->type, 'appointment') => 'bi-calendar-check',
                            str_starts_with($notif->type, 'forum')       => 'bi-chat-dots',
                            str_starts_with($notif->type, 'admin')       => 'bi-megaphone',
                            default                                       => 'bi-bell',
                        };
                    @endphp
                    <i class="bi {{ $icon }} {{ !$notif->is_read ? 'text-success' : 'text-muted' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="fw-semibold small {{ !$notif->is_read ? '' : 'text-muted' }}">
                            {{ $notif->title }}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if(!$notif->is_read)
                                <form method="POST" action="{{ route('expert.notifications.read', $notif) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-success p-0"
                                            title="Mark as read">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            @endif
                            <span class="text-muted" style="font-size:.7rem">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    @if($notif->body)
                        <p class="text-muted small mb-0 mt-1">{{ $notif->body }}</p>
                    @endif
                    <span class="badge bg-light text-secondary mt-1" style="font-size:.65rem">
                        {{ $notif->type }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="p-5 text-center text-muted">
            <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
            No notifications yet
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer bg-white">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
