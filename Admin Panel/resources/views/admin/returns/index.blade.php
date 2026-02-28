@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Returns & Refunds</h5>
                    <a href="{{ route('admin.returns.reasons') }}" class="btn btn-sm btn-outline-secondary">
                        Manage Return Reasons
                    </a>
                </div>
                <div class="card-body p-0">
                    {{-- Filter --}}
                    <form method="GET" class="p-3 border-bottom bg-light">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    @foreach(['pending','approved','rejected','refunded'] as $s)
                                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary">Filter</button>
                                <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-light ms-1">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Reason</th>
                                    <th>Refund Amt</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                <tr>
                                    <td>#{{ $return->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $return->order_id) }}">
                                            Order #{{ $return->order_id }}
                                        </a>
                                    </td>
                                    <td>{{ $return->user->name ?? 'N/A' }}</td>
                                    <td>{{ $return->reason->reason ?? $return->reason_text ?? '—' }}</td>
                                    <td>
                                        @if($return->refund)
                                            {{ config('plantix.currency_symbol') }}{{ number_format($return->refund->amount, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $bc = ['pending'=>'warning','approved'=>'info','rejected'=>'danger','refunded'=>'success'];
                                        @endphp
                                        <span class="badge bg-{{ $bc[$return->status] ?? 'secondary' }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $return->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.returns.show', $return->id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No return requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($returns->hasPages())
                <div class="card-footer">{{ $returns->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
