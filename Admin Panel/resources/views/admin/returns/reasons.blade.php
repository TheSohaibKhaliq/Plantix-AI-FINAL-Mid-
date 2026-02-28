@extends('layouts.app')

@section('title', 'Return Reasons')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="h4 mb-0 d-inline-block"><i class="bi bi-card-list me-2 text-secondary"></i>Return Reasons</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Add Reason Form --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>Add New Reason
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.returns.reasons.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Reason Text</label>
                            <input type="text" name="reason"
                                   class="form-control @error('reason') is-invalid @enderror"
                                   placeholder="e.g. Product damaged on arrival"
                                   value="{{ old('reason') }}" required>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus me-1"></i>Add Reason
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing Reasons --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul me-2"></i>Existing Reasons</span>
                    <span class="badge bg-secondary">{{ $reasons->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reasons as $reason)
                                <tr>
                                    <td class="text-muted small">{{ $reason->id }}</td>
                                    <td>{{ $reason->reason }}</td>
                                    <td>
                                        @if($reason->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $reason->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="{{ route('admin.returns.reasons.destroy', $reason->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this return reason?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        No return reasons defined yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
