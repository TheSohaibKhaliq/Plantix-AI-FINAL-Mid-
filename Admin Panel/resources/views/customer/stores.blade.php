@extends('layouts.frontend')

@section('title', 'AgriTech Stores | Plantix-AI')

@section('page_styles')
    <style>
        .store-card {
            background: white;
            border-radius: var(--agri-radius-md);
            overflow: hidden;
            box-shadow: var(--agri-shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid var(--agri-border);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .store-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--agri-shadow-md);
            border-color: rgba(16, 185, 129, 0.4);
        }
        .store-cover {
            height: 140px;
            background: linear-gradient(135deg, var(--agri-primary-light), rgba(16, 185, 129, 0.2));
            position: relative;
            background-size: cover;
            background-position: center;
        }
        .store-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            position: absolute;
            bottom: -35px;
            left: 20px;
            box-shadow: var(--agri-shadow-sm);
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .store-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .store-logo i {
            font-size: 24px;
            color: var(--agri-primary);
        }
        .store-body {
            padding: 45px 20px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .store-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--agri-dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .store-desc {
            font-size: 14px;
            color: var(--agri-text-muted);
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .store-meta {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--agri-bg);
            padding: 10px 15px;
            border-radius: var(--agri-radius-sm);
            font-size: 13px;
        }
        .store-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #f59e0b;
            font-weight: 600;
        }
        .store-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--agri-primary);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .store-controls-bar {
            background: white;
            border-radius: var(--agri-radius-md);
            padding: 15px 25px;
            box-shadow: var(--agri-shadow-sm);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .store-search input {
            border: 1px solid var(--agri-border);
            border-radius: var(--agri-radius-sm);
            padding: 10px 15px;
            width: 300px;
            outline: none;
        }
        .store-search input:focus {
            border-color: var(--agri-primary);
        }
        .store-filters select {
            border: 1px solid var(--agri-border);
            border-radius: var(--agri-radius-sm);
            padding: 10px 30px 10px 15px;
            background-color: var(--agri-bg);
            outline: none;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
        }
    </style>
@endsection

@section('footer')
@include('partials.footer-alt')
@endsection

@section('content')

    <!-- Start Breadcrumb -->
    <div class="py-5 bg-light" style="background: linear-gradient(to right, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02)); border-bottom: 1px solid var(--agri-border);">
        <div class="container-agri">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="fw-bold mb-2 text-dark" style="font-size: 32px;">Partner Stores & Vendors</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none"><i class="fas fa-home me-1"></i> Home</a></li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Stores</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                    <p class="text-muted mb-0"><i class="fas fa-store text-success me-2"></i> Shop directly from verified agricultural suppliers</p>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Start Stores Grid -->
    <div class="py-5" style="background: var(--agri-bg); min-height: 80vh;">
        <div class="container-agri pb-5">
            
            <div class="store-controls-bar">
                <form action="{{ route('stores') }}" method="GET" class="store-search d-flex gap-2">
                    <input type="text" name="search" placeholder="Search by name or description..." value="{{ request('search') }}">
                    <button type="submit" class="btn-agri btn-agri-primary border-0" style="padding: 10px 20px;">Search</button>
                    @if(request('search') || request('sort'))
                        <a href="{{ route('stores') }}" class="btn-agri btn-agri-outline border-0" style="padding: 10px 20px;">Clear</a>
                    @endif
                </form>
                
                <form action="{{ route('stores') }}" method="GET" class="store-filters d-flex align-items-center gap-2">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <label for="sort" class="text-muted fw-bold mb-0">Sort By:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Top Rated</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </form>
            </div>

            @if($stores->count() > 0)
                <div class="row g-4">
                    @foreach($stores as $store)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <a href="{{ route('stores.single', $store->id) }}" class="text-decoration-none">
                                <div class="store-card w-100">
                                    <div class="store-cover" style="background-image: url('{{ $store->cover_photo ? Storage::url($store->cover_photo) : '' }}'); {{ !$store->cover_photo ? 'background: linear-gradient(135deg, var(--agri-primary-light), rgba(16, 185, 129, 0.2));' : '' }}">
                                        @if($store->is_approved)
                                            <span class="store-badge"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                        @endif
                                        <div class="store-logo">
                                            @if($store->image)
                                                <img src="{{ Storage::url($store->image) }}" alt="{{ $store->title }}">
                                            @else
                                                <i class="fas fa-store"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="store-body">
                                        <h3 class="store-title">{{ $store->title }}</h3>
                                        <p class="store-desc">{{ $store->description ?? 'Premium agricultural supplier in Pakistan.' }}</p>
                                        
                                        <div class="mb-3 text-muted" style="font-size: 13px;">
                                            @if($store->address)
                                                <div class="mb-1"><i class="fas fa-map-marker-alt text-success me-2 width-15 text-center"></i> {{ Str::limit($store->address, 30) }}</div>
                                            @endif
                                            <div class="mb-1"><i class="far fa-clock text-success me-2 width-15 text-center"></i> {{ \Carbon\Carbon::parse($store->open_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($store->close_time)->format('h:i A') }}</div>
                                            <div><i class="fas fa-truck text-success me-2 width-15 text-center"></i> Delivery: Rs. {{ number_format($store->delivery_fee, 0) }}</div>
                                        </div>

                                        <div class="store-meta">
                                            <div class="store-rating">
                                                <i class="fas fa-star"></i> {{ number_format($store->rating, 1) }} <span class="text-muted fw-normal" style="font-size: 12px;">({{ $store->review_count }})</span>
                                            </div>
                                            <div class="text-primary fw-bold" style="font-size: 13px;">
                                                Visit Store <i class="fas fa-arrow-right ms-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-5 d-flex justify-content-center">
                    {{ $stores->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5 bg-white shadow-sm" style="border-radius: var(--agri-radius-md);">
                    <div class="mb-4 text-muted" style="font-size: 60px;">
                        <i class="fas fa-store-slash"></i>
                    </div>
                    <h3 class="fw-bold mb-2">No Stores Found</h3>
                    <p class="text-muted mb-4">We couldn't find any vendor matching your search criteria.</p>
                    <a href="{{ route('stores') }}" class="btn-agri btn-agri-primary text-decoration-none px-4">Clear Filters</a>
                </div>
            @endif

        </div>
    </div>
    <!-- End Stores Grid -->
@endsection
