@extends('vendor.layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="row border-0 justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi {{ isset($product) ? 'bi-pencil-square' : 'bi-plus-circle' }} me-2 text-success fs-4"></i>
                    {{ isset($product) ? 'Edit Product Details' : 'Add New Product' }}
                </h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form method="POST"
                      action="{{ isset($product) ? route('vendor.products.update', $product->id) : route('vendor.products.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    <div class="row g-4">
                        {{-- Name --}}
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name ?? '') }}" placeholder="Enter a descriptive product name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Description</label>
                            <textarea name="description" rows="5"
                                      class="form-control fs-6 rounded-3 bg-light border-0 @error('description') is-invalid @enderror" placeholder="Describe the product features, benefits, and key details...">{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Price --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Selling Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success bg-opacity-10 text-success border-0 rounded-start-3 fw-bold">{{ config('plantix.currency_symbol', 'PKR') }}</span>
                                <input type="number" name="price" step="0.01" min="0"
                                       class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('price') is-invalid @enderror"
                                       value="{{ old('price', $product->price ?? '') }}" placeholder="0.00" required>
                                @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Discount --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Discounted Price</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger bg-opacity-10 text-danger border-0 rounded-start-3 fw-bold">{{ config('plantix.currency_symbol', 'PKR') }}</span>
                                <input type="number" name="discount_price" step="0.01" min="0"
                                       class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('discount_price') is-invalid @enderror"
                                       value="{{ old('discount_price', $product->discount_price ?? '') }}" placeholder="0.00 (Optional)">
                                @error('discount_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">Category</label>
                            <select name="category_id" class="form-select form-select-lg fs-6 rounded-3 bg-light border-0 @error('category_id') is-invalid @enderror">
                                <option value="">— Select Category —</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- SKU --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-1">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku"
                                   class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('sku') is-invalid @enderror"
                                   value="{{ old('sku', $product->sku ?? '') }}" placeholder="e.g. PRD-001">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Admin configuration settings --}}
                        <div class="col-12 my-2">
                            <hr class="text-muted opacity-25">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-gear me-2 text-primary"></i>Configuration</h6>
                        </div>

                        {{-- Stock Tracking --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 rounded-3 h-100 p-3">
                                <label class="form-label text-muted text-uppercase fw-bold small mb-2 w-100">Inventory Tracking</label>
                                <div class="form-check form-switch mt-1">
                                    <input type="checkbox" name="track_stock" id="track_stock"
                                           class="form-check-input fs-4 cursor-pointer" value="1"
                                           {{ old('track_stock', $product->track_stock ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold mt-1 ms-2" for="track_stock">Enable Stock Tracking</label>
                                </div>
                                <div class="mt-3" id="stock_qty_wrap">
                                    <label class="form-label text-muted fw-bold small mb-1">Stock Quantity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-0 rounded-start-3"><i class="bi bi-box-seam"></i></span>
                                        <input type="number" name="stock_quantity" min="0"
                                               class="form-control border-0 rounded-end-3"
                                               value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
                                    </div>
                                    @error('stock_quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 rounded-3 h-100 p-3">
                                <label class="form-label text-muted text-uppercase fw-bold small mb-2 w-100">Visibility Status</label>
                                <div class="form-check form-switch mt-1">
                                    <input type="checkbox" name="is_active" id="is_active"
                                           class="form-check-input fs-4 cursor-pointer" value="1"
                                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold mt-1 ms-2" for="is_active">Active (Visible in Shop)</label>
                                </div>
                                <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Turn off to hide this product from customers while preserving data.</p>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="col-12 mt-4">
                            <label class="form-label text-muted text-uppercase fw-bold small mb-2">Main Product Image <span class="text-danger">*</span></label>
                            @if(isset($product) && $product->image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="rounded-3 shadow-sm border" height="120" style="object-fit:cover;" alt="Current Image">
                                    <div class="form-text mt-1"><i class="bi bi-check-circle text-success me-1"></i>Current Image uploaded.</div>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 @error('image') is-invalid @enderror"
                                   accept="image/*">
                            <div class="form-text text-muted mt-2 fw-medium"><i class="bi bi-card-image me-1"></i>Square images work best. Max 2MB (JPG, PNG).</div>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Submit --}}
                        <div class="col-12 mt-5 border-top pt-4 text-end">
                            <a href="{{ route('vendor.products.index') }}" class="btn btn-light text-dark border rounded-pill px-4 py-2 fw-bold shadow-sm me-2">Cancel</a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-2"></i>{{ isset($product) ? 'Save Changes' : 'Publish Product' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const trackCheck = document.getElementById('track_stock');
    const qtyWrap    = document.getElementById('stock_qty_wrap');
    function toggleQty() { qtyWrap.style.display = trackCheck.checked ? '' : 'none'; }
    trackCheck.addEventListener('change', toggleQty);
    toggleQty();
</script>
@endpush
