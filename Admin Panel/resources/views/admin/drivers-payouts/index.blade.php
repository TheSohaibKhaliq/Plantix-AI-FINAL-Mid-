@extends('layouts.app')

@section('content')
<div class="page-wrapper">


    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor driverName">{{trans('lang.drivers_payout_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.drivers_payout_plural')}}</li>
            </ol>
        </div>

        <div>

        </div>

    </div>


    <div class="container-fluid">

        <div class="row">

            <div class="col-12">
                <?php if ($id != '') { ?>
                    <div class="menu-tab">

                        <ul>
                            <li>
                                <a href="{{route('drivers.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                            </li>
                            <li>
                                <a href="{{route('orders')}}?driverId={{$id}}">{{trans('lang.tab_orders')}}</a>
                            </li>
                            <li class="active">
                                <a href="{{route('driver.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>
                            </li>
                            <li>
                                <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                            </li>

                        </ul>

                    </div>
                <?php } ?>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.drivers_payout_table')}}</a>
                            </li>

                            <?php if ($id != '') { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('driver.payout.create',$id) !!}/"><i
                                            class="fa fa-plus mr-2"></i>{{trans('lang.drivers_payout_create')}}</a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('driversPayouts.create') !!}"><i
                                            class="fa fa-plus mr-2"></i>{{trans('lang.drivers_payout_create')}}</a>
                                </li>
                            <?php } ?>

                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">


                            <table id="driverPayoutTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>
                                        <th>{{ trans('lang.driver')}}</th>
                                        <th>{{trans('lang.paid_amount')}}</th>
                                        <th>{{trans('lang.drivers_payout_paid_date')}}</th>
                                        <th>{{trans('lang.drivers_payout_note')}}</th>
                                        <th>Admin {{trans('lang.drivers_payout_note')}}</th>
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

<script>
    var driver_id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    if (driver_id) {
        $('.menu-tab').show();
        getDriverName(driver_id);
        var refData = database.collection('driver_payouts').where('driverID', '==', driver_id).where('paymentStatus', '==', 'Success');
    } else {
        var refData = database.collection('driver_payouts').where('paymentStatus', '==', 'Success');
    }
    var ref = refData.orderBy('paidDate', 'desc');
    var append_list = '';

    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_digits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;

        if (currencyData.decimal_degits) {
            decimal_digits = currencyData.decimal_degits;
        }
    });

    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });

        jQuery("#data-table_processing").show();
     
        const table = $('#driverPayoutTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = (driver_id != '') ? ['amount', 'paidDate', 'note', 'adminNote'] : ['driverName', 'amount', 'paidDate', 'note', 'adminNote']; // Ensure this matches the actual column names
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }
                await ref.get().then(async function (querySnapshot) {
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
                        childData.driverName = await payoutDriverfunction(childData.driverID);
                        childData.id = doc.id; // Ensure the document ID is included in the data              
                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("paidDate")) {
                                try {
                                    date = childData.paidDate.toDate().toDateString();
                                    time = childData.paidDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var paidDate = date + ' ' + time;
                            if (
                                (childData.driverName && childData.driverName.toString().toLowerCase().includes(searchValue)) ||
                                (childData.amount && childData.amount.toString().includes(searchValue)) ||
                                (paidDate && paidDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                (childData.note && (childData.note).toLowerCase().includes(searchValue)) ||
                                (childData.adminNote && (childData.adminNote).toString().includes(searchValue))

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
                        if (orderByField === 'paidDate') {
                            try {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            } catch (err) {

                            }
                        }
                        if (orderByField === 'amount') {
                            aValue = a[orderByField] ? parseFloat(a[orderByField]) : 0;
                            bValue = b[orderByField] ? parseFloat(b[orderByField]) : 0;

                        }

                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });

                    const totalRecords = filteredRecords.length;

                    const paginatedRecords = filteredRecords.slice(start, start + length);

                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData = await buildHTML(childData);
                        records.push(getData);
                    }));

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
            order: [[2, 'desc']],
            columnDefs: [
                {
                    targets: 2,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },

        });

        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function (...args) {
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

    async function getDriverName(driver_id) {
        var usersnapshots = await database.collection('users').doc(driver_id).get();
        var driverData = usersnapshots.data();
        if (driverData) {
            var driverName = driverData.firstName + ' ' + driverData.lastName;
            $('.driverName').html('{{trans('lang.drivers_payout_plural')}} - ' + driverName);
        }
    }

    async function buildHTML(snapshots) {
        var html = [];

        var val = snapshots;
        var route1 = '{{route("drivers.view", ":id")}}';
        route1 = route1.replace(':id', val.driverID);

        html.push('<a  href="' + route1 + '">'+val.driverName+'</a>');

        if (currencyAtRight) {
            html.push('<span class="text-red">' + parseFloat(val.amount).toFixed(decimal_digits) + ' ' + currentCurrency + '</span>');
        } else {
            html.push('<span class="text-red">' + currentCurrency + ' ' + parseFloat(val.amount).toFixed(decimal_digits) + '</span>');
        }

        var date = val.paidDate.toDate().toDateString();
        var time = val.paidDate.toDate().toLocaleTimeString('en-US');
        html.push(date + ' ' + time);

        if (val.note != undefined && val.note != '') {
           html.push(val.note);
        } else {
            html.push('');
        }

        if (val.adminNote != undefined && val.adminNote != '') {
            html.push(val.adminNote);
        } else {
            html.push('');
        }

        return html;
    }

    
    async function payoutDriverfunction(driverID) {
        var payoutDriver = '';
        var routedriver = '{{route("drivers.view", ":id")}}';
        routedriver = routedriver.replace(':id', driverID);
        await database.collection('users').where("id", "==", driverID).get().then(async function (snapshotss) {
            if (snapshotss.docs[0]) {
                var driver_data = snapshotss.docs[0].data();
                payoutDriver = driver_data.firstName + " " + driver_data.lastName;
                jQuery(".driver_" + driverID).attr("data-url", routedriver).html(payoutDriver);
            } else {
                jQuery(".driver_" + driverID).attr("data-url", routedriver).html('');
            }
        });
        return payoutDriver;
    }
</script>



@endsection