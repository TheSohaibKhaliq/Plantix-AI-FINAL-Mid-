@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-comments text-success me-2"></i> Forum Moderation</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Forum</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body text-center py-4">
                        <div class="mb-2"><i class="fa fa-list fa-2x text-primary"></i></div>
                        <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Total Threads</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body text-center py-4">
                        <div class="mb-2"><i class="fa fa-check-circle fa-2x text-success"></i></div>
                        <h3 class="fw-bold mb-0">{{ $stats['open'] }}</h3>
                        <small class="text-muted">Open</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body text-center py-4">
                        <div class="mb-2"><i class="fa fa-lock fa-2x text-secondary"></i></div>
                        <h3 class="fw-bold mb-0">{{ $stats['closed'] }}</h3>
                        <small class="text-muted">Closed</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm" style="border-radius:16px; border-left:4px solid #f44 !important;">
                    <div class="card-body text-center py-4">
                        <div class="mb-2"><i class="fa fa-flag fa-2x text-danger"></i></div>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['flagged'] }}</h3>
                        <small class="text-muted">Flagged</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="mb-4 d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.forum.threads') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-list me-1"></i> All Threads
            </a>
            <a href="{{ route('admin.forum.threads') }}?status=flagged" class="btn btn-outline-danger btn-sm">
                <i class="fa fa-flag me-1"></i> Flagged Only
            </a>
            <a href="{{ route('admin.forum.threads') }}?status=pending" class="btn btn-outline-warning btn-sm">
                <i class="fa fa-clock-o me-1"></i> Pending Approval
            </a>
            <a href="{{ route('admin.forum.categories') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-tags me-1"></i> Manage Categories
            </a>
        </div>

        {{-- Flagged Threads --}}
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-danger"><i class="fa fa-flag me-2"></i>Flagged Threads Requiring Attention</h5>
                <span class="badge bg-danger">{{ $stats['flagged'] }}</span>
            </div>
            <div class="card-body p-0">
                @if($flagged->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-check-circle fa-3x text-success mb-3 d-block"></i>
                        No flagged threads. Forum is clean!
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Thread</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flagged as $thread)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.forum.thread', $thread->id) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ Str::limit($thread->title, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ optional($thread->user)->name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ optional($thread->category)->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td>{{ $thread->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.forum.thread', $thread->id) }}" class="btn btn-xs btn-outline-primary me-1">
                                            <i class="fa fa-eye"></i> Review
                                        </a>
                                        <form method="POST" action="{{ route('admin.forum.threads.moderate', $thread->id) }}" class="d-inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="btn btn-xs btn-outline-success me-1">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.forum.threads.destroy', $thread->id) }}" class="d-inline"
                                              onsubmit="return confirm('Delete this thread?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
