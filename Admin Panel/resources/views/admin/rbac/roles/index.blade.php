@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.role_plural') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.role_plural') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ trans('lang.role_table') }}</h4>
                        @can('admin', auth('admin')->user())
                        <a href="{{ route('admin.role.save') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus mr-1"></i> {{ trans('lang.create_role') }}
                        </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="roleTable" class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('lang.role_name') }}</th>
                                        <th>Permissions</th>
                                        <th>Status</th>
                                        <th>{{ trans('lang.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $role->role_name }}</strong>
                                            @if($role->guard === 'admin')
                                                <span class="badge badge-info ml-1">Admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $role->permissions_count ?? 0 }} permission(s)
                                            </span>
                                        </td>
                                        <td>
                                            @if($role->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.role.edit', $role->id) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.role.delete', $role->id) }}"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this role? All assigned users will lose their role.')"
                                               title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No roles found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick link to permission management --}}
        <div class="row mt-3">
            <div class="col-12">
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-key mr-1"></i> Manage Permissions
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts_bottom')
<script>
    $(document).ready(function () {
        $('#roleTable').DataTable({ responsive: true, order: [[0, 'asc']] });
    });
</script>
@endpush
