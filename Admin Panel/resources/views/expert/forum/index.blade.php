@extends('expert.layouts.app')
@section('title', 'Forum Discussions')
@section('page-title', 'Farmer Discussion Forum')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-5">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Search Discussions</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-md border-start-0 ps-0"
                           placeholder="Search threads..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-sm-auto ms-auto">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    Filter Results
                </button>
                <a href="{{ route('expert.forum.index') }}" class="btn btn-outline-secondary px-4 rounded-pill ms-2">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
    <div class="card-header bg-white border-bottom py-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2 text-success fs-4"></i>Farmer Discussions</h5>
    </div>
    <div class="list-group list-group-flush">
        @forelse($threads->items() as $thread)
        <a href="{{ route('expert.forum.show', $thread) }}"
           class="list-group-item list-group-item-action border-bottom px-4 py-4 hover-bg-light position-relative">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:50px;height:50px;font-size:1.2rem;font-weight:700">
                    {{ strtoupper(substr($thread->user->name ?? 'F', 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0 fw-semibold text-dark">{{ $thread->title }}</h5>
                        <span class="badge rounded-pill bg-light text-dark border ms-2 px-3 py-2 flex-shrink-0 fs-6">
                            <i class="bi bi-chat-text text-success me-2"></i>{{ $thread->replies_count }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-muted small text-uppercase fw-bold mb-3">
                        <span><i class="bi bi-person me-1"></i>{{ $thread->user->name }}</span>
                        <span><i class="bi bi-tag me-1"></i>{{ $thread->category?->name }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-secondary mb-0 fs-6">{{ Str::limit($thread->body, 150) }}</p>
                </div>
            </div>
        </a>
        @empty
        <div class="p-5 text-center my-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width:100px; height:100px;">
                <i class="bi bi-chat-square-text fs-1 text-success opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark">No discussions found</h4>
            <p class="text-muted">There are currently no active discussions matching your criteria.</p>
        </div>
        @endforelse
    </div>
    @if($threads->hasPages())
    <div class="card-footer bg-white p-4">
        {{ $threads->appends($filters)->links() }}
    </div>
    @endif
</div>
@endsection
