@extends('vendor.layouts.app')
@section('title', 'Earnings & Payouts')
@section('page-title', 'Earnings & Payouts')

@section('content')

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

{{-- ── Stats Row ───────────────────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Earnings</div>
                    <div class="fs-4 fw-bold">
                        {{ config('plantix.currency_symbol') }}
                        {{ number_format($stats['totalEarnings'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar-month fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">This Month</div>
                    <div class="fs-4 fw-bold">
                        {{ config('plantix.currency_symbol') }}
                        {{ number_format($stats['monthEarnings'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Wallet Balance</div>
                    <div class="fs-4 fw-bold">
                        {{ config('plantix.currency_symbol') }}
                        {{ number_format($stats['walletBalance'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Payout</div>
                    <div class="fs-4 fw-bold">
                        {{ config('plantix.currency_symbol') }}
                        {{ number_format($stats['pendingPayout'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-arrow-up-right-circle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Paid Out</div>
                    <div class="fs-4 fw-bold">
                        {{ config('plantix.currency_symbol') }}
                        {{ number_format($stats['totalPaidOut'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    {{-- ── Monthly Earnings Chart ───────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Monthly Earnings (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Request Payout ───────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-send me-2 text-success"></i>Request Payout</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.payouts.request') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Amount ({{ config('plantix.currency_symbol') }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               placeholder="e.g. 5000.00" max="{{ $stats['walletBalance'] }}" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Available: <strong>{{ config('plantix.currency_symbol') }}{{ number_format($stats['walletBalance'], 2) }}</strong></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payout Method <span class="text-danger">*</span></label>
                        <select name="method" class="form-select @error('method') is-invalid @enderror" required>
                            <option value="">— Select method —</option>
                            <option value="bank"   {{ old('method') === 'bank'   ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal" {{ old('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="stripe" {{ old('method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="wallet" {{ old('method') === 'wallet' ? 'selected' : '' }}>Keep in Wallet</option>
                        </select>
                        @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bank / Payment Details</label>
                        <textarea name="bank_details" rows="2" class="form-control"
                                  placeholder="Account number, IBAN, PayPal email…">{{ old('bank_details') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Notes</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional note…">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-send me-1"></i>Submit Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Payout Request History ───────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2 text-primary"></i>Payout History</h6>
            </div>
            <div class="card-body p-0">
                @if ($payoutRequests->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>No payout requests yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payoutRequests as $pr)
                                    <tr>
                                        <td class="text-muted">{{ $pr->id }}</td>
                                        <td>{{ $pr->created_at->format('d M Y') }}</td>
                                        <td class="fw-semibold">
                                            {{ config('plantix.currency_symbol') }}{{ number_format($pr->amount, 2) }}
                                        </td>
                                        <td class="text-capitalize">{{ $pr->method ?? '—' }}</td>
                                        <td>
                                            @php
                                                $badgeMap = [
                                                    'pending'  => 'warning text-dark',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                $badge = $badgeMap[$pr->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badge }} text-capitalize">
                                                {{ $pr->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ Str::limit($pr->notes, 40) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $payoutRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Wallet Transactions ──────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-wallet me-2 text-info"></i>Recent Wallet Activity</h6>
            </div>
            <div class="card-body p-0" style="max-height:420px;overflow-y:auto;">
                @forelse ($walletHistory as $tx)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <div class="rounded-circle p-2 {{ $tx->type === 'credit' ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                            <i class="bi bi-{{ $tx->type === 'credit' ? 'arrow-down' : 'arrow-up' }} small
                               text-{{ $tx->type === 'credit' ? 'success' : 'danger' }}"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="small fw-semibold">{{ Str::limit($tx->description, 35) }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $tx->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="fw-bold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->type === 'credit' ? '+' : '−' }}{{ config('plantix.currency_symbol') }}{{ number_format($tx->amount, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No wallet activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('earningsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyChart->pluck('month')) !!},
        datasets: [{
            label: '{{ config('plantix.currency_symbol') }} Earnings',
            data: {!! json_encode($monthlyChart->pluck('total')) !!},
            backgroundColor: 'rgba(76, 175, 80, 0.6)',
            borderColor: 'rgba(76, 175, 80, 1)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '{{ config('plantix.currency_symbol') }} ' + parseFloat(ctx.parsed.y).toFixed(2)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => '{{ config('plantix.currency_symbol') }} ' + v.toLocaleString()
                }
            }
        }
    }
});
</script>
@endpush
