@extends('vendor.layouts.app')
@section('title', 'Categories')
@section('page-title', 'Product Categories')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success"></i>Categories</h5>
            <span class="badge bg-success-subtle text-success">{{ $categories->total() }} total</span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Categories are managed by admin. Use these when creating or editing your products.
        </p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th class="text-center">Products (yours)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="text-muted small">{{ $cat->id }}</td>
                        <td class="fw-semibold">
                            @if($cat->image)
                                <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}"
                                     class="rounded me-2" style="width:28px;height:28px;object-fit:cover;">
                            @endif
                            {{ $cat->name }}
                        </td>
                        <td class="text-muted small">{{ Str::limit($cat->description ?? '—', 60) }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">
                                {{ $cat->products_count ?? '—' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
