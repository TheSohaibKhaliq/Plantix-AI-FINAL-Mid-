@extends('layouts.app')

@section('title', 'Edit Product: '.$product->name)

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="h4 mb-0">Edit Product: {{ $product->name }}</h2>
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

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form')
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update Product
            </button>
            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>

</div>
@endsection
