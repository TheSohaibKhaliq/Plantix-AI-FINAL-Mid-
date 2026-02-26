@extends('vendor.layouts.app')
@section('title', 'Products')
@section('page-title', 'My Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="text-muted small">{{ $products->total() }} product(s)</span>
    </div>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
</div>

{{-- Search / Filter --}}
<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control"
                       placeholder="Search products…" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     class="rounded" width="50" height="50" style="object-fit:cover"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                     style="width:50px;height:50px">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            @if($product->sku)
                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                            @endif
                        </td>
                        <td>
                            {{ config('plantix.currency_symbol') }}{{ number_format($product->price, 2) }}
                            @if($product->discount_price)
                                <br><small class="text-danger">
                                    <s>{{ config('plantix.currency_symbol') }}{{ number_format($product->discount_price, 2) }}</s>
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($product->track_stock)
                                <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success') }}">
                                    {{ $product->stock_quantity }} units
                                </span>
                            @else
                                <span class="text-muted small">Untracked</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $product->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('vendor.products.edit', $product->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('vendor.products.destroy', $product->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                            No products found.
                            <a href="{{ route('vendor.products.create') }}">Add your first product</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-transparent">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
