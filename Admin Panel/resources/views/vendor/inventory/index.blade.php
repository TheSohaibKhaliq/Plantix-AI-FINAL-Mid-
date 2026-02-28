@extends('vendor.layouts.app')
@section('title', 'Inventory')
@section('page-title', 'Inventory Management')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ $summary['total_products'] }}</div>
            <div class="small text-muted">Total Products</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ $summary['low_stock'] }}</div>
            <div class="small text-muted">Low Stock</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-danger">{{ $summary['out_of_stock'] }}</div>
            <div class="small text-muted">Out of Stock</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search product..."
               value="{{ request('search') }}">
    </div>
    <div class="col-auto">
        <select name="stock_status" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="in_stock" @selected(request('stock_status')==='in_stock')>In Stock</option>
            <option value="low"      @selected(request('stock_status')==='low')>Low Stock</option>
            <option value="out"      @selected(request('stock_status')==='out')>Out of Stock</option>
        </select>
    </div>
    <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-outline-success btn-sm">Filter</button>
        @if(request()->hasAny(['search','stock_status']))
            <a href="{{ route('vendor.inventory.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        @endif
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Current Qty</th>
                        <th class="text-center">Low Stock Threshold</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Update Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>
                            <p class="mb-0 fw-semibold small">{{ $stock->product->name ?? '—' }}</p>
                            <p class="mb-0 text-muted" style="font-size:.75rem;">SKU: {{ $stock->sku ?? '—' }}</p>
                        </td>
                        <td class="text-center fw-bold {{ $stock->quantity <= 0 ? 'text-danger' : ($stock->isLow() ? 'text-warning' : 'text-success') }}">
                            {{ $stock->quantity }}
                        </td>
                        <td class="text-center text-muted small">{{ $stock->low_stock_threshold ?? '—' }}</td>
                        <td class="text-center">
                            @if($stock->quantity <= 0)
                                <span class="badge bg-danger-subtle text-danger">Out of Stock</span>
                            @elseif($stock->isLow())
                                <span class="badge bg-warning-subtle text-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success-subtle text-success">In Stock</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('vendor.inventory.update', $stock->product_id) }}"
                                  class="d-inline-flex gap-2">
                                @csrf
                                <input type="number" name="quantity" value="{{ $stock->quantity }}"
                                       class="form-control form-control-sm" style="width:80px;" min="0">
                                <input type="number" name="low_stock_threshold" value="{{ $stock->low_stock_threshold }}"
                                       class="form-control form-control-sm" style="width:80px;" min="0"
                                       placeholder="Threshold">
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-save"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No inventory records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stocks->hasPages())
    <div class="card-footer bg-white border-0">{{ $stocks->links() }}</div>
    @endif
</div>
@endsection
