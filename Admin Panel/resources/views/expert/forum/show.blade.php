@extends('expert.layouts.app')
@section('title', Str::limit($thread->title, 40))
@section('page-title', 'Forum Thread')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Thread --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-1">{{ $thread->title }}</h5>
                <div class="text-muted small mb-3">
                    by <strong>{{ $thread->user->name }}</strong>
                    · {{ $thread->category?->name }}
                    · {{ $thread->created_at->diffForHumans() }}
                    · <i class="bi bi-eye me-1"></i>{{ $thread->views }} views
                </div>
                <p class="mb-0">{{ $thread->body }}</p>
            </div>
        </div>

        {{-- Replies --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-chat me-2 text-success"></i>
                    Replies ({{ $thread->replies->count() }})
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($thread->replies->sortBy('created_at') as $reply)
                <div class="list-group-item border-bottom px-4 py-3
                    {{ $reply->is_expert_reply ? 'border-start border-success border-3' : '' }}">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                            {{ $reply->is_expert_reply ? 'bg-success text-white' : 'bg-light text-muted' }}"
                             style="width:36px;height:36px;font-size:.8rem;font-weight:700">
                            {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-semibold small">{{ $reply->user->name }}</span>
                                @if($reply->is_expert_reply)
                                    <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-size:.65rem;border:1px solid #c8e6c9">
                                        <i class="bi bi-shield-check me-1"></i>Expert Advice
                                    </span>
                                @endif
                                <span class="text-muted" style="font-size:.7rem">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mb-0 small">{{ $reply->body }}</p>
                            @if($reply->expertResponse?->recommendation)
                                <div class="mt-2 p-2 rounded" style="background:#f1f8e9;border-left:3px solid #66bb6a">
                                    <div class="small text-success fw-semibold mb-1">
                                        <i class="bi bi-lightbulb me-1"></i>Expert Recommendation
                                    </div>
                                    <p class="mb-0 small">{{ $reply->expertResponse->recommendation }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted small">
                    No replies yet. Be the first expert to respond!
                </div>
                @endforelse
            </div>
        </div>

        {{-- Reply Form --}}
        @if(!$thread->is_locked)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-shield-check me-2 text-success"></i>Post Expert Response
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('expert.forum.reply', $thread) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your Answer <span class="text-danger">*</span></label>
                        <textarea name="body" rows="5" class="form-control @error('body') is-invalid @enderror"
                                  placeholder="Provide your expert answer here (minimum 20 characters)..."
                                  required minlength="20">{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Structured Recommendation <span class="text-muted small">(optional)</span></label>
                        <textarea name="recommendation" rows="3" class="form-control"
                                  placeholder="Add a formal recommendation or actionable advice...">{{ old('recommendation') }}</textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1 text-success"></i>
                            This will appear highlighted as an expert recommendation block.
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-shield-check me-1"></i>Post as Expert Response
                        </button>
                        <span class="text-muted small">Your reply will be marked with an Expert badge</span>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-secondary">
            <i class="bi bi-lock me-2"></i>This thread is locked and no longer accepting replies.
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2 text-info"></i>Thread Info</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><strong>Category:</strong> {{ $thread->category?->name ?? 'General' }}</li>
                    <li class="mb-1"><strong>Posted by:</strong> {{ $thread->user->name }}</li>
                    <li class="mb-1"><strong>Date:</strong> {{ $thread->created_at->format('d M Y') }}</li>
                    <li class="mb-1"><strong>Views:</strong> {{ $thread->views }}</li>
                    <li><strong>Replies:</strong> {{ $thread->replies->count() }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('expert.forum.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left me-1"></i>Back to Forum
        </a>
    </div>
</div>
@endsection
