@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-user-plus me-2 text-success"></i>{{trans('lang.create_admin')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.users') !!}">{{trans('lang.admin_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.create_admin')}}</li>
            </ol>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-info-circle me-2 text-primary"></i>Profile Details</h5>
                </div>
                <div class="card-body p-4">
                    @if (Session::has('message'))
                    <div class="alert alert-danger error_top rounded border-0 shadow-sm"><p class="mb-0">{{Session::get('message')}}</p></div>
                    @endif

                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                    Processing...</div>

                <form method="post" action="{{ route('admin.admin.users.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.user_name')}}</label>
                        <input type="text" value="{{ old('name') }}" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" name="name" placeholder="Enter full name">
                        <div class="form-text text-muted small mt-1">
                            <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_name_help") }}
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{trans('lang.password')}}</label>
                            <input type="password" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" name="password" placeholder="Enter password">
                            <div class="form-text text-muted small mt-1">
                                <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_password_help") }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{trans('lang.confirm_password')}}</label>
                            <input type="password" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" name="confirm_password" placeholder="Confirm password">
                            <div class="form-text text-muted small mt-1">
                                <i class="fa fa-info-circle me-1"></i>{{ trans("lang.confirm_password_help") }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.user_email')}}</label>
                        <input type="email" value="{{ old('email') }}" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" name="email" placeholder="example@domain.com">
                        <div class="form-text text-muted small mt-1">
                            <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_email_help") }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.role')}}</label>
                        <select class="form-select form-select-lg rounded-3 border-secondary border-opacity-25" name="role">
                            <option value="" disabled selected>Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>{{$role->role_name}}</option> 
                            @endforeach  
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                        <a href="{!! route('admin.users') !!}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                            <i class="fa fa-undo me-2"></i>{{ trans('lang.cancel')}}
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="save_user_btn">
                            <i class="fa fa-save me-2"></i> {{ trans('lang.save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endsection

    @section('scripts')

    @endsection