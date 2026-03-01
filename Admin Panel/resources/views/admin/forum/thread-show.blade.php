@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-6 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-comments text-success me-2"></i> Thread Review</h3>
        </div>
        <div class="col-md-6 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.forum.threads') }}">Threads</a></li>
                <li class="breadcrumb-item active">Review</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row g-4">

            {{-- Thread Details --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $thread->title }}</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                @php
                                    $colors = ['open'=>'success','closed'=>'secondary','flagged'=>'danger','pending'=>'warning'];
                                    $c = $colors[$thread->status] ?? 'light';
                                @endphp
                                <span class="badge bg-{{ $c }}">{{ ucfirst($thread->status) }}</span>
                                @if($thread->is_pinned)
                                    <span class="badge bg-warning text-dark"><i class="fa fa-thumbtack me-1"></i>Pinned</span>
                                @endif
                                <span class="badge bg-light text-dark border">{{ optional($thread->category)->name ?? 'Uncategorised' }}</span>
                            </div>
                        </div>
                        <small class="text-muted">{{ $thread->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3 mb-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;font-size:16px;flex-shrink:0;">
                                {{ strtoupper(substr(optional($thread->user)->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ optional($thread->user)->name ?? 'Unknown' }}</div>
                                <div class="text-muted small">{{ optional($thread->user)->email }}</div>
                            </div>
                        </div>
                        <p class="mb-0" style="white-space:pre-wrap;">{{ $thread->body }}</p>
                    </div>
                </div>

                {{-- Replies --}}
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0">Replies ({{ $thread->replies->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($thread->replies as $reply)
                        <div class="p-4 border-bottom" id="reply-{{ $reply->id }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="rounded-circle {{ $reply->is_expert_answer ? 'bg-warning' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center"
                                         style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                                        {{ strtoupper(substr(optional($reply->user)->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ optional($reply->user)->name ?? 'Unknown' }}</span>
                                        @if($reply->is_expert_answer)
                                            <span class="badge bg-warning text-dark ms-1 small"><i class="fa fa-star me-1"></i>Expert</span>
                                        @endif
                                        @if(!$reply->is_approved)
                                            <span class="badge bg-danger ms-1 small">Pending</span>
                                        @endif
                                        <div class="text-muted small">{{ $reply->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(!$reply->is_approved)
                                    <form method="POST" action="{{ route('admin.forum.replies.approve', $reply->id) }}">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-xs btn-outline-success">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.forum.replies.destroy', $reply->id) }}"
                                          onsubmit="return confirm('Delete this reply?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="mb-0 ms-5 ps-3" style="white-space:pre-wrap;">{{ $reply->body }}</p>
                        </div>
                        @empty
                        <div class="py-4 text-center text-muted">No replies yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Moderation Actions Panel --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0">Moderation Actions</h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">

                        {{-- Change Status --}}
                        <form method="POST" action="{{ route('admin.forum.threads.moderate', $thread->id) }}">
                            @csrf @method('PUT')
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Change Status</label>
                                <select name="status" class="form-select form-select-sm rounded-pill border-0 bg-light">
                                    @foreach(['open','closed','flagged','pending'] as $s)
                                        <option value="{{ $s }}" {{ $thread->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Admin Note (optional)</label>
                                <textarea name="admin_note" class="form-control form-control-sm border-0 bg-light" rows="2"
                                          placeholder="Internal note…">{{ $thread->admin_note }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill">
                                <i class="fa fa-check me-1"></i> Update Status
                            </button>
                        </form>

                        <hr>

                        {{-- Pin / Unpin --}}
                        <form method="POST" action="{{ route('admin.forum.threads.pin', $thread->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill text-dark">
                                <i class="fa fa-thumbtack me-1"></i>
                                {{ $thread->is_pinned ? 'Unpin Thread' : 'Pin Thread' }}
                            </button>
                        </form>

                        <hr>

                        {{-- Delete --}}
                        <form method="POST" action="{{ route('admin.forum.threads.destroy', $thread->id) }}"
                              onsubmit="return confirm('Permanently delete this thread and all its replies?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">
                                <i class="fa fa-trash me-1"></i> Delete Thread
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Thread Meta --}}
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0">Thread Info</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr><td class="text-muted">ID</td><td class="fw-semibold">#{{ $thread->id }}</td></tr>
                            <tr><td class="text-muted">Author</td><td>{{ optional($thread->user)->name }}</td></tr>
                            <tr><td class="text-muted">Email</td><td>{{ optional($thread->user)->email }}</td></tr>
                            <tr><td class="text-muted">Category</td><td>{{ optional($thread->category)->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Replies</td><td>{{ $thread->replies->count() }}</td></tr>
                            <tr><td class="text-muted">Created</td><td>{{ $thread->created_at->format('d M Y') }}</td></tr>
                            <tr><td class="text-muted">Updated</td><td>{{ $thread->updated_at->diffForHumans() }}</td></tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
