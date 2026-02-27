@extends('expert.layouts.app')
@section('title', 'Forum Discussions')
@section('page-title', 'Farmer Discussion Forum')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search threads..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <a href="{{ route('expert.forum.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2 text-success"></i>Farmer Discussions</h6>
    </div>
    <div class="list-group list-group-flush">
        @forelse($threads->items() as $thread)
        <a href="{{ route('expert.forum.show', $thread) }}"
           class="list-group-item list-group-item-action border-bottom px-4 py-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:38px;height:38px;font-size:.85rem;font-weight:700">
                    {{ strtoupper(substr($thread->user->name ?? 'F', 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1 fw-semibold">{{ $thread->title }}</h6>
                        <span class="badge bg-light text-dark ms-2 flex-shrink-0" style="font-size:.7rem">
                            <i class="bi bi-chat me-1"></i>{{ $thread->replies_count }}
                        </span>
                    </div>
                    <div class="text-muted small mb-1">
                        by <strong>{{ $thread->user->name }}</strong>
                        · {{ $thread->category?->name }}
                        · {{ $thread->created_at->diffForHumans() }}
                    </div>
                    <p class="text-muted small mb-0">{{ Str::limit($thread->body, 120) }}</p>
                </div>
            </div>
        </a>
        @empty
        <div class="p-5 text-center text-muted">
            <i class="bi bi-chat-square fs-2 d-block mb-2"></i>No discussions found
        </div>
        @endforelse
    </div>
    @if($threads->hasPages())
    <div class="card-footer bg-white">
        {{ $threads->appends($filters)->links() }}
    </div>
    @endif
</div>
@endsection
