@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-2">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="fs-3 fw-bold text-primary">{{ $summary['total_products'] }}</div>
                        <div class="small text-muted">Total Products Tracked</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="fs-3 fw-bold text-warning">{{ $summary['low_stock'] }}</div>
                        <div class="small text-muted">Low Stock Alerts</div>
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
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search product..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="vendor_id" class="form-select form-select-sm">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" @selected(request('vendor_id') == $v->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
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
                    @if(request()->hasAny(['search','vendor_id','stock_status']))
                        <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock Tracking</h5>
                    <span class="badge bg-secondary">{{ $stocks->total() }} records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Vendor</th>
                                    <th>Category</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Threshold</th>
                                    <th>SKU</th>
                                    <th class="text-center">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-semibold small">{{ $stock->product->name ?? '—' }}</p>
                                    </td>
                                    <td class="small text-muted">{{ $stock->vendor->name ?? '—' }}</td>
                                    <td class="small text-muted">{{ $stock->product->category->name ?? '—' }}</td>
                                    <td class="text-center fw-bold {{ $stock->quantity <= 0 ? 'text-danger' : ($stock->isLow() ? 'text-warning' : 'text-success') }}">
                                        {{ $stock->quantity }}
                                    </td>
                                    <td class="text-center text-muted small">{{ $stock->low_stock_threshold ?? '—' }}</td>
                                    <td class="small text-muted">{{ $stock->sku ?? '—' }}</td>
                                    <td class="text-center">
                                        @if($stock->quantity <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($stock->isLow())
                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.stock.edit', $stock->id) }}"
                                           class="btn btn-sm btn-outline-primary me-1">Edit</a>

                                        {{-- Quick adjust --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#adjustModal{{ $stock->id }}">Adjust</button>

                                        {{-- Quick Adjust Modal --}}
                                        <div class="modal fade" id="adjustModal{{ $stock->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Quick Adjust: {{ Str::limit($stock->product->name ?? '', 25) }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.stock.adjust', $stock->id) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p class="text-muted small">Current qty: <strong>{{ $stock->quantity }}</strong></p>
                                                            <div class="mb-2">
                                                                <label class="form-label small">Adjustment (+ add, − subtract)</label>
                                                                <input type="number" name="adjustment" class="form-control form-control-sm"
                                                                       placeholder="e.g. 10 or -5" required>
                                                            </div>
                                                            <div>
                                                                <label class="form-label small">Note (optional)</label>
                                                                <input type="text" name="note" class="form-control form-control-sm"
                                                                       placeholder="Reason for adjustment">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No stock records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stocks->hasPages())
                <div class="card-footer bg-white">{{ $stocks->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
