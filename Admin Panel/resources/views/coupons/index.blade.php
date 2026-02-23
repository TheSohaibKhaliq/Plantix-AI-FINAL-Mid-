@extends('layouts.app')



@section('content')



<div class="page-wrapper">



    <div class="row page-titles">



        <div class="col-md-5 align-self-center">



            <h3 class="text-themecolor restaurantTitle">{{trans('lang.coupon_plural')}}</h3>



        </div>



        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>



                <li class="breadcrumb-item active">{{trans('lang.coupon_table')}}</li>

            </ol>

        </div>



    </div>





    <div class="container-fluid">



        <div class="row">



            <div class="col-12">



                <?php if ($id != '') { ?>

                    <div class="menu-tab">

                        <ul>

                            <li>

                                <a href="{{route('stores.view', $id)}}">{{trans('lang.tab_basic')}}</a>

                            </li>

                            <li>

                                <a href="{{route('stores.items', $id)}}">{{trans('lang.tab_items')}}</a>

                            </li>

                            <li>

                                <a href="{{route('stores.orders', $id)}}">{{trans('lang.tab_orders')}}</a>

                            </li>

                            <li class="active">

                                <a href="{{route('stores.coupons', $id)}}">{{trans('lang.tab_promos')}}</a>

                            <li>

                                <a href="{{route('stores.payout', $id)}}">{{trans('lang.tab_payouts')}}</a>

                            </li>

                            <li>

                            <a href="{{route('payoutRequests.stores.view', $id)}}">{{trans('lang.tab_payout_request')}}</a>

                            </li>

                            <li id="restaurant_wallet"></li>

                        </ul>

                    </div>

                <?php } ?>



                <div class="card">

                    <div class="card-header">

                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">

                            <li class="nav-item">

                                <a class="nav-link active" href="{!! url()->current() !!}"><i

                                            class="fa fa-list mr-2"></i>{{trans('lang.coupon_table')}}</a>

                            </li>

                            <?php if ($id != '') { ?>

                                <li class="nav-item">

                                    <a class="nav-link" href="{!! route('coupons.create') !!}/{{$id}}"><i

                                                class="fa fa-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>

                                </li>

                            <?php } else { ?>

                                <li class="nav-item">

                                    <a class="nav-link" href="{!! route('coupons.create') !!}"><i

                                                class="fa fa-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>

                                </li>

                            <?php } ?>

                        </ul>

                    </div>

                    <div class="card-body">

                        <div id="data-table_processing" class="dataTables_processing panel panel-default"

                             style="display: none;">Processing...

                        </div>



                        <div class="table-responsive m-t-10">



                            <table id="couponTable"

                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"

                                   cellspacing="0" width="100%">



                                <thead>



                                <tr>

                                    <?php if (in_array('coupons.delete', json_decode(@session('user_permissions'), true))) { ?>

                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>

                                    <?php } ?>

                                    </th>

                                    <th>{{trans('lang.coupon_code')}}</th>



                                    <th>{{trans('lang.coupon_discount')}}</th>



                                    <th>{{trans('lang.coupon_privacy')}}</th>



                                    <th>{{trans('lang.coupon_store_id')}}</th>





                                    <th>{{trans('lang.coupon_expires_at')}}</th>





                                    <th>{{trans('lang.coupon_enabled')}}</th>



                                    <th>{{trans('lang.coupon_description')}}</th>





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



</div>

</div>



@endsection



@section('scripts')



<script type="text/javascript">



    var database = firebase.firestore();



    var getId = '{{$id}}';

    <?php if ($id != '') { ?>

    database.collection('vendors').where("id", "==", '<?php    echo $id; ?>').get().then(async function(snapshots) {

        var vendorData = snapshots.docs[0].data();

        walletRoute = "{{route('users.walletstransaction', ':id')}}";

        walletRoute = walletRoute.replace(":id", vendorData.author);

        $('#restaurant_wallet').append('<a href="' + walletRoute + '">{{trans("lang.wallet_transaction")}}</a>');

    });

    var ref = database.collection('coupons').where('resturant_id', '==', '<?php    echo $id; ?>');

    const getStoreName = getStoreNameFunction('<?php    echo $id; ?>');

    <?php } else { ?>

    var ref = database.collection('coupons');

    <?php } ?>



    ref = ref.orderBy('expiresAt', 'desc');

    var currentCurrency = '';

    var currencyAtRight = false;

    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);

    refCurrency.get().then(async function (snapshots) {

        var currencyData = snapshots.docs[0].data();

        currentCurrency = currencyData.symbol;

        currencyAtRight = currencyData.symbolAtRight;



        if (currencyData.decimal_degits) {

            decimal_degits = currencyData.decimal_degits;

        }

    });



    var user_permissions = '<?php echo @session("user_permissions")?>';

    user_permissions = Object.values(JSON.parse(user_permissions));

    var checkDeletePermission = false;

    if ($.inArray('coupons.delete', user_permissions) >= 0) {

        checkDeletePermission = true;

    }



    $(document).ready(function () {



        $(document.body).on('click', '.redirecttopage', function () {

            var url = $(this).attr('data-url');

            window.location.href = url;

        });



        jQuery("#data-table_processing").show();



        const table = $('#couponTable').DataTable({

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

                const orderableColumns =(checkDeletePermission) ? ['','code', 'discount', 'isPublic', 'restaurantName', 'expiresAt','', 'description',''] : ['code', 'discount', 'isPublic', 'restaurantName', 'expiresAt', '', 'description', '']; // Ensure this matches the actual column names

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



                    await Promise.all(querySnapshot.docs.map(async (doc) => {

                        let childData = doc.data();

                        childData.id = doc.id; // Ensure the document ID is included in the data

                        childData.restaurantName = await getrestaurantName(childData.resturant_id);

                        if (searchValue) {

                            var date = '';

                            var time = '';

                            if (childData.hasOwnProperty("expiresAt")) {

                                try {

                                    date = childData.expiresAt.toDate().toDateString();

                                    time = childData.expiresAt.toDate().toLocaleTimeString('en-US');

                                } catch (err) {

                                }

                            }

                            var expiresAt = date + ' ' + time;

                            if (

                                (childData.code && childData.code.toString().toLowerCase().includes(searchValue)) ||

                                (expiresAt && expiresAt.toString().toLowerCase().indexOf(searchValue) > -1) || (childData.restaurantName && childData.restaurantName.toString().toLowerCase().includes(searchValue)) || (childData.description && childData.description.toString().toLowerCase().includes(searchValue))

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

                        if (orderByField === 'expiresAt') {

                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;

                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                        }



                        if (orderByField === 'discount') {

                            aValue = a[orderByField] ? parseInt(a[orderByField] ) : 0;

                            bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;

                        }

                        if (orderDirection === 'asc') {

                            return (aValue > bValue) ? 1 : -1;

                        } else {

                            return (aValue < bValue) ? 1 : -1;

                        }

                    });



                    const totalRecords = filteredRecords.length;



                    const paginatedRecords = filteredRecords.slice(start, start + length);







                    paginatedRecords.forEach(function (childData) {



                        var route1 = '{{route("coupons.edit", ":id")}}';

                        route1 = route1.replace(':id', childData.id);

                        <?php if ($id != '') { ?>

                        route1 = route1 + '?eid={{$id}}';

                        <?php } ?>



                        var route_view = '{{route("stores.view", ":id")}}';

                        route_view = route_view.replace(':id', childData.resturant_id);



                        var date = '';

                        var time = '';

                        if (childData.hasOwnProperty("expiresAt")) {

                            try {

                                date = childData.expiresAt.toDate().toDateString();

                                time = childData.expiresAt.toDate().toLocaleTimeString('en-US');

                            } catch (err) {

                            }

                        }

                        if (currencyAtRight) {

                            if (childData.discountType == 'Percent' || childData.discountType == 'Percentage') {

                                discount_price = childData.discount + "%";

                            } else {

                                discount_price = parseFloat(childData.discount).toFixed(decimal_degits) + "" + currentCurrency;

                            }

                        } else {

                            if (childData.discountType == 'Percent' || childData.discountType == 'Percentage') {

                                discount_price = childData.discount + "%";

                            } else {

                                discount_price = currentCurrency + "" + parseFloat(childData.discount).toFixed(decimal_degits);

                            }

                        }



                        records.push([

                            checkDeletePermission ? '<td class="delete-all"><input type="checkbox" id="is_open_' + childData.id + '" class="is_open" dataId="' + childData.id + '"><label class="col-3 control-label"\n' + 'for="is_open_' + childData.id + '" ></label></td>' : '',

                            '<a href="' + route1 + '"  class="redirecttopage">' + childData.code + '</a>',

                            discount_price,

                            childData.hasOwnProperty('isPublic') && childData.isPublic ? '<td class="success"><span class="badge badge-success py-2 px-3">{{trans("lang.public")}}</sapn></td>' : '<td class="danger"><span class="badge badge-danger py-2 px-3">{{trans("lang.private")}}</sapn></td>',

                            '<td  data-url="' + route_view + '" class="redirecttopage storeName_' + childData.resturant_id + '" >' + childData.restaurantName + '</td>',

                            '<td class="dt-time">' + date + ' ' + time + '</td>',

                            childData.isEnabled ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>',

                            childData.description,

                            '<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a><?php if (in_array('coupons.delete', json_decode(@session('user_permissions'), true))) { ?> <a id="' + childData.id + '" name="coupon_delete_btn" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i></a></span><?php } ?>'



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

            order: (checkDeletePermission) ? [5, 'desc'] : [4, 'desc'],

            columnDefs: [

                { targets: (checkDeletePermission) ? [0, 6, 8] : [5, 7], orderable: false }

            ],

            language: {

                zeroRecords: "{{trans("lang.no_record_found")}}",

                emptyTable: "{{trans("lang.no_record_found")}}"

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



    });



    async function getStoreNameFunction(resturant_id) {

        var vendorName = '';

        await database.collection('vendors').where('id', '==', resturant_id).get().then(async function (snapshots) {

            if(!snapshots.empty){

            var vendorData = snapshots.docs[0].data();



            vendorName = vendorData.title;

            $('.restaurantTitle').html('{{trans("lang.coupon_plural")}} - ' + vendorName);

        }

        });



        return vendorName;

    }



    $(document).on("click", "input[name='isActive']", function (e) {

        var ischeck = $(this).is(':checked');

        var id = this.id;

        if (ischeck) {

            database.collection('coupons').doc(id).update({'isEnabled': true}).then(function (result) {



            });

        } else {

            database.collection('coupons').doc(id).update({'isEnabled': false}).then(function (result) {



            });

        }



    });





    async function getrestaurantName(resturant_id) {

        var title = '';

        if (resturant_id) {

            await database.collection('vendors').where("id", "==", resturant_id).get().then(async function (snapshots) {

                if (snapshots.docs.length > 0) {

                    var data = snapshots.docs[0].data();

                    title = data.title;

                }

            });

        }



        return title;

    }





    $("#is_active").click(function () {



        $("#couponTable .is_open").prop('checked', $(this).prop('checked'));

    });





    $("#deleteAll").click(function () {

        if ($('#couponTable .is_open:checked').length) {



            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#data-table_processing").show();

                $('#couponTable .is_open:checked').each(function () {

                    var dataId = $(this).attr('dataId');



                    database.collection('coupons').doc(dataId).delete().then(function () {



                        window.location.reload();

                    });



                });



            }

        } else {

            alert("{{trans('lang.select_delete_alert')}}");

        }

    });



    $(document).on("click", "a[name='coupon_delete_btn']", function (e) {



        var id = this.id;

        database.collection('coupons').doc(id).delete().then(function () {



            window.location = "{{!url()->current() }}";

        });



    });



</script>



@endsection

