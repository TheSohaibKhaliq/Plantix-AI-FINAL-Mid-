@extends('vendor.layouts.app')
@section('title', 'Categories')
@section('page-title', 'Product Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-tags-fill me-2 text-primary"></i>Product Categories</h4>
        <span class="text-muted small fw-medium mt-1 d-block">View available categories managed by administrators for your products</span>
    </div>
</div>

<div class="card border-0 shadow-sm hover-card pt-2" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-success fs-5"></i>All Categories</h6>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 shadow-sm">{{ $categories->total() }} Categories</span>
    </div>
    <div class="card-body p-0">
        @if($categories->isEmpty())
            <div class="text-center text-muted py-5 my-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-muted" style="width: 80px; height: 80px;">
                    <i class="bi bi-tags fs-1"></i>
                </div>
                <h6 class="fw-bold text-dark">No categories found</h6>
                <p class="small mb-0">There are currently no product categories available.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 fw-semibold text-muted text-uppercase small" style="width: 80px;">ID</th>
                            <th class="fw-semibold text-muted text-uppercase small">Category Name</th>
                            <th class="fw-semibold text-muted text-uppercase small">Description</th>
                            <th class="text-center pe-4 fw-semibold text-muted text-uppercase small">Your Products</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace text-muted small">#{{ $cat->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($cat->image)
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 border shadow-sm flex-shrink-0" style="width:40px;height:40px;">
                                            <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}" class="rounded w-100 h-100" style="object-fit:cover;">
                                        </div>
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 border shadow-sm flex-shrink-0" style="width:40px;height:40px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                    <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small d-inline-block text-truncate" style="max-width: 300px;" title="{{ $cat->description }}">
                                    {{ Str::limit($cat->description ?? 'No description provided.', 60) }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <span class="badge {{ ($cat->products_count ?? 0) > 0 ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' : 'bg-light border text-muted' }} rounded-pill px-3 py-1 shadow-sm fs-6">
                                    {{ $cat->products_count ?? '0' }} Products
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($categories->hasPages())
        <div class="card-footer bg-white border-top p-4 d-flex justify-content-center" style="border-radius: 0 0 16px 16px;">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
