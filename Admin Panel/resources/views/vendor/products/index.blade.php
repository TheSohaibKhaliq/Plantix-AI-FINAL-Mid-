@extends('vendor.layouts.app')
@section('title', 'Products')
@section('page-title', 'My Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam me-2 text-success"></i>Product Catalog</h4>
        <span class="text-muted small fw-medium mt-1 d-block">{{ $products->total() }} product(s) available</span>
    </div>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i>List New Product
    </a>
</div>

{{-- Search / Filter --}}
<form method="GET" class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Search Products</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-md bg-light border-start-0 ps-0"
                           placeholder="Search by name, SKU..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Status Filter</label>
                <select name="status" class="form-select form-select-md bg-light">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active (Visible)</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive (Hidden)</option>
                </select>
            </div>
            <div class="col-auto ms-auto align-self-end">
                <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm">
                    Apply Filters
                </button>
                <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary ms-2 px-4 rounded-pill">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 rounded-top-left-3">Product Image</th>
                        <th class="py-3">Name & SKU</th>
                        <th class="py-3">Pricing</th>
                        <th class="py-3">Inventory</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Listed On</th>
                        <th class="text-end py-3 px-4 rounded-top-right-3">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="hover-bg-light">
                        <td class="px-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     class="rounded-3 shadow-sm border" width="60" height="60" style="object-fit:cover"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center shadow-sm border"
                                     style="width:60px;height:60px">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                            @if($product->sku)
                                <div class="text-secondary small fw-medium mt-1"><i class="bi bi-upc-scan me-1"></i>SKU: {{ $product->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-success fs-6">
                                {{ config('plantix.currency_symbol') }}{{ number_format($product->price, 2) }}
                            </div>
                            @if($product->discount_price)
                                <div class="text-danger small fw-medium">
                                    <s>{{ config('plantix.currency_symbol') }}{{ number_format($product->discount_price, 2) }}</s>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($product->track_stock)
                                <span class="badge rounded-pill px-3 py-2 bg-opacity-10 border border-opacity-25
                                    bg-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success') }}
                                    text-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning text-dark' : 'success') }}
                                    border-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success') }}">
                                    <i class="bi bi-box me-1"></i>{{ $product->stock_quantity }} units
                                </span>
                            @else
                                <span class="text-muted small fw-medium"><i class="bi bi-infinity me-1"></i>Unlimited</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 shadow-sm
                                bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Unlisted' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark fw-medium small"><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $product->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="text-end px-4">
                            <div class="btn-group shadow-sm rounded-pill">
                                <a href="{{ route('vendor.products.edit', $product->id) }}"
                                   class="btn btn-sm btn-outline-success px-3" title="Edit Product">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="{{ route('vendor.products.destroy', $product->id) }}"
                                      onsubmit="return confirm('Delete this product permanently?');" class="m-0">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger px-3 rounded-end-pill" title="Delete Product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width:100px; height:100px;">
                                <i class="bi bi-box-seam fs-1 text-success opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-dark">No Products Found</h4>
                            <p class="text-muted">You haven't listed any products yet. Click "List New Product" to get started.</p>
                            <a href="{{ route('vendor.products.create') }}" class="btn btn-success rounded-pill px-4 mt-2">
                                <i class="bi bi-plus-lg me-1"></i>List Your First Product
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white p-4" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px;">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
