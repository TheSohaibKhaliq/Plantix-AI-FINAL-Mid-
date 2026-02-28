@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Edit Role: {{ $role->role_name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">{{ trans('lang.role_plural') }}</a></li>
                <li class="breadcrumb-item active">Edit Role</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Role</h4>
                        <span class="badge badge-{{ $role->is_active ? 'success' : 'secondary' }}">
                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('admin.role.update', $role->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Role Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="role_name"
                                           class="form-control @error('role_name') is-invalid @enderror"
                                           value="{{ old('role_name', $role->role_name) }}"
                                           placeholder="e.g. Store Manager" required>
                                    @error('role_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                               name="is_active" value="1"
                                               {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Permissions grouped by group --}}
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Permissions</label>
                                <div class="col-sm-9">
                                    <div class="mb-2">
                                        <a href="#" id="select-all-perms" class="btn btn-sm btn-outline-primary mr-1">Select All</a>
                                        <a href="#" id="deselect-all-perms" class="btn btn-sm btn-outline-secondary">Deselect All</a>
                                    </div>
                                    @foreach($permissions as $group => $groupPerms)
                                    <div class="card mb-2">
                                        <div class="card-header py-2 d-flex justify-content-between">
                                            <strong class="text-capitalize">{{ str_replace('-', ' ', $group) }}</strong>
                                            <a href="#" class="select-all-group text-sm" data-group="{{ $group }}">Select All</a>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row">
                                                @foreach($groupPerms as $perm)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox mb-1">
                                                        <input type="checkbox"
                                                               class="custom-control-input perm-checkbox perm-{{ $group }}"
                                                               id="perm_{{ $perm['id'] }}"
                                                               name="permissions[]"
                                                               value="{{ $perm['id'] }}"
                                                               {{ in_array($perm['id'], old('permissions', $assignedIds)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="perm_{{ $perm['id'] }}">
                                                            {{ $perm['display_name'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fa fa-save mr-1"></i> Update Role
                                    </button>
                                    <a href="{{ route('admin.role.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                {{-- Users with this role --}}
                @if(isset($usersCount) && $usersCount > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Users with this Role ({{ $usersCount }})</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            This role is currently assigned to <strong>{{ $usersCount }}</strong> user(s).
                            Changing permissions will affect all of them on next login.
                        </p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts_bottom')
<script>
$(document).on('click', '.select-all-group', function(e) {
    e.preventDefault();
    const group = $(this).data('group');
    const boxes = $('.perm-' + group);
    const allChecked = boxes.filter(':checked').length === boxes.length;
    boxes.prop('checked', !allChecked);
    $(this).text(allChecked ? 'Select All' : 'Deselect All');
});

$('#select-all-perms').on('click', function(e) {
    e.preventDefault();
    $('.perm-checkbox').prop('checked', true);
});

$('#deselect-all-perms').on('click', function(e) {
    e.preventDefault();
    $('.perm-checkbox').prop('checked', false);
});
</script>
@endpush
