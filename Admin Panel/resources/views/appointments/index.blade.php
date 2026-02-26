@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Appointments</h5>
                </div>
                <div class="card-body p-0">
                    {{-- Filter --}}
                    <form method="GET" class="p-3 border-bottom bg-light">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    @foreach(['pending','confirmed','cancelled','completed'] as $s)
                                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date" class="form-control form-control-sm"
                                       value="{{ request('date') }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-light ms-1">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Customer</th>
                                    <th>Expert</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Booked On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appt)
                                <tr>
                                    <td>#{{ $appt->id }}</td>
                                    <td>{{ $appt->user->name ?? 'N/A' }}</td>
                                    <td>{{ $appt->expert->name ?? '—' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}</td>
                                    <td>{{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : '—' }}</td>
                                    <td>{{ config('plantix.currency_symbol') }}{{ number_format($appt->expert->fee ?? 0, 2) }}</td>
                                    <td>
                                        @php
                                            $bc = ['pending'=>'warning','confirmed'=>'success','cancelled'=>'danger','completed'=>'info'];
                                        @endphp
                                        <span class="badge bg-{{ $bc[$appt->status] ?? 'secondary' }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $appt->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.appointments.show', $appt->id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($appt->status === 'pending')
                                        <form action="{{ route('admin.appointments.confirm', $appt->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success"
                                                    title="Confirm">✓</button>
                                        </form>
                                        <form action="{{ route('admin.appointments.cancel', $appt->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Cancel appointment?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Cancel">✕</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No appointments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($appointments->hasPages())
                <div class="card-footer">{{ $appointments->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
