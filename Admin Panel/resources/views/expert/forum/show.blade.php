@extends('expert.layouts.app')
@section('title', Str::limit($thread->title, 40))
@section('page-title', 'Forum Thread')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        {{-- Thread --}}
        <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 border border-success border-opacity-25 fs-6">
                        <i class="bi bi-tag me-2"></i>{{ $thread->category?->name }}
                    </span>
                    <div class="text-muted small text-uppercase fw-bold">
                        <i class="bi bi-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-4" style="line-height:1.4;">{{ $thread->title }}</h3>
                
                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:45px; height:45px; font-weight:bold; font-size:1.2rem;">
                        {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted fw-bold mb-1">Posted By</div>
                        <div class="fw-semibold text-dark fs-6">{{ $thread->user->name }}</div>
                    </div>
                    <div class="ms-auto text-end">
                        <div class="badge rounded-pill bg-white text-dark border px-3 py-2 fs-6">
                            <i class="bi bi-eye text-success me-2"></i>{{ $thread->views }} views
                        </div>
                    </div>
                </div>
                
                <p class="mb-0 fs-5 text-secondary" style="line-height:1.8;">{!! nl2br(e($thread->body)) !!}</p>
            </div>
        </div>

        {{-- Replies --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px; overflow:hidden;">
            <div class="card-header bg-white border-bottom py-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-chat-text text-success me-2 fs-4"></i>
                    Discussion Replies
                </h5>
                <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fs-6">{{ $thread->replies->count() }} Responses</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($thread->replies->sortBy('created_at') as $reply)
                <div class="list-group-item border-bottom px-4 py-4 hover-bg-light
                    {{ $reply->is_expert_reply ? 'bg-success bg-opacity-10' : '' }}">
                    
                    @if($reply->is_expert_reply)
                        <div class="mb-3 d-inline-block">
                            <span class="badge rounded-pill bg-success text-white px-3 py-2 fs-6 shadow-sm">
                                <i class="bi bi-shield-check me-2"></i>Verified Expert Reply
                            </span>
                        </div>
                    @endif

                    <div class="d-flex gap-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm
                            {{ $reply->is_expert_reply ? 'bg-success text-white border border-2 border-white' : 'bg-white text-dark border' }}"
                             style="width:50px;height:50px;font-size:1.2rem;font-weight:bold;">
                            {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark fs-6">{{ $reply->user->name }}</span>
                                <span class="text-muted small text-uppercase fw-bold"><i class="bi bi-clock me-1"></i>{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <p class="mb-0 text-secondary" style="line-height:1.6; font-size:1.05rem;">{!! nl2br(e($reply->body)) !!}</p>
                            
                            @if($reply->expertResponse?->recommendation)
                                <div class="mt-4 p-4 rounded-4 shadow-sm bg-white" style="border: 1px solid #c8e6c9; border-left: 4px solid #2e7d32;">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                        <i class="bi bi-lightbulb-fill fs-5"></i>
                                        <span class="fw-bold text-uppercase small">Actionable Recommendation</span>
                                    </div>
                                    <p class="mb-0 text-dark fw-medium" style="line-height:1.6;">{{ $reply->expertResponse->recommendation }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-5 text-center text-muted">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width:80px; height:80px;">
                        <i class="bi bi-chat-left-dots fs-2 text-success opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No replies yet</h5>
                    <p>Be the first expert to respond and help this farmer out!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Reply Form --}}
        @if(!$thread->is_locked)
        <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
            <div class="card-header bg-success text-white py-3 border-bottom-0" style="border-top-left-radius:16px; border-top-right-radius:16px;">
                <h6 class="mb-0 fw-bold fs-5">
                    <i class="bi bi-shield-check me-2"></i>Post Expert Response
                </h6>
            </div>
            <div class="card-body p-4 bg-light rounded-bottom-4">
                <form method="POST" action="{{ route('expert.forum.reply', $thread) }}">
                    @csrf
                    <div class="mb-4 bg-white p-4 rounded-4 border shadow-sm">
                        <label class="form-label text-uppercase fw-bold text-dark mb-2">Your Answer <span class="text-danger">*</span></label>
                        <textarea name="body" rows="6" class="form-control border-0 bg-light p-3 fs-6 rounded-3 @error('body') is-invalid @enderror"
                                  placeholder="Provide your detailed expert answer here (minimum 20 characters)..."
                                  required minlength="20">{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4 bg-white p-4 rounded-4 border shadow-sm">
                        <label class="form-label text-uppercase fw-bold text-dark mb-2">Structured Recommendation <span class="text-muted text-lowercase fw-normal ms-2">(optional)</span></label>
                        <textarea name="recommendation" rows="3" class="form-control border-0 bg-light p-3 fs-6 rounded-3"
                                  placeholder="Add a formal recommendation or actionable step-by-step advice...">{{ old('recommendation') }}</textarea>
                        <div class="form-text mt-2 text-success fw-medium">
                            <i class="bi bi-stars me-1"></i>
                            This will appear highlighted in a special green recommendation block at the bottom of your post.
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-pill border shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-patch-check-fill text-success fs-4 me-3 ms-2"></i>
                            <span class="text-muted fw-bold text-uppercase small">Posted as Verified Expert</span>
                        </div>
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold fs-6">
                            Publish Response <i class="bi bi-send ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-secondary border-0 text-center p-4 rounded-4 mt-2">
            <i class="bi bi-lock-fill d-block fs-1 text-muted mb-2 opacity-50"></i>
            <h5 class="fw-bold text-dark">Thread Locked</h5>
            <p class="mb-0 text-muted">This discussion is no longer accepting replies.</p>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle-fill me-2 text-info fs-5"></i>Thread Info</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted text-uppercase fw-bold small"><i class="bi bi-hash me-1"></i>Category</span>
                        <span class="fw-bold text-dark">{{ $thread->category?->name ?? 'General' }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted text-uppercase fw-bold small"><i class="bi bi-person me-1"></i>Author</span>
                        <span class="fw-bold text-dark">{{ $thread->user->name }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted text-uppercase fw-bold small"><i class="bi bi-calendar3 me-1"></i>Date</span>
                        <span class="fw-bold text-dark">{{ $thread->created_at->format('d M Y') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted text-uppercase fw-bold small"><i class="bi bi-eye me-1"></i>Views</span>
                        <span class="badge rounded-pill bg-light text-dark border px-3">{{ $thread->views }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-muted text-uppercase fw-bold small"><i class="bi bi-chat-text me-1"></i>Replies</span>
                        <span class="badge rounded-pill bg-success text-white px-3">{{ $thread->replies->count() }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <a href="{{ route('expert.forum.index') }}" class="btn btn-outline-secondary rounded-pill w-100 py-3 fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Return to Forum List
        </a>
    </div>
</div>
@endsection
