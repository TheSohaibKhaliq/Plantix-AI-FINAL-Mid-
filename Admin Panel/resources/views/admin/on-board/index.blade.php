@extends('layouts.app')

@section('content')

    <div class="page-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.on_board_plural')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.on_board_table')}}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                 style="display: none;">{{trans('lang.processing')}}
                            </div>


                            <div class="table-responsive m-t-10">
                                <table id="userTable"
                                       class="display  table table-hover table-striped table-bordered table table-striped"
                                       cellspacing="0" width="100%">
                                    <thead>
                                    <tr>

                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.description')}}</th>
                                        <th>{{trans('lang.app_screen')}}</th>
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

@endsection

@section('scripts')


    <script type="text/javascript">

        var database = firebase.firestore();

        var offest = 1;
        var pagesize = 10;
        var end = null;
        var endarray = [];
        var start = null;
        var user_number = [];

        var ref = database.collection('on_boarding');

        var append_list = '';

        $(document).ready(function () {

            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });

            var inx = parseInt(offest) * parseInt(pagesize);
            jQuery("#overlay").show();

            append_list = document.getElementById('append_list1');
            append_list.innerHTML = '';
            ref.get().then(async function (snapshots) {

                html = '';
                if (snapshots.docs.length > 0) {
                    html = await buildHTML(snapshots);
                }

                jQuery("#overlay").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);

                    if (snapshots.docs.length < pagesize) {
                        jQuery("#data-table_paginate").hide();
                    }

                }
                $('#userTable').DataTable({
                    order: [[1, 'asc']],
                    columnDefs: [
                        {orderable: false, targets: [3]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    },
                    "bPaginate": false
                });
            });

        });


        async function buildHTML(snapshots) {

            await Promise.all(snapshots.docs.map(async (listval) => {
                var val = listval.data();
                var getData = await getListData(val);
                html += getData;

            }));
            return html;
        }

        function getListData(val) {
            var html = '';
            html = html + '<tr>';
            newdate = '';
            var id = val.id;
            var route1 = '{{route("on-board.save",":id")}}';
            route1 = route1.replace(':id', id);

            html = html + '<td><a href="' + route1 + '">' + val.title + '</a></td>';

            html = html + '<td>' + val.description + '</td>';

            var type = '';
            if (val.type == "customerApp") {
                type = "Customer";
            } else if (val.type == "driverApp") {
                type = "Driver";
            } else if (val.type == "restaurantApp") {
                type = "Store";
            }
            html = html + '<td>' + type + '</td>';

            html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a></td>';

            html = html + '</tr>';
            return html;
        }


    </script>

@endsection
