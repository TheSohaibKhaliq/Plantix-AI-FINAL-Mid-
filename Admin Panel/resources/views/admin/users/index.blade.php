@extends('layouts.app')

@section('content')

<div class="page-wrapper">


    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-users text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px; width: 44px; text-align: center;"></i>
                {{trans('lang.admin_plural')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.admin_plural')}}</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid pl-0 pr-0">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{trans('lang.admin_table')}}</h4>
                            <p class="text-muted small mt-1">Manage administrative users and their permissions.</p>
                        </div>
                        <a href="{!! route('admin.users.create') !!}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                            <i class="fa fa-plus me-2"></i>{{trans('lang.create_admin')}}
                        </a>
                    </div>
                    <div class="card-body p-5">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">Processing...</div>
                        <div class="table-responsive">
                            <table id="adminTable" class="table table-hover align-middle mb-0" cellspacing="0" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <?php if (in_array('admin.users.delete', json_decode(@session('admin_permissions'),true))) { ?>
                                        <th class="delete-all ps-4" style="width: 50px;">
                                            <div class="form-check m-0">
                                                <input type="checkbox" id="is_active" class="form-check-input">
                                                <label class="form-check-label d-none" for="is_active"></label>
                                            </div>
                                            <a id="deleteAll" class="do_not_delete text-danger small mt-1 d-block" href="javascript:void(0)" style="font-size: 0.70rem; text-decoration: none;">
                                                <i class="fa fa-trash"></i> {{trans('lang.all')}}
                                            </a>
                                        </th>
                                        <?php } ?>
                                        <th class="fw-medium text-muted text-uppercase small">{{trans('lang.name')}}</th>
                                        <th class="fw-medium text-muted text-uppercase small">{{trans('lang.email')}}</th>
                                        <th class="fw-medium text-muted text-uppercase small">{{trans('lang.role')}}</th>
                                        <th class="text-end pe-4 fw-medium text-muted text-uppercase small">{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list1">
                                    @foreach($users as $user)
                                    <tr class="hover-card-row">
                                        <?php if (in_array('admin.users.delete', json_decode(@session('admin_permissions'),true))) { ?>
                                        <td class="delete-all ps-4">
                                            <div class="form-check m-0">
                                                <input type="checkbox" id="is_open_{{$user->id}}" class="is_open form-check-input custom-checkbox" dataid="{{$user->id}}">
                                            </div>
                                        </td>
                                        <?php } ?>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <a href="{{route('admin.users.edit', ['id' => $user->id])}}" class="text-decoration-none fw-bold text-dark fs-6 d-block mb-0">
                                                        {{ $user->name }}
                                                    </a>
                                                    <span class="text-muted small">Admin User</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="fa fa-envelope-o me-2 text-success opacity-75"></i>
                                                {{ $user->email }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-success rounded-pill px-3 py-2 fw-semibold border shadow-sm small">
                                                <i class="fa fa-shield me-1"></i> {{ $user->roleName }}
                                            </span>
                                        </td>
                                        <td class="action-btn text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{route('admin.users.edit', ['id' => $user->id])}}" class="btn btn-sm btn-light text-success rounded-circle shadow-sm hover-card d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                 @if($user->id != 1)
                                                <?php if (in_array('admin.admin.users.delete', json_decode(@session('admin_permissions'),true))) { ?>
                                                <a href="{{route('admin.admin.users.delete', ['id' => $user->id])}}" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm hover-card d-flex align-items-center justify-content-center delete-btn" style="width: 38px; height: 38px;" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                 <?php } ?>     
                                                 @endif  
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">

    var user_permissions = '<?php echo @session("admin_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('admin.users.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

    if (checkDeletePermission) {
        $('#adminTable').DataTable({
            order: [],
            columnDefs: [
                { orderable: false, targets: [0, 4] },

            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}"
                        },
            responsive: true
        });
    }
    else
    {
        $('#adminTable').DataTable({
            order: [],
            columnDefs: [
                { orderable: false, targets: [3] },

            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}"
                        },
            responsive: true
        });
    }

    $("#is_active").click(function () {
        $("#adminTable .is_open").prop('checked', $(this).prop('checked'));

    });

    $("#deleteAll").click(function () {
        if ($('#adminTable .is_open:checked').length) {
            if (confirm('Are You Sure want to Delete Selected Data ?')) {
                var arrayUsers = [];
                $('#adminTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    arrayUsers.push(dataId);

                });

                arrayUsers = JSON.stringify(arrayUsers);
                var url = "{{url('admin-users/delete', 'id')}}";
                url = url.replace('id', arrayUsers);

                $(this).attr('href', url);
            }
        } else {
            alert('Please Select Any One Record .');
        }
    });
    
</script>


@endsection