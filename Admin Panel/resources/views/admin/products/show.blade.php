@extends('layouts.app')

@section('title', 'Product: '.$product->name)

@section('content')
<div class="container-fluid">

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <a href="{{ route('admin.products.index') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                    Products
                </a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <span style="color: var(--agri-primary); font-size: 13px; font-weight: 600;">{{ $product->name }}</span>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; color: var(--agri-primary-dark); margin: 0;">{{ $product->name }}</h1>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-agri btn-agri-outline" style="text-decoration: none; color: #D97706; border-color: #FDE68A; background: #FEF3C7; padding: 10px 24px;">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}"
                  class="d-inline" onsubmit="return confirm('Delete this product?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-agri" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 10px 24px;">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px; font-size: 14px; font-weight: 600; margin-bottom: 24px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Main Info --}}
        <div class="col-lg-8">
            <div class="card-agri mb-4" style="padding: 24px;">
                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 alt="{{ $product->name }}"
                                 style="max-height: 200px; width: 100%; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        @else
                            <div style="background: var(--agri-bg); border-radius: 12px; height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 48px; color: var(--agri-border);"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h4 style="font-size: 20px; font-weight: 800; color: var(--agri-text-heading); margin-bottom: 8px;">{{ $product->name }}</h4>
                        <p style="color: var(--agri-text-muted); font-size: 14px; margin-bottom: 24px; line-height: 1.6;">{{ $product->short_description }}</p>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                            <div style="background: var(--agri-bg); padding: 12px 16px; border-radius: 12px;">
                                <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 4px;">SKU</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--agri-text-main);">{{ $product->sku ?? '—' }}</div>
                            </div>
                            <div style="background: var(--agri-bg); padding: 12px 16px; border-radius: 12px;">
                                <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 4px;">Category</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--agri-text-main);">{{ $product->category->name ?? '—' }}</div>
                            </div>
                            <div style="background: var(--agri-bg); padding: 12px 16px; border-radius: 12px;">
                                <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 4px;">Vendor</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--agri-text-main);">{{ $product->vendor->name ?? '—' }}</div>
                            </div>
                            <div style="background: var(--agri-bg); padding: 12px 16px; border-radius: 12px;">
                                <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 4px;">Price</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--agri-primary);">
                                    Rs {{ number_format($product->price, 2) }} <span style="font-size: 13px; font-weight: 500; color: var(--agri-text-muted);">/ {{ $product->unit ?? 'unit' }}</span>
                                    @if($product->sale_price)
                                        <div style="margin-top: 4px;">
                                            <span style="background: #FEE2E2; color: #991B1B; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; border: 1px solid #FECACA;">
                                                Sale: Rs {{ number_format($product->sale_price, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->description)
                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px dashed var(--agri-border);">
                        <h6 style="font-size: 13px; font-weight: 800; color: var(--agri-text-heading); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">Description</h6>
                        <div style="color: var(--agri-text-main); font-size: 14px; line-height: 1.7;">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Gallery --}}
            @if($product->images && $product->images->where('is_primary', false)->count())
                <div class="card-agri mb-4" style="padding: 24px;">
                    <h6 style="font-size: 14px; font-weight: 800; color: var(--agri-text-heading); text-transform: uppercase; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-images text-success"></i> Gallery
                    </h6>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        @foreach($product->images->where('is_primary', false) as $img)
                            <img src="{{ asset('storage/'.$img->path) }}"
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--agri-border); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar: stock & flags --}}
        <div class="col-lg-4">
            <div class="card-agri mb-4" style="padding: 0; overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px dashed var(--agri-border); background: var(--agri-bg);">
                    <h6 style="font-size: 13px; font-weight: 800; color: var(--agri-text-heading); text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-boxes text-success"></i> Stock Availability
                    </h6>
                </div>
                <div style="padding: 24px; text-align: center;">
                    <div style="font-size: 48px; font-weight: 900; line-height: 1; {{ ($product->stock_quantity ?? 0) <= ($product->low_stock_threshold ?? 10) ? 'color: #DC2626;' : 'color: var(--agri-primary-dark);' }}">
                        {{ $product->stock_quantity ?? 0 }}
                    </div>
                    <p style="font-size: 13px; font-weight: 600; color: var(--agri-text-muted); text-transform: uppercase; margin-top: 8px; margin-bottom: 24px; letter-spacing: 0.5px;">units in stock</p>
                    
                    <div style="background: #F8FAFC; border-radius: 8px; padding: 12px; font-size: 12px; color: var(--agri-text-muted); display: inline-block;">
                        <i class="fas fa-info-circle me-1" style="color: #94A3B8;"></i>
                        Low-stock alert threshold: <strong style="color: var(--agri-text-heading);">{{ $product->low_stock_threshold ?? config('plantix.low_stock_threshold') }} units</strong>
                    </div>
                </div>
            </div>

            <div class="card-agri mb-4" style="padding: 0; overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px dashed var(--agri-border); background: var(--agri-bg);">
                    <h6 style="font-size: 13px; font-weight: 800; color: var(--agri-text-heading); text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-toggle-on text-success"></i> Status & Flags
                    </h6>
                </div>
                <div style="padding: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--agri-text-muted);">Active</span>
                            @if($product->is_active)
                                <span style="background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #A7F3D0;">Yes</span>
                            @else
                                <span style="background: #F3F4F6; color: #4B5563; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #E5E7EB;">No</span>
                            @endif
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--agri-text-muted);">Featured</span>
                            @if($product->is_featured)
                                <span style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #FDE68A;"><i class="fas fa-star text-warning me-1"></i>Yes</span>
                            @else
                                <span style="background: #F3F4F6; color: #4B5563; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #E5E7EB;">No</span>
                            @endif
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--agri-text-muted);">Returnable</span>
                            @if($product->is_returnable)
                                <span style="background: #E0F2FE; color: #0369A1; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #BAE6FD;">Yes ({{ $product->return_window_days ?? 7 }} days)</span>
                            @else
                                <span style="background: #F3F4F6; color: #4B5563; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 800; border: 1px solid #E5E7EB;">No</span>
                            @endif
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--agri-text-muted);">Tax Rate</span>
                            <span style="font-size: 14px; font-weight: 800; color: var(--agri-text-heading);">{{ $product->tax_rate ?? 0 }}%</span>
                        </div>
                    </div>

                    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px dashed var(--agri-border);">
                        <form method="POST" action="{{ route('admin.products.toggle-featured', $product->id) }}">
                            @csrf
                            <button type="submit" class="btn-agri w-100" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; padding-top: 12px; padding-bottom: 12px; font-size: 13px; box-shadow: none;">
                                <i class="fas fa-star me-2" style="color: #D97706;"></i>
                                {{ $product->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-agri" style="padding: 0; overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px dashed var(--agri-border); background: var(--agri-bg);">
                    <h6 style="font-size: 13px; font-weight: 800; color: var(--agri-text-heading); text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-clock text-success"></i> Timestamps
                    </h6>
                </div>
                <div style="padding: 20px 24px;">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 2px;">Created</div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--agri-text-main);">{{ $product->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 2px;">Updated</div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--agri-text-main);">{{ $product->updated_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
