@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-bullhorn text-success me-2"></i> Broadcast Notification</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('notification') }}">Notifications</a></li>
                <li class="breadcrumb-item active">Broadcast</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-bullhorn me-2 text-success"></i>Compose Broadcast</h5>
                        <a href="{{ route('admin.notifications.broadcast.history') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fa fa-history me-1"></i> History
                        </a>
                    </div>
                    <div class="card-body p-4">

                        <form method="POST" action="{{ route('admin.notifications.broadcast.send') }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" maxlength="120"
                                       class="form-control border-0 bg-light rounded-pill form-control-lg @error('title') is-invalid @enderror"
                                       placeholder="Notification title…" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="text-muted small mt-1">120 characters max.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                                <textarea name="body" rows="5" maxlength="500"
                                          class="form-control border-0 bg-light @error('body') is-invalid @enderror"
                                          style="border-radius:12px;" placeholder="Write your broadcast message…" required>{{ old('body') }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="text-muted small mt-1">500 characters max.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Action URL <span class="text-muted small">(optional)</span></label>
                                <input type="url" name="action_url"
                                       class="form-control border-0 bg-light rounded-pill @error('action_url') is-invalid @enderror"
                                       placeholder="https://example.com/page" value="{{ old('action_url') }}">
                                @error('action_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="text-muted small mt-1">Link users are taken to when they tap the notification.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Send To <span class="text-danger">*</span></label>
                                <select name="target"
                                        class="form-select border-0 bg-light rounded-pill form-select-lg @error('target') is-invalid @enderror" required>
                                    @foreach($targets as $value => $label)
                                        <option value="{{ $value }}" {{ old('target') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                           id="send_email" {{ old('send_email') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="send_email">
                                        Also send via Email
                                    </label>
                                </div>
                                <div class="text-muted small mt-1">Sends an email copy in addition to the in-app notification.</div>
                            </div>

                            <div class="alert alert-warning border-0 rounded-3 mb-4">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>Heads up!</strong> This will send a notification to <em>every active user</em>
                                in the selected group. Broadcasts are processed in background jobs.
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5"
                                        onclick="return confirm('Send broadcast to all selected users?')">
                                    <i class="fa fa-paper-plane me-2"></i> Send Broadcast
                                </button>
                                <a href="{{ url('notification') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                                    Cancel
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
