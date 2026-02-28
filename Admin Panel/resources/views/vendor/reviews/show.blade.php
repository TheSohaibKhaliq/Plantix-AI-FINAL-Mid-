@extends('vendor.layouts.app')
@section('title', 'Review Detail')
@section('page-title', 'Review Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-chat-quote me-2 text-warning"></i>Customer Review</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Product</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $review->product->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Customer</dt>
                    <dd class="col-sm-8">{{ $review->user->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Order</dt>
                    <dd class="col-sm-8">{{ $review->order->order_number ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted">Rating</dt>
                    <dd class="col-sm-8">
                        @for($i=1;$i<=5;$i++)
                            <i class="bi bi-star{{ ($i <= $review->rating) ? '-fill text-warning' : ' text-muted' }}"></i>
                        @endfor
                        <span class="ms-2 small text-muted">({{ $review->rating }}/5)</span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Comment</dt>
                    <dd class="col-sm-8">{{ $review->comment ?: 'No comment provided.' }}</dd>

                    <dt class="col-sm-4 text-muted">Submitted</dt>
                    <dd class="col-sm-8 text-muted small">{{ $review->created_at->format('d M Y, H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('vendor.reviews.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Reviews
            </a>
        </div>
    </div>
</div>
@endsection
