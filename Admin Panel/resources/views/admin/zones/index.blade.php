@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-map-marker text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px; width: 44px; text-align: center;"></i>
                {{trans('lang.zone_plural')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.zone_plural')}}</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{trans('lang.zone_table')}}</h4>
                            <p class="text-muted small mt-1">Manage and view all delivery zones.</p>
                        </div>
                        <a href="{!! route('admin.zone.create') !!}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                            <i class="fa fa-plus me-2"></i>{{trans('lang.zone_create')}}
                        </a>
                    </div>
                    <div class="card-body p-5">

                        <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">

                        <table id="example24"
                               class="display nowrap table table-hover table-striped table-bordered table table-striped"
                               cellspacing="0" width="100%">

                            <thead>

                            <tr>
                                <?php if (in_array('zone.delete', json_decode(@session('admin_permissions'),true))) { ?>

                                    <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                class="col-3 control-label" for="is_active">
                                            <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                        class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>

                                <?php } ?>
                                <th>{{trans('lang.zone_name')}}</th>

                                <th>{{trans('lang.status')}}</th>

                                <th>{{trans('lang.actions')}}</th>

                            </tr>

                            </thead>

                            <tbody id="append_list1">


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
    // Firebase has been removed from the application
    // Zone management functionality requires backend migration to MySQL
    
    $(document).ready(function () {
    });

</script>

@endsection
