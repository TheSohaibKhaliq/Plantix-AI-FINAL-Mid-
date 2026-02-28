@extends('layouts.app')

@section('title', 'Edit Product: '.$product->name)

@section('content')
<div class="container-fluid">

    <div class="row page-titles border-bottom pb-3 mb-4">
        <div class="col-md-5 align-self-center d-flex align-items-center">
            <a href="{{ route('admin.products.index') }}" class="btn btn-light border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                <i class="fas fa-arrow-left text-muted"></i>
            </a>
            <h3 class="text-themecolor fw-bold mb-0">Edit Product: {{ $product->name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.products.index') !!}">Products</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
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
        <div class="mt-4 pb-5 d-flex justify-content-end gap-3">
            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                <i class="fa fa-undo me-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-save me-2"></i> Update Product
            </button>
        </div>
    </form>

</div>
@endsection
