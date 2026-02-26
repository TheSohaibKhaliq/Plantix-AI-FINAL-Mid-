@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9 offset-lg-1">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Return Request #{{ $return->id }}</h5>
                    @php $bc = ['pending'=>'warning','approved'=>'info','rejected'=>'danger','refunded'=>'success']; @endphp
                    <span class="badge bg-{{ $bc[$return->status] ?? 'secondary' }} fs-6">
                        {{ ucfirst($return->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer</h6>
                            <p class="mb-1 fw-semibold">{{ $return->user->name ?? 'N/A' }}</p>
                            <p class="mb-1 small">{{ $return->user->email ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order</h6>
                            <p class="mb-1">
                                <a href="{{ route('admin.orders.show', $return->order_id) }}">
                                    Order #{{ $return->order_id }}
                                </a>
                            </p>
                            <p class="mb-0 small text-muted">
                                Placed: {{ $return->order->created_at->format('d M Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Return Reason</h6>
                            <p class="mb-0">{{ $return->reason->reason ?? $return->reason_text ?? 'N/A' }}</p>
                        </div>
                        @if($return->customer_notes)
                        <div class="col-12">
                            <h6 class="text-muted">Customer Notes</h6>
                            <p class="mb-0">{{ $return->customer_notes }}</p>
                        </div>
                        @endif
                        @if($return->refund)
                        <div class="col-md-6">
                            <h6 class="text-muted">Refund</h6>
                            <p class="mb-1">
                                <strong>Amount:</strong>
                                {{ config('plantix.currency_symbol') }}{{ number_format($return->refund->amount, 2) }}
                            </p>
                            <p class="mb-0 small">
                                <strong>Method:</strong> {{ ucfirst($return->refund->method ?? 'wallet') }}
                                <br>
                                <strong>Processed:</strong> {{ $return->refund->processed_at?->format('d M Y') ?? 'Pending' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if($return->status === 'pending')
            <div class="card mb-3">
                <div class="card-header"><h6 class="mb-0">Approve / Reject</h6></div>
                <div class="card-body d-flex gap-3">
                    <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST">
                        @csrf
                        <textarea name="admin_note" class="form-control mb-2" rows="2"
                                  placeholder="Optional note to customer…"></textarea>
                        <button class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Approve Return
                        </button>
                    </form>
                    <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST">
                        @csrf
                        <textarea name="admin_note" class="form-control mb-2" rows="2"
                                  placeholder="Reason for rejection…" required></textarea>
                        <button class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i>Reject Return
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($return->status === 'approved' && !$return->refund)
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Process Refund</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.returns.refund', $return->id) }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Refund Amount ({{ config('plantix.currency_symbol') }})</label>
                                <input type="number" name="amount" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ $return->order->grand_total }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Method</label>
                                <select name="method" class="form-select">
                                    <option value="wallet">Wallet Credit</option>
                                    <option value="original_payment">Original Payment Method</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary">
                                    <i class="bi bi-wallet2 me-1"></i>Process Refund
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Returns
        </a>
    </div>
</div>
@endsection
