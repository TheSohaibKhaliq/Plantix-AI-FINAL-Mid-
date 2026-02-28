@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-user-circle me-2 text-success"></i>{{trans('lang.user_profile')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.users') !!}">{{trans('lang.admin_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.user_edit')}}</li>
            </ol>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-info-circle me-2 text-primary"></i>Profile Details</h5>
                    <span class="badge bg-light text-dark border">{{ $user->email }}</span>
                </div>
                <div class="card-body p-4">
                    @if (Session::has('message'))
                    <div class="alert alert-danger error_top rounded border-0 shadow-sm"><p class="mb-0">{{Session::get('message')}}</p></div>
                    @endif

                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                    Processing...</div>

                <form method="post" action="{{ route('admin.admin.users.update',$user->id) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.user_name')}}</label>
                        <input type="text" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25" name="name"
                            value="{{ $user->name }}">
                        <div class="form-text text-muted small mt-1">
                            <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_name_help") }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.user_email')}}</label>
                        <input type="email" class="form-control form-control-lg rounded-3 border-secondary border-opacity-25"
                            value="{{ $user->email }}" name="email">
                        <div class="form-text text-muted small mt-1">
                            <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_email_help") }}
                        </div>
                    </div>

                    <div class="card bg-light border-0 rounded-3 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fa fa-lock me-2 text-warning"></i>Change Password (Optional)</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-medium text-dark small">{{trans('lang.old_password')}}</label>
                                    <input type="password" class="form-control rounded border-secondary border-opacity-25" name="old_password">
                                    <div class="form-text text-muted small">{{ trans("lang.old_password_help") }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark small">{{trans('lang.new_password')}}</label>
                                    <input type="password" class="form-control rounded border-secondary border-opacity-25" name="password">
                                    <div class="form-text text-muted small">{{ trans("lang.user_password_help") }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark small">{{trans('lang.confirm_password')}}</label>
                                    <input type="password" class="form-control rounded border-secondary border-opacity-25" name="confirm_password">
                                    <div class="form-text text-muted small">{{ trans("lang.confirm_password_help") }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->id != 1)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">{{trans('lang.role')}}</label>
                        <select class="form-select form-select-lg rounded-3 border-secondary border-opacity-25" name="role" >
                            @foreach($roles as $role)
                            <option value="{{$role->id}}" {{($user->role_id==$role->id) ? "selected" :""}}>{{$role->role_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif 

                    <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                        <a href="{!! route('admin.users') !!}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                            <i class="fa fa-undo me-2"></i>{{ trans('lang.cancel')}}
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" id="save_user_btn">
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