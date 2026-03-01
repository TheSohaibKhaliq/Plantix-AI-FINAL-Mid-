@extends('layouts.app')

@section('title', 'Crop Recommendations')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <a href="{{ route('admin.ai.dashboard') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 14px; font-weight: 600;">AI Agriculture</a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <span style="color: var(--agri-primary); font-size: 14px; font-weight: 600;">Crop Recommendations</span>
            </div>
            <h2 class="h4 mb-0" style="font-weight: 700; color: var(--agri-primary-dark);"><i class="fas fa-seedling me-2 text-primary"></i>Crop Recommendations</h2>
        </div>
        <a href="{{ route('admin.ai.dashboard') }}" class="btn-agri btn-agri-outline" style="text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 700; padding: 10px 20px;">
            <i class="fas fa-arrow-left"></i> AI Dashboard
        </a>
    </div>

    <div class="card-agri" style="padding: 0; background: white; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); overflow: hidden;">
        <div class="table-responsive">
            <table class="table mb-0" style="vertical-align: middle;">
                <thead style="background: var(--agri-bg);">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">#</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">User</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Top Crop</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Crops Count</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Soil Source</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;">Date</th>
                        <th style="padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--agri-text-muted); text-transform: uppercase; letter-spacing: 1px; border: none;" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recommendations as $rec)
                    <tr style="border-bottom: 1px solid var(--agri-border);">
                        <td style="padding: 16px 24px; font-weight: 600;">{{ $rec->id }}</td>
                        <td style="padding: 16px 24px; font-weight: 700; color: var(--agri-primary);">{{ $rec->user->name ?? '—' }}</td>
                        <td style="padding: 16px 24px;"><span class="badge bg-success" style="padding: 6px 12px; border-radius: 8px; font-weight: 800;">{{ $rec->top_crop }}</span></td>
                        <td style="padding: 16px 24px; font-weight: 600;">{{ count($rec->recommended_crops ?? []) }}</td>
                        <td style="padding: 16px 24px; color: var(--agri-text-muted);">{{ $rec->soil_test_id ? 'Lab Test #'.$rec->soil_test_id : 'Manual Input' }}</td>
                        <td style="padding: 16px 24px; color: var(--agri-text-muted); font-size: 13px;">{{ $rec->created_at->format('d M Y') }}</td>
                        <td style="padding: 16px 24px;" class="text-end">
                            <a href="{{ route('admin.ai.crop-recommendations.show', $rec->id) }}" class="btn-agri btn-agri-primary" style="padding: 8px 16px; font-size: 12px; font-weight: 700; text-decoration: none;">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No recommendations yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($recommendations->hasPages())
        <div style="padding: 24px; border-top: 1px solid var(--agri-border); background: var(--agri-bg);">
            {{ $recommendations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
