@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="h4 mb-0">Add New Product</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>

</div>
@endsection
