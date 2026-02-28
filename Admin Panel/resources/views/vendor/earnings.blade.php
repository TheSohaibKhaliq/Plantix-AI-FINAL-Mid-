@extends('vendor.layouts.app')
@section('title', 'Earnings & Payouts')
@section('page-title', 'Earnings & Payouts')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Earnings & Payouts</h4>
        <span class="text-muted small fw-medium mt-1 d-block">Track your revenue, view payouts, and request withdrawals</span>
    </div>
</div>
{{-- ── Alerts ─────────────────────────────────────────────────────────────── --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4 mb-4">

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius:16px; overflow:hidden;">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute opacity-10" style="right:-20px; top:-20px;">
                    <i class="bi bi-cash-stack text-success" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success me-3 shadow-sm d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div class="text-uppercase fw-bold text-muted small position-relative z-1">Total Earnings</div>
                </div>
                <div class="fs-2 fw-bold text-dark position-relative z-1">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['totalEarnings'], 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius:16px; overflow:hidden;">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute opacity-10" style="right:-20px; top:-20px;">
                    <i class="bi bi-calendar-check text-primary" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary me-3 shadow-sm d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <div class="text-uppercase fw-bold text-muted small position-relative z-1">This Month</div>
                </div>
                <div class="fs-2 fw-bold text-dark position-relative z-1">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['monthEarnings'], 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius:16px; overflow:hidden; border-bottom: 4px solid var(--bs-warning) !important;">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute opacity-10" style="right:-20px; top:-20px;">
                    <i class="bi bi-wallet2 text-warning" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning me-3 shadow-sm d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div class="text-uppercase fw-bold text-muted small position-relative z-1">Wallet Balance</div>
                </div>
                <div class="fs-2 fw-bold text-dark position-relative z-1">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['walletBalance'], 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius:16px; overflow:hidden;">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute opacity-10" style="right:-20px; top:-20px;">
                    <i class="bi bi-hourglass-split text-info" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info me-3 shadow-sm d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div class="text-uppercase fw-bold text-muted small position-relative z-1">Pending Payout</div>
                </div>
                <div class="fs-2 fw-bold text-dark position-relative z-1">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['pendingPayout'], 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius:16px; overflow:hidden;">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute opacity-10" style="right:-20px; top:-20px;">
                    <i class="bi bi-cash-coin text-secondary" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-2 bg-secondary bg-opacity-10 text-secondary me-3 shadow-sm d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                        <i class="bi bi-arrow-up-right-circle fs-4"></i>
                    </div>
                    <div class="text-uppercase fw-bold text-muted small position-relative z-1">Total Paid Out</div>
                </div>
                <div class="fs-2 fw-bold text-dark position-relative z-1">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['totalPaidOut'], 2) }}
                </div>
            </div>
        </div>
    </div>

</div>

    {{-- ── Monthly Earnings Chart ───────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill me-2 text-primary fs-5"></i>Monthly Earnings (Last 6 Months)</h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 300px; width: 100%;">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Request Payout ───────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm hover-card mb-4" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-send-fill me-2 text-success fs-5"></i>Request Payout</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('vendor.payouts.request') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Amount ({{ config('plantix.currency_symbol') }}) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-3 fw-bold">{{ config('plantix.currency_symbol') }}</span>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg fs-6 bg-light border-0 rounded-end-3 @error('amount') is-invalid @enderror"
                                   placeholder="0.00" max="{{ $stats['walletBalance'] }}" required>
                        </div>
                        @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="form-text small mt-1">Available to withdraw: <strong class="text-success">{{ config('plantix.currency_symbol') }}{{ number_format($stats['walletBalance'], 2) }}</strong></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Payout Method <span class="text-danger">*</span></label>
                        <select name="method" class="form-select form-select-lg fs-6 bg-light border-0 rounded-3 @error('method') is-invalid @enderror" required>
                            <option value="">— Select method —</option>
                            <option value="bank"   {{ old('method') === 'bank'   ? 'selected' : '' }}>🏦 Bank Transfer</option>
                            <option value="paypal" {{ old('method') === 'paypal' ? 'selected' : '' }}>🔵 PayPal</option>
                            <option value="stripe" {{ old('method') === 'stripe' ? 'selected' : '' }}>💳 Stripe</option>
                            <option value="wallet" {{ old('method') === 'wallet' ? 'selected' : '' }}>💼 Keep in Wallet</option>
                        </select>
                        @error('method')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Bank / Payment Details</label>
                        <textarea name="bank_details" rows="3" class="form-control fs-6 bg-light border-0 rounded-3"
                                  placeholder="Account number, IBAN, PayPal email…">{{ old('bank_details') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-1">Notes (Optional)</label>
                        <input type="text" name="notes" class="form-control form-control-lg fs-6 bg-light border-0 rounded-3" value="{{ old('notes') }}" placeholder="Any extra instructions…">
                    </div>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold w-100 py-2 shadow-sm" {{ $stats['walletBalance'] <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-send me-2"></i>Submit Payout Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Payout Request History ───────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary fs-5"></i>Recent Payout History</h6>
            </div>
            <div class="card-body p-0">
                @if ($payoutRequests->isEmpty())
                    <div class="text-center text-muted py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-muted" style="width: 80px; height: 80px;">
                            <i class="bi bi-inbox fs-1"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No payouts yet</h6>
                        <p class="small mb-0">You haven't requested any payouts.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th class="pe-4">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payoutRequests as $pr)
                                    <tr>
                                        <td class="ps-4 text-muted small fw-medium">{{ $pr->created_at->format('d M, Y') }}<br><span style="font-size:0.75rem;">{{ $pr->created_at->format('h:i A') }}</span></td>
                                        <td class="fw-bold text-dark">
                                            {{ config('plantix.currency_symbol') }}{{ number_format($pr->amount, 2) }}
                                        </td>
                                        <td class="text-capitalize">
                                            @if($pr->method == 'bank') <i class="bi bi-bank me-1 text-muted"></i>Bank
                                            @elseif($pr->method == 'paypal') <i class="bi bi-paypal me-1 text-info"></i>PayPal
                                            @elseif($pr->method == 'stripe') <i class="bi bi-credit-card me-1 text-primary"></i>Stripe
                                            @else <i class="bi bi-wallet me-1 text-secondary"></i>{{ $pr->method ?? '—' }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badgeMap = [
                                                    'pending'  => 'warning text-dark',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                $badge = $badgeMap[$pr->status] ?? 'secondary';
                                                
                                                $iconMap = [
                                                    'pending'  => 'bi-hourglass-split',
                                                    'approved' => 'bi-check-circle-fill',
                                                    'rejected' => 'bi-x-circle-fill',
                                                ];
                                                $icon = $iconMap[$pr->status] ?? 'bi-info-circle';
                                            @endphp
                                            <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ str_replace(' text-dark', '', $badge) }} border border-{{ str_replace(' text-dark', '', $badge) }} border-opacity-25 rounded-pill px-3 py-2 text-capitalize shadow-sm">
                                                <i class="bi {{ $icon }} me-1"></i>{{ $pr->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted small pe-4">{{ Str::limit($pr->notes, 30, '...') ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($payoutRequests->hasPages())
                        <div class="p-3 border-top">
                            {{ $payoutRequests->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ── Wallet Transactions ──────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-wallet-fill me-2 text-info fs-5"></i>Recent Wallet Activity</h6>
            </div>
            <div class="card-body p-0" style="max-height:450px;overflow-y:auto;">
                @forelse ($walletHistory as $tx)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom hover-bg-light transition-all">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm {{ $tx->type === 'credit' ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 border border-{{ $tx->type === 'credit' ? 'success' : 'danger' }} border-opacity-25" style="width:42px; height:42px;">
                            <i class="bi bi-{{ $tx->type === 'credit' ? 'arrow-down-left' : 'arrow-up-right' }} fs-5"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="small fw-bold text-dark">{{ Str::limit($tx->description, 35) }}</div>
                            <div class="text-muted" style="font-size:.70rem;"><i class="bi bi-clock me-1"></i>{{ $tx->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="fw-bold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->type === 'credit' ? '+' : '−' }}{{ config('plantix.currency_symbol') }}{{ number_format($tx->amount, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-dash-circle fs-3 d-block mb-2 opacity-50"></i>
                        <span class="small fw-medium">No wallet activity yet</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById('earningsChart');
    if(!canvas) return;
    
    // Resize chart container fixes
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    
    const ctx = canvas.getContext('2d');
    
    // Add gradient for bars
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(76, 175, 80, 0.8)'); // plantix green
    gradient.addColorStop(1, 'rgba(76, 175, 80, 0.2)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyChart->pluck('month')) !!},
            datasets: [{
                label: 'Earnings',
                data: {!! json_encode($monthlyChart->pluck('total')) !!},
                backgroundColor: gradient,
                borderColor: 'rgba(76, 175, 80, 1)',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#333',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: ctx => '{{ config('plantix.currency_symbol') }}' + parseFloat(ctx.parsed.y).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: v => '{{ config('plantix.currency_symbol') }}' + v.toLocaleString(),
                        font: { size: 11, family: "'Inter', sans-serif" },
                        color: '#888'
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        font: { size: 12, family: "'Inter', sans-serif", weight: '500' },
                        color: '#555'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
});
</script>
@endpush
