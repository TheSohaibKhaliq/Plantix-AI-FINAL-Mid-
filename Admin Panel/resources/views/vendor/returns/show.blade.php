@extends('vendor.layouts.app')
@section('title', 'Return #{{ $return->id }}')
@section('page-title', 'Return Request Detail')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-success"></i>Return Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Return Ref</dt>
                    <dd class="col-sm-8">#{{ $return->id }}</dd>

                    <dt class="col-sm-4 text-muted">Order #</dt>
                    <dd class="col-sm-8">{{ $return->order->order_number ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Customer</dt>
                    <dd class="col-sm-8">{{ $return->user->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Reason</dt>
                    <dd class="col-sm-8">{{ $return->reason->reason ?? $return->description ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Description</dt>
                    <dd class="col-sm-8">{{ $return->description ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8">
                        @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','refunded'=>'info'][$return->status] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}">{{ ucfirst($return->status) }}</span>
                    </dd>

                    @if($return->admin_notes)
                    <dt class="col-sm-4 text-muted">Admin Notes</dt>
                    <dd class="col-sm-8">{{ $return->admin_notes }}</dd>
                    @endif

                    @if($return->vendor_notes)
                    <dt class="col-sm-4 text-muted">Your Notes</dt>
                    <dd class="col-sm-8">{{ $return->vendor_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Add Vendor Note (only when pending) --}}
        @if($return->status === 'pending')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">Add a Note for Admin</h6>
                <p class="text-muted small mb-0">You can provide context about this return. Admin will make the final decision.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.returns.note', $return->id) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="3" placeholder="e.g. Customer received correct item, please review photos..."
                                  required>{{ old('notes', $return->vendor_notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-save me-1"></i>Save Note
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Order Items --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">Order Items</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($return->order->items ?? [] as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <p class="mb-0 fw-semibold small">{{ $item->product->name ?? 'N/A' }}</p>
                            <p class="mb-0 text-muted" style="font-size:.75rem;">Qty: {{ $item->quantity }}</p>
                        </div>
                        <span class="text-muted small">{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted text-center">No items.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('vendor.returns.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Returns
    </a>
</div>
@endsection
