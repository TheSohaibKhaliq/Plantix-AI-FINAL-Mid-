{{-- Shared product form partial: used by both create.blade.php and edit.blade.php --}}
{{-- $product is only defined when editing --}}

<div class="row g-4">

    {{-- ── Left column: core details ── --}}
    <div class="col-lg-8">

        {{-- Basic Info --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Basic Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku', $product->sku ?? '') }}">
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price (Rs) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="price"
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $product->price ?? '') }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sale Price (Rs)</label>
                        <input type="number" step="0.01" min="0" name="sale_price"
                               class="form-control @error('sale_price') is-invalid @enderror"
                               value="{{ old('sale_price', $product->sale_price ?? '') }}">
                        @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Short Description</label>
                    <input type="text" name="short_description"
                           class="form-control @error('short_description') is-invalid @enderror"
                           value="{{ old('short_description', $product->short_description ?? '') }}"
                           maxlength="255">
                    @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mt-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" rows="5"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Classification --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Classification</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">— No Brand —</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                            <option value="">— Select Vendor —</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    @selected(old('vendor_id', $product->vendor_id ?? '') == $vendor->id)>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit"
                               class="form-control @error('unit') is-invalid @enderror"
                               value="{{ old('unit', $product->unit ?? '') }}"
                               placeholder="e.g. kg, litre, bag">
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" min="0" name="stock_quantity"
                               class="form-control @error('stock_quantity') is-invalid @enderror"
                               value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
                        @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Low-Stock Alert Threshold</label>
                        <input type="number" min="0" name="low_stock_threshold"
                               class="form-control @error('low_stock_threshold') is-invalid @enderror"
                               value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? config('plantix.low_stock_threshold')) }}">
                        @error('low_stock_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end left col --}}

    {{-- ── Right column: images & flags ── --}}
    <div class="col-lg-4">

        {{-- Primary Image --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Primary Image</div>
            <div class="card-body">
                @isset($product)
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}"
                             alt="Current image"
                             class="img-fluid rounded mb-2"
                             style="max-height:180px;object-fit:cover;">
                        <p class="text-muted small mb-2">Upload a new image to replace it.</p>
                    @endif
                @endisset
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                       accept="image/*">
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Gallery --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Gallery Images</div>
            <div class="card-body">
                @isset($product)
                    @if($product->images && $product->images->count())
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @foreach($product->images->where('is_primary', false) as $img)
                                <img src="{{ asset('storage/'.$img->path) }}"
                                     class="rounded border"
                                     style="width:56px;height:56px;object-fit:cover;">
                            @endforeach
                        </div>
                    @endif
                @endisset
                <input type="file" name="gallery[]" class="form-control @error('gallery') is-invalid @enderror"
                       accept="image/*" multiple>
                <small class="text-muted">Select multiple to add new gallery images.</small>
                @error('gallery') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Status Flags --}}
        <div class="card">
            <div class="card-header fw-semibold">Status &amp; Visibility</div>
            <div class="card-body">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible in shop)</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                           value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured on homepage</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_returnable" id="is_returnable"
                           value="1" {{ old('is_returnable', $product->is_returnable ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_returnable">Returnable</label>
                </div>

                <hr>

                <div class="mb-2">
                    <label class="form-label small">Return Window (days)</label>
                    <input type="number" min="0" name="return_window_days"
                           class="form-control form-control-sm @error('return_window_days') is-invalid @enderror"
                           value="{{ old('return_window_days', $product->return_window_days ?? 7) }}">
                    @error('return_window_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label small">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" name="tax_rate"
                           class="form-control form-control-sm @error('tax_rate') is-invalid @enderror"
                           value="{{ old('tax_rate', $product->tax_rate ?? config('plantix.tax_rate')) }}">
                    @error('tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

    </div>{{-- end right col --}}

</div>{{-- end row --}}
