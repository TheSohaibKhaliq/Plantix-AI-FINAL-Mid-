@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Permissions</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">{{ trans('lang.role_plural') }}</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row">
            {{-- Create Permission Card --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Add Permission</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.permission.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>Slug / Name <span class="text-danger">*</span>
                                    <small class="text-muted">(e.g. view-users)</small>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="view-users" required>
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Group <span class="text-danger">*</span>
                                    <small class="text-muted">(e.g. users)</small>
                                </label>
                                <input type="text" name="group"
                                       class="form-control @error('group') is-invalid @enderror"
                                       list="group-options"
                                       value="{{ old('group') }}"
                                       placeholder="users" required>
                                <datalist id="group-options">
                                    @foreach($groups as $g)
                                        <option value="{{ $g }}">
                                    @endforeach
                                </datalist>
                                @error('group')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Display Name <span class="text-danger">*</span></label>
                                <input type="text" name="display_name"
                                       class="form-control @error('display_name') is-invalid @enderror"
                                       value="{{ old('display_name') }}"
                                       placeholder="View Users" required>
                                @error('display_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-plus mr-1"></i> Add Permission
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Permissions Table --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">All Permissions ({{ $permissions->count() }})</h4>
                        <div>
                            <select id="filter-group" class="form-control form-control-sm" style="min-width:150px">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g }}">{{ ucfirst(str_replace('-', ' ', $g)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="permsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Slug</th>
                                        <th>Group</th>
                                        <th>Display Name</th>
                                        <th>Roles</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissions as $perm)
                                    <tr data-group="{{ $perm->group }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code>{{ $perm->name }}</code></td>
                                        <td><span class="badge badge-info">{{ $perm->group }}</span></td>
                                        <td>{{ $perm->display_name }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $perm->roles_count ?? 0 }} role(s)
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning edit-perm-btn"
                                                    data-id="{{ $perm->id }}"
                                                    data-name="{{ $perm->name }}"
                                                    data-group="{{ $perm->group }}"
                                                    data-display="{{ $perm->display_name }}"
                                                    title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <form method="POST"
                                                  action="{{ route('admin.permission.destroy', $perm->id) }}"
                                                  class="d-inline delete-perm-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Delete"
                                                        onclick="return confirm('Delete permission \'{{ $perm->name }}\'? This will remove it from all roles.')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No permissions found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Permission Modal --}}
<div class="modal fade" id="editPermModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" id="editPermForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Slug / Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Group <span class="text-danger">*</span></label>
                        <input type="text" name="group" id="edit_group" class="form-control"
                               list="group-options" required>
                    </div>
                    <div class="form-group">
                        <label>Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="edit_display_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts_bottom')
<script>
// Group filter
$('#filter-group').on('change', function() {
    const g = $(this).val();
    if (!g) { $('tbody tr').show(); return; }
    $('tbody tr').each(function() {
        $(this).toggle($(this).data('group') === g);
    });
});

// Edit modal
$(document).on('click', '.edit-perm-btn', function() {
    const id      = $(this).data('id');
    const name    = $(this).data('name');
    const group   = $(this).data('group');
    const display = $(this).data('display');
    const url     = '{{ route("admin.permission.update", ":id") }}'.replace(':id', id);

    $('#edit_name').val(name);
    $('#edit_group').val(group);
    $('#edit_display_name').val(display);
    $('#editPermForm').attr('action', url);
    $('#editPermModal').modal('show');
});
</script>
@endpush
