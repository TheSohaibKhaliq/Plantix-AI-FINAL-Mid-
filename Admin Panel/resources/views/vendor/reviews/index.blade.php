@extends('vendor.layouts.app')
@section('title', 'Product Reviews')
@section('page-title', 'Product Reviews & Ratings')

@section('content')
{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="display-6 fw-bold text-warning">{{ number_format($avgRating ?? 0, 1) }}</div>
            <div class="small text-muted">Average Rating</div>
            <div>
                @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ ($i <= round($avgRating ?? 0)) ? '-fill text-warning' : ' text-muted' }}"></i>
                @endfor
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="card border-0 shadow-sm p-3">
            @for($r=5;$r>=1;$r--)
            @php $count = $ratingCounts[$r] ?? 0; $total = $reviews->total() ?: 1; @endphp
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="small" style="min-width:12px">{{ $r }}</span>
                <i class="bi bi-star-fill text-warning small"></i>
                <div class="progress flex-grow-1" style="height:10px;">
                    <div class="progress-bar bg-warning" style="width:{{ round($count/$total*100) }}%"></div>
                </div>
                <span class="small text-muted" style="min-width:24px">{{ $count }}</span>
            </div>
            @endfor
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <select name="rating" class="form-select form-select-sm">
            <option value="">All Ratings</option>
            @foreach([5,4,3,2,1] as $r)
                <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} Star</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-success btn-sm">Filter</button>
    </div>
    @if(request()->hasAny(['rating','product_id']))
    <div class="col-auto">
        <a href="{{ route('vendor.reviews.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
    </div>
    @endif
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th class="text-center">Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td class="small fw-semibold">{{ $review->product->name ?? '—' }}</td>
                        <td class="small">{{ $review->user->name ?? '—' }}</td>
                        <td class="text-center">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ ($i <= $review->rating) ? '-fill text-warning' : ' text-muted' }}"></i>
                            @endfor
                        </td>
                        <td class="small text-muted">{{ Str::limit($review->comment ?? '—', 60) }}</td>
                        <td class="small text-muted">{{ $review->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('vendor.reviews.show', $review->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No reviews yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
    <div class="card-footer bg-white border-0">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
