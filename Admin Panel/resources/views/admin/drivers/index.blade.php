@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('drivers/approved'))
                @php    $type = 'approved'; @endphp
                {{trans('lang.approved_drivers')}}
                @elseif(request()->is('drivers/pending'))
                @php    $type = 'pending'; @endphp
                {{trans('lang.approval_pending_drivers')}}
                @else
                @php    $type = 'all'; @endphp
                {{trans('lang.all_drivers')}}
                @endif
            </h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.driver_table')}}</li>

            </ol>

        </div>

        <div>

        </div>

    </div>


    <div class="container-fluid">

        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! route('drivers') !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.driver_table')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('drivers.create') !!}"><i
                                        class="fa fa-plus mr-2"></i>{{trans('lang.drivers_create')}}</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">

                            <table id="driverTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>
                                        <?php if (
    ($type == "approved" && in_array('approve.driver.delete', json_decode(@session('user_permissions'), true))) ||
    ($type == "pending" && in_array('pending.driver.delete', json_decode(@session('user_permissions'), true))) ||
    ($type == "all" && in_array('driver.delete', json_decode(@session('user_permissions'), true)))
) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                        class="do_not_delete" href="javascript:void(0)"><i
                                                            class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>
                                            </th>
                                        <?php } ?>

                                        <th>{{trans('lang.extra_image')}}</th>

                                        <th>{{trans('lang.user_name')}}</th>
                                        <th>{{trans('lang.email')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                        <th>{{trans('lang.document_plural')}}</th>

                                        <th>{{trans('lang.driver_active')}}</th>
                                        <th>{{trans('lang.driver_online')}}</th>

                                        <th>{{trans('lang.wallet_history')}}</th>
                                        <th>{{trans('lang.dashboard_total_orders')}}</th>


                                        <th>{{trans('lang.actions')}}</th>

                                    </tr>

                                </thead>

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
   
    var type = "{{$type}}";

    var ref = database.collection('users').where("role", "==", "driver").orderBy('createdAt', 'desc');
    if (type == 'pending') {
        ref = database.collection('users').where("role", "==", "driver").where("isDocumentVerify", "==", false).orderBy('createdAt', 'desc');
    } else if (type == 'approved') {
        ref = database.collection('users').where("role", "==", "driver").where("isDocumentVerify", "==", true).orderBy('createdAt', 'desc');
    }
    var alldriver = database.collection('users').where("role", "==", "driver");
    var append_list = '';

    var placeholderImage = '';
    var user_permissions = '<?php echo @session("user_permissions") ?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if (
        (type == 'pending' && $.inArray('pending.driver.delete', user_permissions) >= 0) ||
        (type == 'approved' && $.inArray('approve.driver.delete', user_permissions) >= 0) ||
        (type == 'all' && $.inArray('driver.delete', user_permissions) >= 0)
    ) {
        checkDeletePermission = true;
    }

    var placeholder = database.collection('settings').doc('placeHolderImage');

    placeholder.get().then(async function (snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    });

    $(document).ready(function () {

        jQuery("#data-table_processing").show();

        const table = $('#driverTable').DataTable({
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
               
                const orderableColumns = (checkDeletePermission)? ['','','fullName', 'email', 'createdAt','','','','','',''] : ['','fullName', 'email', 'createdAt','','','','','',''];
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                ref.get().then(async function (querySnapshot) {
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

                    querySnapshot.forEach(function (doc) {                        
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        childData.fullName = childData.firstName + ' ' + childData.lastName || " "
                        
                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var createdAt = date + ' ' + time;
                            if (
                                (childData.fullName && childData.fullName.toString().toLowerCase().includes(searchValue)) ||
                                (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) || (childData.email && childData.email.toString().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    });

                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                        if (orderByField === 'createdAt') {
                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
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
                        if (childData.id) {
                            const totalOrderSnapShot = await database.collection('restaurant_orders').where('driverID', '==', childData.id).get();
                            const orders = totalOrderSnapShot.size;

                            childData.orders = orders;
                        } else {
                            childData.orders = 0;
                        }
                    }));

                    paginatedRecords.forEach(function (childData) {
                        var id = childData.id;
                        var route1 = '{{route("drivers.edit", ":id")}}';
                        route1 = route1.replace(':id', id);

                        var driverView = '{{route("drivers.view", ":id")}}';
                        driverView = driverView.replace(':id', id);

                        document_list_view = "{{route('drivers.document', ':id')}}";
                        document_list_view = document_list_view.replace(':id', childData.id);

                        var trroute2 = '{{route("orders", ":id")}}';
                        trroute2 = trroute2.replace(':id', 'driverId=' + id);

                        var walletTransactions = '{{route("users.walletstransaction", ":id")}}';
                        walletTransactions = walletTransactions.replace(':id', id);

                        var date = '';
                        var time = '';
                        if (childData.hasOwnProperty("createdAt")) {
                            try {
                                date = childData.createdAt.toDate().toDateString();
                                time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                            } catch (err) {
                            }
                        }

                        records.push([
                            checkDeletePermission ? '<td class="delete-all"><input type="checkbox" id="is_open_' + childData.id + '" class="is_open" dataId="' + childData.id + '"><label class="col-3 control-label"\n' + 'for="is_open_' + childData.id + '" ></label></td>' : '',
                            childData.profilePictureURL == '' || childData.profilePictureURL == null ? '<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:50px" src="' + childData.profilePictureURL + '" alt="image">',
                            '<a href="' + driverView + '" class="redirecttopage">' + childData.fullName + '</a>',
                            childData.email ? shortEmail(childData.email) : ' ',
                            date + ' ' + time,
                            '<a href="' + document_list_view + '"><i class="fa fa-file"></i></a>',
                            childData.active ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>',
                            childData.isActive ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isOnline"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isOnline"><span class="slider round"></span></label>',
                            '<a href="' + walletTransactions + '">{{trans("lang.wallet_history")}}</a>',
                            '<a href="' + trroute2 + '">' + childData.orders + '</a>',
                            '<span class="action-btn"><a href="' + driverView + '"><i class="fa fa-eye"></i></a><a href="' + route1 + '"><i class="fa fa-edit"></i></a><?php if (in_array('driver.delete', json_decode(@session('user_permissions'), true))) { ?> <a id="' + childData.id + '" name="driver-delete" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i></a><?php } ?></span>'                           

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
            order: [4, 'desc'],
            columnDefs: [
                {
                    targets: (checkDeletePermission) ? 4 : 3,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                { orderable: false, targets: (checkDeletePermission) ? [0, 1, 5, 6, 7, 8, 8, 10] : [0, 4, 5, 6, 7, 9] },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },
        
        });

    
        table.columns.adjust().draw();

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

        alldriver.get().then(async function (snapshotsdriver) {

            snapshotsdriver.docs.forEach((listval) => {
                database.collection('restaurant_orders').where('driverID', '==', listval.id).where("status", "in", ["Order Completed"]).get().then(async function (orderSnapshots) {
                    var count_order_complete = orderSnapshots.docs.length;
                    database.collection('users').doc(listval.id).update({ 'orderCompleted': count_order_complete }).then(function (result) {

                    });

                });

            });
        });

    });

    async function orderDetails(driver) {
        var count_order_complete = 0;

        await database.collection('restaurant_orders').where('driverID', '==', driver).get().then(async function (orderSnapshots) {
            count_order_complete = orderSnapshots.docs.length;

        });

        return count_order_complete;
    }

    $(document).on("click", "input[name='isOnline']", async function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        var switchElement=$(this);
        if (ischeck) {
            await database.collection('users').where('id', '==', id).get().then(async function (snapshot) {
                var data = snapshot.docs[0].data();
                if (data.isDocumentVerify == false) {
                    switchElement.prop('checked',false);
                    alert('{{trans("lang.document_verification_is_pending")}}');
                    
                    return false;
                } else {
                    database.collection('users').doc(id).update({ 'isActive': true }).then(function (result) {
                    });
                }
            })

        } else {
            database.collection('users').doc(id).update({ 'isActive': false }).then(function (result) {
            });
        }
    });

    $(document).on("click", "input[name='isActive']", function (e) {
        jQuery("#data-table_processing").show();
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('users').doc(id).update({ 'active': true }).then(function (result) {
                jQuery("#data-table_processing").hide();
            });
        } else {
            database.collection('users').doc(id).update({ 'active': false }).then(function (result) {
                jQuery("#data-table_processing").hide();
            });
        }
    });

    $("#is_active").click(function () {
        $("#driverTable .is_open").prop('checked', $(this).prop('checked'));

    });

    $("#deleteAll").click(function () {
        if ($('#driverTable .is_open:checked').length) {

            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#data-table_processing").show();

                $('#driverTable .is_open:checked').each(function () {

                    var dataId = $(this).attr('dataId');

                    database.collection('users').doc(dataId).delete().then(function () {

                        const getStoreName = deleteDriverData(dataId);

                        setTimeout(function () {
                            window.location.reload();
                        }, 7000);

                    });

                });

            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document.body).on('click', '.redirecttopage', function () {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });

    $(document).on("click", "a[name='driver-delete']", function (e) {
        var id = this.id;
        jQuery("#data-table_processing").show();
        database.collection('users').doc(id).delete().then(function () {
            deleteDriverData(id).then(function () {
                setTimeout(function () {
                    window.location.reload();
                }, 9000);

            });
        });


    });

    async function deleteDriverData(driverId) {

        //delete user from authentication  
        var dataObject = { "data": { "uid": driverId } };
        var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
        jQuery.ajax({
            url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
            method: 'POST',
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(dataObject),
            success: function (data) {
                console.log('Delete user success:', data.result);
            },
            error: function (xhr, status, error) {
                var responseText = JSON.parse(xhr.responseText);
                console.log('Delete user error:', responseText.error);
            }
        });

        await database.collection('wallet').where('user_id', '==', driverId).get().then(async function (snapshotsItem) {
            if (snapshotsItem.docs.length > 0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data = temData.data();
                    database.collection('wallet').doc(item_data.id).delete().then(function () {
                    });
                });
            }
        });

        await database.collection('driver_payouts').where('driverID', '==', driverId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data = temData.data();

                    database.collection('driver_payouts').doc(item_data.id).delete().then(function () {

                    });
                });
            }

        });

    }

    function searchclear() {
        jQuery("#search").val('');
        searchtext();
    }

</script>

@endsection