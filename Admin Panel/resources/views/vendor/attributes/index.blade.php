@extends('vendor.layouts.app')
@section('title', 'Attributes')
@section('page-title', 'Product Attributes')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-sliders me-2 text-success"></i>Product Attributes</h5>
            <span class="badge bg-success-subtle text-success">{{ $attributes->total() }} total</span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Attributes (e.g. Weight, Size, Color) are managed by admin and can be assigned to your products.
        </p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Attribute Name</th>
                        <th>Type</th>
                        <th>Values</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attributes as $attr)
                    <tr>
                        <td class="text-muted small">{{ $attr->id }}</td>
                        <td class="fw-semibold">{{ $attr->name }}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary">{{ $attr->type ?? 'text' }}</span></td>
                        <td class="text-muted small">{{ $attr->values ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No attributes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attributes->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $attributes->links() }}
    </div>
    @endif
</div>
@endsection
