@extends('layouts.app')

@section('title', 'Seasonal Data')

@push('styles')
<style>
#modalForm .form-label { font-size: .85rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <a href="{{ route('admin.ai.dashboard') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 14px; font-weight: 600;">AI Agriculture</a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <span style="color: var(--agri-primary); font-size: 14px; font-weight: 600;">Seasonal Data</span>
            </div>
            <h2 class="h4 mb-0" style="font-weight: 700; color: var(--agri-primary-dark);"><i class="fas fa-database me-2 text-secondary"></i>Seasonal Crop Data</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.ai.dashboard') }}" class="btn-agri btn-agri-outline" style="text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 700; padding: 10px 20px;">
                <i class="fas fa-arrow-left"></i> AI Dashboard
            </a>
            <button class="btn-agri btn-agri-primary" style="display: flex; align-items: center; gap: 10px; font-weight: 700; padding: 10px 20px; border: none; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Entry
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-agri" style="padding: 0; background: white; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); overflow: hidden;">
        <div class="table-responsive">
            <table class="table mb-0" style="vertical-align: middle;">
                <thead style="background: var(--agri-bg);">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">#</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Crop</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Season</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Sowing</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Harvest</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Water Needs</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Yield (kg/acre)</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($seasonalData as $item)
                    <tr style="border-bottom: 1px solid var(--agri-border);">
                        <td style="padding: 16px 24px; font-weight: 600;">{{ $item->id }}</td>
                        <td style="padding: 16px 24px; font-weight: 700; color: var(--agri-text-heading);">{{ $item->crop_name }}</td>
                        <td style="padding: 16px 24px;">
                            @php
                                $seasonColors = [
                                    'Rabi' => ['bg' => '#DBEAFE', 'text' => '#1D4ED8', 'border' => '#BFDBFE'],
                                    'Kharif' => ['bg' => '#FEF3C7', 'text' => '#B45309', 'border' => '#FDE68A'],
                                ];
                                $sColor = $seasonColors[$item->season] ?? ['bg' => '#D1FAE5', 'text' => '#065F46', 'border' => '#A7F3D0'];
                            @endphp
                            <span style="background: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }}; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 900; text-transform: uppercase;">
                                {{ $item->season }}
                            </span>
                        </td>
                        <td style="padding: 16px 24px; font-weight: 600;">{{ $item->sowing_month }}</td>
                        <td style="padding: 16px 24px; font-weight: 600;">{{ $item->harvest_month }}</td>
                        <td style="padding: 16px 24px; color: var(--agri-text-muted);">{{ ucfirst(str_replace('_', ' ', $item->water_needs ?? '')) }}</td>
                        <td style="padding: 16px 24px; font-weight: 700;">{{ number_format($item->avg_yield_kg_acre ?? 0) }}</td>
                        <td style="padding: 16px 24px;" class="text-end">
                            <button class="btn-agri btn-agri-outline" style="padding: 6px 10px; font-size: 13px; border: 1px solid var(--agri-border); background: white; border-radius: 8px; cursor: pointer; color: var(--agri-text-heading);"
                                    onclick="editRow({{ $item->id }}, @json($item))">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.ai.seasonal-data.destroy', $item->id) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button class="btn-agri" style="padding: 8px 10px; font-size: 13px; border: 1px solid #FECACA; background: #FEF2F2; color: #DC2626; border-radius: 8px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No seasonal data. <a href="#" data-bs-toggle="modal" data-bs-target="#addModal" style="color: var(--agri-primary); font-weight: 700; text-decoration: none;">Add one.</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Add Modal --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalForm" style="border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden;">
            <form method="POST" id="seasonalForm" action="{{ route('admin.ai.seasonal-data.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header" style="background: var(--agri-bg); border-bottom: 1px solid var(--agri-border); padding: 24px;">
                    <h5 class="modal-title" id="modalTitle" style="font-weight: 800; color: var(--agri-primary-dark); font-size: 18px;">Add Seasonal Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 14px;"></button>
                </div>
                <div class="modal-body" style="padding: 32px;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Crop Name</label>
                            <input type="text" name="crop_name" class="form-control" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Season</label>
                            <select name="season" class="form-select" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" required>
                                <option value="Rabi">Rabi</option>
                                <option value="Kharif">Kharif</option>
                                <option value="Zaid">Zaid</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Sowing Month</label>
                            <input type="text" name="sowing_month" class="form-control" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" placeholder="e.g. October" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Harvest Month</label>
                            <input type="text" name="harvest_month" class="form-control" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" placeholder="e.g. April" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Water Needs</label>
                            <select name="water_needs" class="form-select" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="very_high">Very High</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Avg Yield (kg/acre)</label>
                            <input type="number" name="avg_yield_kg_acre" class="form-control" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted text-uppercase fw-bold" style="font-size: 12px; letter-spacing: 0.5px;">Notes</label>
                            <textarea name="notes" class="form-control" style="border: 1px solid var(--agri-border); border-radius: 10px; padding: 12px 16px; font-size: 14px;" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 24px 32px; border-top: 1px solid var(--agri-border); background: #F8FAFC;">
                    <button type="button" class="btn-agri btn-agri-outline" style="border: none; background: transparent; color: var(--agri-text-muted); font-weight: 700; padding: 10px 20px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-agri btn-agri-primary" style="padding: 10px 24px; font-weight: 700; border: none; cursor: pointer;">Save Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editRow(id, data) {
    const form = document.getElementById('seasonalForm');
    form.action = `/admin/ai-modules/seasonal-data/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Edit Seasonal Data';

    ['crop_name','season','sowing_month','harvest_month','water_needs','avg_yield_kg_acre','notes'].forEach(f => {
        const el = form.querySelector(`[name="${f}"]`);
        if (el) el.value = data[f] ?? '';
    });
    new bootstrap.Modal(document.getElementById('addModal')).show();
}
</script>
@endpush
