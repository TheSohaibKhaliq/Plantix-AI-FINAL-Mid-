@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Products</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Product
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search name / SKU…" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="vendor_id" class="form-select form-select-sm">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected(($filters['vendor_id'] ?? '') == $vendor->id)>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="">Status</option>
                        <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
                        <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="is_featured" class="form-select form-select-sm">
                        <option value="">Featured</option>
                        <option value="1" @selected(($filters['is_featured'] ?? '') === '1')>Yes</option>
                        <option value="0" @selected(($filters['is_featured'] ?? '') === '0')>No</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Brand</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Featured</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width:48px;height:48px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->sku }}</small>
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ $product->vendor->name ?? '—' }}</td>
                                <td>{{ $product->brand->name ?? '—' }}</td>
                                <td class="text-end">Rs {{ number_format($product->price, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $product->stock_quantity <= 10 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->stock_quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST"
                                          action="{{ route('admin.products.toggle-featured', $product->id) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm border-0 p-0"
                                                title="Toggle Featured">
                                            @if($product->is_featured)
                                                <i class="fas fa-star text-warning fs-5"></i>
                                            @else
                                                <i class="far fa-star text-muted fs-5"></i>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.products.destroy', $product->id) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer">
                {{ $products->appends($filters)->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
