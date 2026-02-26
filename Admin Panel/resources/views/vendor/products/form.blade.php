@extends('vendor.layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST"
                      action="{{ isset($product) ? route('vendor.products.update', $product->id) : route('vendor.products.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Price & Discount --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price ({{ config('plantix.currency_symbol') }}) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" min="0"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', $product->price ?? '') }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Discount Price ({{ config('plantix.currency_symbol') }})</label>
                            <input type="number" name="discount_price" step="0.01" min="0"
                                   class="form-control @error('discount_price') is-invalid @enderror"
                                   value="{{ old('discount_price', $product->discount_price ?? '') }}">
                            @error('discount_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
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
                            <label class="form-label fw-semibold">SKU</label>
                            <input type="text" name="sku"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku', $product->sku ?? '') }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Stock --}}
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="track_stock" id="track_stock"
                                       class="form-check-input" value="1"
                                       {{ old('track_stock', $product->track_stock ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="track_stock">Track Stock</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="stock_qty_wrap">
                            <label class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" name="stock_quantity" min="0"
                                   class="form-control @error('stock_quantity') is-invalid @enderror"
                                   value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
                            @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" id="is_active"
                                       class="form-check-input" value="1"
                                       {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Active (visible in shop)</label>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Main Image</label>
                            @if(isset($product) && $product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="rounded" height="80" alt="Current Image">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Submit --}}
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ isset($product) ? 'Update Product' : 'Add Product' }}
                            </button>
                            <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
