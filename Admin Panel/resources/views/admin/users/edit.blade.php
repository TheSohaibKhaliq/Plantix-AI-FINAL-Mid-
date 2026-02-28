@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-user-circle text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px; width: 44px; text-align: center;"></i>
                {{trans('lang.user_edit')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.users') !!}" class="text-muted text-decoration-none">{{trans('lang.admin_plural')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.user_edit')}}</li>
            </ol>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid pl-0 pr-0">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5 d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Profile Details</h4>
                            <p class="text-muted small mt-1">Update user account information and security settings.</p>
                        </div>
                        <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">
                            <i class="fa fa-envelope me-1"></i> {{ $user->email }}
                        </span>
                    </div>

                    <div class="card-body p-5">
                        @if (Session::has('message'))
                        <div class="alert alert-danger error_top rounded-4 border-0 shadow-sm mb-4"><p class="mb-0 fw-semibold">{{Session::get('message')}}</p></div>
                        @endif

                        <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">
                            Processing...</div>

                        <form method="post" action="{{ route('admin.admin.users.update', $user->id) }}" class="mt-2">
                            @csrf

                            <div class="row">
                                <!-- User Name -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.user_name')}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control px-4 py-3 border-0 bg-light rounded-4" name="name" value="{{ $user->name }}" placeholder="Full name">
                                    <div class="form-text text-muted mt-2">
                                        <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_name_help") }}
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.user_email')}} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control px-4 py-3 border-0 bg-light rounded-4" value="{{ $user->email }}" name="email" placeholder="Email address">
                                    <div class="form-text text-muted mt-2">
                                        <i class="fa fa-info-circle me-1"></i>{{ trans("lang.user_email_help") }}
                                    </div>
                                </div>

                                <!-- Password Update Section -->
                                <div class="col-md-12 mb-4">
                                    <div class="card bg-light border-0 rounded-4 shadow-sm border border-white">
                                        <div class="card-body p-4">
                                            <h6 class="fw-bold mb-4 text-dark"><i class="fa fa-lock me-2 text-warning"></i>Security Check <span class="text-muted small fw-normal">(Leave blank if not changing)</span></h6>
                                            <div class="row g-4">
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.old_password')}}</label>
                                                    <input type="password" class="form-control px-4 py-2 border-0 bg-white shadow-sm rounded-pill" name="old_password" placeholder="Current password">
                                                    <div class="form-text text-muted small mt-2 ms-2">{{ trans("lang.old_password_help") }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.new_password')}}</label>
                                                    <input type="password" class="form-control px-4 py-2 border-0 bg-white shadow-sm rounded-pill" name="password" placeholder="New password">
                                                    <div class="form-text text-muted small mt-2 ms-2">{{ trans("lang.user_password_help") }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.confirm_password')}}</label>
                                                    <input type="password" class="form-control px-4 py-2 border-0 bg-white shadow-sm rounded-pill" name="confirm_password" placeholder="Repeat new password">
                                                    <div class="form-text text-muted small mt-2 ms-2">{{ trans("lang.confirm_password_help") }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role Selection -->
                                @if($user->id != 1)
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.role')}} <span class="text-danger">*</span></label>
                                    <select class="form-select px-4 py-3 border-0 bg-light rounded-4" name="role" style="height: 52px; appearance: auto;">
                                        @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{($user->role_id==$role->id) ? "selected" :""}}>{{$role->role_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted mt-2">
                                        <i class="fa fa-shield me-1"></i>Assign specific permissions via user roles.
                                    </div>
                                </div>
                                @endif 
                            </div>

                            <!-- Footer Actions -->
                            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                <a href="{!! route('admin.users') !!}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                    <i class="fa fa-undo me-2"></i>{{ trans('lang.cancel')}}
                                </a>
                                <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold" id="save_user_btn">
                                    <i class="fa fa-save me-2"></i> {{ trans('lang.save')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @section('scripts')

    @endsection