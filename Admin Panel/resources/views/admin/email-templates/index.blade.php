@extends('layouts.app')

@section('content')

    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">{{trans('lang.email_templates')}}</h3>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                    <li class="breadcrumb-item">{{trans('lang.email_templates')}}</li>

                </ol>

            </div>

        </div>


        <div class="container-fluid">

            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item">
                                    <a class="nav-link active" href="{!! url()->current() !!}"><i
                                                class="fa fa-list mr-2"></i>{{trans('lang.email_templates_table')}}</a>
                                </li>
                               {{-- <li class="nav-item">
                                    <a class="nav-link" href="{!! url('email-templates/save') !!}"><i
                                                class="fa fa-plus mr-2"></i>{{trans('lang.create_email_templates')}}</a>
                                </li>--}}

                            </ul>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive m-t-10">


                                <table id="emailTemplatesTable"
                                       class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                       cellspacing="0" width="100%">

                                    <thead>

                                    <tr>

                                        <th>{{trans('lang.type')}}</th>
                                        <th>{{trans('lang.subject')}}</th>
                                        <th>{{trans('lang.actions')}}</th>

                                    </tr>

                                    </thead>

                                    <tbody id="emailTemplatesTbody">


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
        var refData = database.collection('email_templates').orderBy('createdAt', 'desc');
        var append_list = '';

        $(document).ready(function () {

            jQuery("#data-table_processing").show();

            const table = $('#emailTemplatesTable').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: function (data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['type','subject']; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table

                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }

                    refData.get().then(async function (querySnapshot) {
                        if (querySnapshot.empty) {
                            console.error("No data found in Firestore.");
                            $('#data-table_processing').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: [] // No data
                            });
                            return;
                        }

                        let records = [];
                        let filteredRecords = [];

                        await Promise.all(querySnapshot.docs.map(async (doc) => {
                            let childData = doc.data();
                            childData.id = doc.id; // Ensure the document ID is included in the data
                            
                            if (searchValue) {
                                if (
                                    (childData.type && childData.type.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.subject && childData.subject.toString().toLowerCase().includes(searchValue))
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));

                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';

                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });

                        const totalRecords = filteredRecords.length;

                        const paginatedRecords = filteredRecords.slice(start, start + length);

                        paginatedRecords.forEach(function (childData) {

                            var route1 = '{{route("email-templates.save",":id")}}';
                            route1 = route1.replace(":id", childData.id);
                            var type = '';

                            if (childData.type == "new_order_placed") {
                                type = "{{trans('lang.new_order_placed')}}";

                            } else if (childData.type == "new_vendor_signup") {
                                type = "{{trans('lang.new_vendor_signup')}}";
                            } else if (childData.type == "payout_request") {
                                type = "{{trans('lang.payout_request')}}";
                            } else if (childData.type == "payout_request_status") {
                                type = "{{trans('lang.payout_request_status')}}";

                            } else if (childData.type == "wallet_topup") {
                                type = "{{trans('lang.wallet_topup')}}";
                            }
                            records.push([
                                type,
                                childData.subject,
                                '<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a></span>'
                            ]);
                        });

                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords, // Total number of records in Firestore
                            recordsFiltered: totalRecords, // Number of records after filtering (if any)
                            data: records // The actual data to display in the table
                        });
                    }).catch(function (error) {
                        console.error("Error fetching data from Firestore:", error);
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data due to error
                        });
                    });
                },
                order: [0,'asc'],
                columnDefs: [
                    {orderable: false, targets: [2]},
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}",
                    "processing": "",
                },

            });

            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            $('#search-input').on('input', debounce(function () {
                const searchValue = $(this).val();
                if (searchValue.length >= 3) {
                    $('#data-table_processing').show();
                    table.search(searchValue).draw();
                } else if (searchValue.length === 0) {
                    $('#data-table_processing').show();
                    table.search('').draw();
                }
            }, 300));

        });

        $("#is_active").click(function () {
            $("#emailTemplatesTable .is_open").prop('checked', $(this).prop('checked'));
        });

        $("#deleteAll").click(function () {
            if ($('#emailTemplatesTable .is_open:checked').length) {
                if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                    jQuery("#data-table_processing").show();
                    $('#emailTemplatesTable .is_open:checked').each(function () {
                        var dataId = $(this).attr('dataId');

                        database.collection('email_templates').doc(dataId).delete().then(function () {

                            window.location.reload();
                        });

                    });

                }
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });


        function buildHTML(snapshots) {

            var html = '';
            var number = [];
            var count = 0;
            snapshots.docs.forEach(async (listval) => {
                var listval = listval.data();

                var data = listval;
                data.id = listval.id;
                html = html + '<tr>';
                newdate = '';
                var id = data.id;
                var route1 = '{{route("email-templates.save",":id")}}';
                route1 = route1.replace(":id", id);

                var type = '';

                if (data.type == "new_order_placed") {
                    type = "{{trans('lang.new_order_placed')}}";

                } else if (data.type == "new_vendor_signup") {
                    type = "{{trans('lang.new_vendor_signup')}}";
                } else if (data.type == "payout_request") {
                    type = "{{trans('lang.payout_request')}}";
                } else if (data.type == "payout_request_status") {
                    type = "{{trans('lang.payout_request_status')}}";

                } else if (data.type == "wallet_topup") {
                    type = "{{trans('lang.wallet_topup')}}";
                }

                html = html + '<td>' + type + '</td>';
                html = html + '<td>' + data.subject + '</td>';

                html = html + '<td class="action-btn">' +
                    '<a href="' + route1 + '"><i class="fa fa-edit"></i></a></td>';

                html = html + '</tr>';
                count = count + 1;
            });
            return html;
        }

        $(document).on("click", "a[name='notifications-delete']", function (e) {
            var id = this.id;
            database.collection('email_templates').doc(id).delete().then(function () {
                window.location.reload();
            });
        });
    </script>


@endsection
