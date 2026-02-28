@extends('layouts.app')

@section('content')

<div class="page-wrapper">


    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor restaurantTitle">{{trans('lang.admin_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.admin_plural')}}</li>
            </ol>

        </div>

        <div>

        </div>

    </div>


    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm hover-card" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <ul class="nav nav-tabs card-header-tabs border-bottom-0 fw-semibold m-0">
                            <li class="nav-item">
                                <a class="nav-link active text-success border-success border-bottom border-2 bg-transparent" href="{!! url()->current() !!}">
                                    <i class="fa fa-list me-2"></i>{{trans('lang.admin_table')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-muted" href="{!! route('admin.users.create') !!}">
                                    <i class="fa fa-plus me-2"></i>{{trans('lang.create_admin')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
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
                                    <tr>
                                        <?php if (in_array('admin.users.delete', json_decode(@session('admin_permissions'),true))) { ?>
                                        <td class="delete-all ps-4">
                                            <div class="form-check m-0">
                                                <input type="checkbox" id="is_open_{{$user->id}}" class="is_open form-check-input" dataid="{{$user->id}}">
                                            </div>
                                        </td>
                                        <?php } ?>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <a href="{{route('admin.users.edit', ['id' => $user->id])}}" class="text-decoration-none fw-bold text-dark hover-text-primary">
                                                    {{ $user->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small"><i class="fa fa-envelope-o me-1"></i>{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 shadow-sm fs-6">
                                                {{ $user->roleName }}
                                            </span>
                                        </td>
                                        <td class="action-btn text-end pe-4">
                                            <a href="{{route('admin.users.edit', ['id' => $user->id])}}" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm me-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                             @if($user->id != 1)
                                            <?php if (in_array('admin.admin.users.delete', json_decode(@session('admin_permissions'),true))) { ?>
                                            <a href="{{route('admin.admin.users.delete', ['id' => $user->id])}}" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm delete-btn">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                             <?php } ?>     
                                             @endif  
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