@extends('vendor.layouts.app')
@section('title', 'Returns & Refunds')
@section('page-title', 'Returns & Refunds')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-return-left me-2 text-warning"></i>Return Requests</h5>
        {{-- Filter --}}
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Return Ref</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                        <th>Requested On</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                    <tr>
                        <td class="small text-muted">#{{ $ret->id }}</td>
                        <td class="fw-semibold small">{{ $ret->order->order_number ?? '—' }}</td>
                        <td>{{ $ret->user->name ?? '—' }}</td>
                        <td class="small text-muted">{{ Str::limit($ret->reason->reason ?? $ret->description ?? '—', 40) }}</td>
                        <td class="text-center">
                            @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','refunded'=>'info'][$ret->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}">{{ ucfirst($ret->status) }}</span>
                        </td>
                        <td class="small text-muted">{{ $ret->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('vendor.returns.show', $ret->id) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No return requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($returns->hasPages())
    <div class="card-footer bg-white border-0">{{ $returns->links() }}</div>
    @endif
</div>
@endsection
