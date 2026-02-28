@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding-top: 24px;">

    {{-- Breadcrumb/Header Section --}}
    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <a href="{{url('/dashboard')}}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 14px; font-weight: 600;">{{trans('lang.dashboard')}}</a>
            <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
            <span style="color: var(--agri-primary); font-size: 14px; font-weight: 600;">{{trans('lang.email_templates')}}</span>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: var(--agri-primary-dark); margin: 0;">System Notifications</h1>
        <p style="color: var(--agri-text-muted); margin: 4px 0 0 0;">Manage communication templates sent to farmers and vendors.</p>
    </div>

    {{-- Table Card --}}
    <div class="card-agri" style="padding: 0; overflow: hidden;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
            <div style="display: flex; align-items: center; gap: 16px;">
                 <h4 class="mb-0 fw-bold text-dark" style="font-size: 18px;">Email Templates</h4>
                 <div id="data-table_processing" class="spinner-border spinner-border-sm text-primary" role="status" style="display: none;"></div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: var(--agri-border);">
                        <i class="fas fa-search" style="color: var(--agri-text-muted); font-size: 14px;"></i>
                    </span>
                    <input type="text" id="custom-search-input" class="form-agri border-start-0" placeholder="Search templates..." style="margin-bottom: 0; border-radius: 0 10px 10px 0; height: 42px;">
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="emailTemplatesTable" class="table mb-0" style="vertical-align: middle;">
                <thead style="background: var(--agri-bg);">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: var(--agri-text-muted); text-transform: uppercase; border: none;">{{trans('lang.type')}}</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: var(--agri-text-muted); text-transform: uppercase; border: none;">{{trans('lang.subject')}}</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: var(--agri-text-muted); text-transform: uppercase; border: none;" class="text-end">{{trans('lang.actions')}}</th>
                    </tr>
                </thead>
                <tbody id="emailTemplatesTbody">
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-top-0 py-4 px-4">
        </div>
    </div>
</div>
@endsection
<style>
    /* DataTable Overrides */
    #emailTemplatesTable tbody tr:hover { background-color: rgba(var(--agri-primary-rgb), 0.02); }
    #emailTemplatesTable tbody td { border-bottom: 1px solid var(--agri-border); padding: 16px 24px; font-size: 14px; font-weight: 500;}
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: 1px solid var(--agri-border) !important; margin: 0 2px; padding: 6px 14px !important; font-weight: 600; font-size: 13px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--agri-primary) !important; color: white !important; border-color: var(--agri-primary) !important; }
    .dataTables_wrapper .dataTables_info { color: var(--agri-text-muted) !important; font-size: 13px; font-weight: 500; }
    .dataTables_filter { display: none; } /* Hide default search */
</style>

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

                            var route1 = '{{route("admin.email-templates.save",":id")}}';
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
                                '<div style="font-weight:700; color:var(--agri-text-heading);">' + type + '</div>',
                                '<div style="font-size:14px; color:var(--agri-text-muted);">' + childData.subject + '</div>',
                                '<div class="text-end">' +
                                    '<a href="' + route1 + '" class="btn-agri" style="padding: 8px; background: var(--agri-bg); color: var(--agri-primary); border-radius: 10px;" title="Edit Template"><i class="fas fa-edit"></i></a>' +
                                '</div>'
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

            $('#custom-search-input').on('input', debounce(function () {
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
                var route1 = '{{route("admin.email-templates.save",":id")}}';
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
