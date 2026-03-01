@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-tags text-success me-2"></i> Forum Categories</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        <div class="row g-4">

            {{-- Create Category Form --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0"><i class="fa fa-plus me-2 text-success"></i>Add Category</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.forum.categories.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control border-0 bg-light rounded-pill"
                                       placeholder="e.g. Pest Control" required maxlength="100"
                                       value="{{ old('name') }}">
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Description</label>
                                <textarea name="description" class="form-control border-0 bg-light" rows="3"
                                          placeholder="Optional description…" style="border-radius:12px;">{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Slug (auto-generated if blank)</label>
                                <input type="text" name="slug" class="form-control border-0 bg-light rounded-pill"
                                       placeholder="pest-control" value="{{ old('slug') }}">
                            </div>
                            <button type="submit" class="btn btn-success w-100 rounded-pill">
                                <i class="fa fa-plus me-1"></i> Create Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Categories List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">All Categories</h6>
                        <span class="badge bg-success">{{ $categories->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($categories->isEmpty())
                            <div class="py-5 text-center text-muted">No categories yet. Create one!</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Threads</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $cat)
                                        <tr>
                                            <td class="text-muted small">{{ $cat->id }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.forum.categories.update', $cat->id) }}"
                                                      class="d-flex gap-2 align-items-center" id="edit-cat-{{ $cat->id }}">
                                                    @csrf @method('PUT')
                                                    <input type="text" name="name" class="form-control form-control-sm border-0 bg-light rounded-pill"
                                                           value="{{ $cat->name }}" style="max-width:180px;">
                                                </form>
                                            </td>
                                            <td class="text-muted small">{{ $cat->slug }}</td>
                                            <td class="text-center">{{ $cat->threads_count ?? 0 }}</td>
                                            <td>
                                                <button type="submit" form="edit-cat-{{ $cat->id }}"
                                                        class="btn btn-xs btn-outline-primary me-1">
                                                    <i class="fa fa-save"></i> Save
                                                </button>
                                                <form method="POST" action="{{ route('admin.forum.categories.destroy', $cat->id) }}"
                                                      class="d-inline" onsubmit="return confirm('Delete category?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
