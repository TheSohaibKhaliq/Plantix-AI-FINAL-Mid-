@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-history text-success me-2"></i> Broadcast History</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('notification') }}">Notifications</a></li>
                <li class="breadcrumb-item active">Broadcast History</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.notifications.broadcast') }}" class="btn btn-success rounded-pill px-4">
                <i class="fa fa-bullhorn me-2"></i> New Broadcast
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Past Broadcasts</h5>
                <span class="badge bg-secondary">Last 50 grouped by day</span>
            </div>
            <div class="card-body p-0">
                @if($history->isEmpty())
                    <div class="py-5 text-center text-muted">
                        <i class="fa fa-bullhorn fa-3x mb-3 d-block text-muted opacity-50"></i>
                        No broadcasts sent yet.
                        <div class="mt-3">
                            <a href="{{ route('admin.notifications.broadcast') }}" class="btn btn-outline-success btn-sm rounded-pill">
                                Send your first broadcast
                            </a>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Title (first in day)</th>
                                    <th class="text-center">Recipients</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $row)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $row['date'] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $row['title'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3">
                                            {{ number_format($row['count']) }} users
                                        </span>
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
