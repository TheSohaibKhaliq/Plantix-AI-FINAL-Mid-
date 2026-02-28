@extends('vendor.layouts.app')
@section('title', 'Attributes')
@section('page-title', 'Product Attributes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Product Attributes</h4>
        <span class="text-muted small fw-medium mt-1 d-block">Manage product variations like Weight, Size, and Color used across the store</span>
    </div>
</div>

<div class="card border-0 shadow-sm hover-card pt-2" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-success fs-5"></i>All Attributes</h6>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 shadow-sm">{{ $attributes->total() }} Attributes</span>
    </div>
    <div class="card-body p-0">
        @if($attributes->isEmpty())
            <div class="text-center text-muted py-5 my-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-muted" style="width: 80px; height: 80px;">
                    <i class="bi bi-sliders fs-1"></i>
                </div>
                <h6 class="fw-bold text-dark">No attributes found</h6>
                <p class="small mb-0">There are currently no product attributes managed by the administration.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 fw-semibold text-muted text-uppercase small" style="width: 80px;">ID</th>
                            <th class="fw-semibold text-muted text-uppercase small">Attribute Name</th>
                            <th class="fw-semibold text-muted text-uppercase small">Type</th>
                            <th class="pe-4 fw-semibold text-muted text-uppercase small">Values</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attributes as $attr)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace text-muted small">#{{ $attr->id }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $attr->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 shadow-sm text-uppercase" style="font-size: 0.70rem;">
                                    {{ $attr->type ?? 'text' }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex flex-wrap gap-1">
                                    @if($attr->values)
                                        @foreach(explode(',', $attr->values) as $val)
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-medium shadow-sm">{{ trim($val) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small fst-italic">No predefined values</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($attributes->hasPages())
        <div class="card-footer bg-white border-top p-4 d-flex justify-content-center" style="border-radius: 0 0 16px 16px;">
            {{ $attributes->links() }}
        </div>
    @endif
</div>
@endsection
