@extends('layouts.app')



@section('content')

<div class="page-wrapper">





    <div class="row page-titles">



        <div class="col-md-5 align-self-center">



            <h3 class="text-themecolor">{{trans('lang.store_plural')}}</h3>



        </div>



        <div class="col-md-7 align-self-center">



            <ol class="breadcrumb">



                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>



                <li class="breadcrumb-item">{{trans('lang.store_plural')}}</li>



                <li class="breadcrumb-item active">{{trans('lang.store_table')}}</li>



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

                                <a class="nav-link active" href="{!! url()->current() !!}"><i

                                        class="fa fa-list mr-2"></i>{{trans('lang.stores_table')}}</a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link" href="{!! route('stores.create') !!}"><i

                                        class="fa fa-plus mr-2"></i>{{trans('lang.create_store')}}</a>

                            </li>



                        </ul>

                    </div>

                    <div class="card-body">



                        <div id="data-table_processing" class="dataTables_processing panel panel-default"

                            style="display: none;">{{trans('lang.processing')}}

                        </div>



                        <div class="table-responsive m-t-10">





                            <table id="storeTable"

                                class="display nowrap table table-hover table-striped table-bordered table table-striped"

                                cellspacing="0" width="100%">



                                <thead>



                                    <tr>

                                        <?php if (in_array('store.delete', json_decode(@session('user_permissions'), true))) { ?>

                                            <th class="delete-all"><input type="checkbox" id="is_active"><label

                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"

                                                        class="do_not_delete" href="javascript:void(0)"><i

                                                            class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>

                                            </th>

                                        <?php } ?>



                                        <th>{{trans('lang.store_image')}}</th>



                                        <th>{{trans('lang.store_name')}}</th>





                                        <th>{{trans('lang.store_phone')}}</th>

                                        <th>{{trans('lang.date')}}</th>



                                        <!-- <th>{{trans('lang.order_transactions')}}</th> -->



                                        <th>{{trans('lang.wallet_history')}}</th>



                                        <th>{{trans('lang.item_plural')}}</th>

                                        <th>{{trans('lang.order_plural')}}</th>



                                        <th>{{trans('lang.actions')}}</th>



                                    </tr>



                                </thead>



                                <tbody id="append_restaurants">





                                </tbody>



                            </table>



                        </div>



                        <!-- Popup -->



                        <div class="modal fade" id="create_vendor" tabindex="-1" role="dialog" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered notification-main" role="document">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title" id="exampleModalLongTitle">{{trans('lang.copy_vendor')}}

                                            <span id="vendor_title_lable"></span>

                                        </h5>

                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                            <span aria-hidden="true">&times;</span>

                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <div id="data-table_processing"

                                            class="dataTables_processing panel panel-default" style="display: none;">

                                            {{trans('lang.processing')}}

                                        </div>

                                        <div class="error_top"></div>

                                        <!-- Form -->

                                        <div class="form-row">

                                            <div class="col-md-12 form-group">

                                                <label class="form-label">{{trans('lang.first_name')}}</label>

                                                <div class="input-group">

                                                    <input placeholder="Name" type="text" id="user_name"

                                                        class="form-control">

                                                </div>

                                            </div>

                                            <div class="col-md-12 form-group">

                                                <label class="form-label">{{trans('lang.last_name')}}</label>

                                                <div class="input-group">

                                                    <input placeholder="Name" type="text" id="user_last_name"

                                                        class="form-control">

                                                </div>

                                            </div>

                                            <div class="col-md-12 form-group">

                                                <label class="form-label">{{trans('lang.vendor_title')}}</label>

                                                <div class="input-group">

                                                    <input placeholder="Vendor Title" type="text" id="vendor_title"

                                                        class="form-control">

                                                </div>

                                            </div>

                                            <div class="col-md-12 form-group"><label

                                                    class="form-label">{{trans('lang.email')}}</label><input

                                                    placeholder="Email" value="" id="user_email" type="text"

                                                    class="form-control"></div>

                                            <div class="col-md-12 form-group"><label

                                                    class="form-label">{{trans('lang.password')}}</label><input

                                                    placeholder="Password" id="user_password" type="password"

                                                    class="form-control">

                                            </div>



                                        </div>

                                        <!-- Form -->

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-primary save-form-btn"

                                            >{{trans('lang.create')}}

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Popup -->





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

    var refData = database.collection('vendors');

    var ref = refData.orderBy('createdAt', 'desc');

    var append_list = '';



    var placeholderImage = '';



    var user_permissions = '<?php echo @session("user_permissions") ?>';

    user_permissions = Object.values(JSON.parse(user_permissions));

    var checkDeletePermission = false;

    if ($.inArray('store.delete', user_permissions) >= 0) {

        checkDeletePermission = true;

    }



    var userData = [];

    var vendorData = [];

    var vendorProducts = [];



    var placeholder = database.collection('settings').doc('placeHolderImage');



    placeholder.get().then(async function (snapshotsimage) {

        var placeholderImageData = snapshotsimage.data();

        placeholderImage = placeholderImageData.image;

    })

    $(document).ready(function () {



        jQuery("#data-table_processing").show();



        const table = $('#storeTable').DataTable({

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

                const orderableColumns = (checkDeletePermission) ? ['', '', 'title', 'phone', 'createdAt', '', 'foods', 'orders', ''] : ['', 'title', 'phone', 'createdAt', '', 'foods', 'orders', '']; // Ensure this matches the actual column names

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

                        childData.phone = (childData.phonenumber != '' && childData.phonenumber != null && childData.phonenumber.slice(0, 1) == '+') ? childData.phonenumber.slice(1) : childData.phonenumber;



                        childData.id = doc.id; // Ensure the document ID is included in the data              

                        if (childData.id) {

                            childData.orders = await getTotalOrders(childData.id);

                            childData.foods = await getTotalProduct(childData.id);

                        } else {

                            childData.orders = 0;

                            childData.foods = 0;



                        }

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

                                (childData.title && childData.title.toLowerCase().toString().includes(searchValue)) ||

                                (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||

                                (childData.email && childData.email.toLowerCase().toString().includes(searchValue)) ||

                                (childData.phoneNumber && childData.phoneNumber.toString().includes(searchValue))



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

                        if (orderByField === 'createdAt') {

                            try {

                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;

                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                            } catch (err) {



                            }

                        }

                        if (orderByField === 'foods') {

                            aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;

                            bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;

                            

                        }

                        if (orderByField === 'orders') {

                            aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;

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

            order: (checkDeletePermission) ? [[4, 'desc']] : [[3, 'desc']],

            columnDefs: [

                {

                    targets: (checkDeletePermission) ? 4 : 3,

                    type: 'date',

                    render: function (data) {

                        return data;

                    }

                },

                { orderable: false, targets: (checkDeletePermission) ? [0, 1, 5, 8] : [0, 4, 7] },

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







    async function buildHTML(val) {



        var html = [];
        
        if(val.title != " " || val.title != "null" || val.title != null ){
            var id = val.id;
            var vendorUserId = val.author;

            var route1 = '{{route("stores.edit",":id")}}';
            route1 = route1.replace(':id', id);

            var route_view = '{{route("stores.view",":id")}}';
            route_view = route_view.replace(':id', id);
            if (checkDeletePermission) {
                html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '" author="' + val.author + '"><label class="col-3 control-label"\n' +
                    'for="is_open_' + id + '" ></label></td>');
            }

            if (val.photo != '' && val.photo != null) {
                html.push('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + val.photo + '" alt="image">');

            } else {

                html.push('<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">');
            }

            if(val.title != null && val.title != ""){
                html.push('<a href="' + route_view + '">' + val.title + '</a>');
            }
            else
            {
                html.push('');
            }
            

            if (val.hasOwnProperty('phonenumber') && val.phonenumber != null && val.phonenumber != "") {
                html.push(shortEditNumber(val.phonenumber));
            } else {
                html.push('');
            }

            var date = '';
            var time = '';
            if (val.hasOwnProperty("createdAt")) {
                try {
                    date = val.createdAt.toDate().toDateString();
                    time = val.createdAt.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
                html.push('<span class="dt-time">' + date + ' ' + time + '</span>');
            } else {
                html.push('');
            }

            var payoutRequests = '{{route("users.walletstransaction",":id")}}';
            payoutRequests = payoutRequests.replace(':id', val.author);

            html.push('<a href="' + payoutRequests + '">{{trans("lang.wallet_history")}}</a>');

            var active = val.isActive;
            var vendorId = val.id;
            var url = '{{route("stores.items",":id")}}';
            url = url.replace(":id", vendorId);
            html.push('<a href = "' + url + '">' + val.foods + '</a>');

            var url2 = '{{route("stores.orders",":id")}}';
            url2 = url2.replace(":id", vendorId);
            html.push('<a href="' + url2 + '">' + val.orders + '</a>');
            var actionHtml = '';
            actionHtml += '<span class="action-btn"><a href="javascript:void(0)" vendor_id="' + val.id + '" author="' + val.author + '" name="vendor-clone"><i class="fa fa-clone"></i></a><a href="' + route_view + '"><i class="fa fa-eye"></i></a><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
            if (checkDeletePermission) {
                actionHtml += '<a id="' + val.id + '" author="' + val.author + '" name="vendor-delete" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i></a></span>';
            }
            html.push(actionHtml);
            return html;
        }
       
    }



    async function getTotalProduct(id) {

        var Product_total = 0;



        await database.collection('vendor_products').where('vendorID', '==', id).get().then(async function (productSnapshots) {

            Product_total = productSnapshots.docs.length;



        });



        return Product_total;

    }



    async function getTotalOrders(id) {

        var order_total = 0;



        await database.collection('restaurant_orders').where('vendorID', '==', id).get().then(async function (productSnapshots) {

            order_total = productSnapshots.docs.length;



        });



        return order_total;

    }



    $("#is_active").click(function () {

        $("#storeTable .is_open").prop('checked', $(this).prop('checked'));



    });



    $("#deleteAll").click(function () {

        if ($('#storeTable .is_open:checked').length) {

            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#data-table_processing").show();

                $('#storeTable .is_open:checked').each(function () {

                    var dataId = $(this).attr('dataId');

                    var author = $(this).attr('author');



                    database.collection('users').doc(author).update({ 'vendorID': "" }).then(function (result) {

                        database.collection('vendors').doc(dataId).delete().then(function () {



                            const getStoreName = deleteStoreData(dataId);



                            setTimeout(function () {

                                window.location.reload();

                            }, 7000);

                        });

                    });





                }

                );



            }

        } else {

            alert("{{trans('lang.select_delete_alert')}}");

        }

    });





    $(document.body).on('click', '.redirecttopage', function () {

        var url = $(this).attr('data-url');

        window.location.href = url;

    });



    $(document).on("click", "a[name='vendor-delete']", function (e) {

        var id = this.id;

        jQuery("#data-table_processing").show();

        var author = $(this).attr('author');

        if (confirm("{{trans('lang.selected_delete_alert')}}")) {

            database.collection('vendors').doc(id).delete().then(function () {

                deleteStoreData(id).then(function () {

                    setTimeout(function () {

                        window.location.reload();

                    }, 9000);

                });

            });

        }

    });

    async function deleteStoreData(storeId) {

        await database.collection('users').where('vendorID', '==', storeId).get().then(async function (userssanpshots) {



            if (userssanpshots.docs.length > 0) {

                var item_data = userssanpshots.docs[0].data();

                await database.collection('wallet').where('user_id', '==', item_data.id).get().then(async function (snapshotsItem) {

                    if (snapshotsItem.docs.length > 0) {

                        snapshotsItem.docs.forEach((temData) => {

                            var item_data = temData.data();

                            database.collection('wallet').doc(item_data.id).delete().then(function () { });

                        });

                    }

                });



                var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';

                var dataObject = { "data": { "uid": item_data.id } };



                jQuery.ajax({

                    url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',

                    method: 'POST',

                    contentType: "application/json; charset=utf-8",

                    data: JSON.stringify(dataObject),

                    success: function (data) {

                        console.log('Delete user success:', data.result);

                        database.collection('users').doc(item_data.id).delete().then(function () {

                        });

                    },

                    error: function (xhr, status, error) {

                        var responseText = JSON.parse(xhr.responseText);

                        console.log('Delete user error:', responseText.error);

                    }

                });

            }

        });

        await database.collection('vendor_products').where('vendorID', '==', storeId).get().then(async function (snapshots) {

            if (snapshots.docs.length > 0) {

                snapshots.docs.forEach((listval) => {

                    var data = listval.data();

                    database.collection('vendor_products').doc(data.id).delete().then(function () {

                    });

                });

            }

        });

        await database.collection('foods_review').where('VendorId', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('foods_review').doc(item_data.Id).delete().then(function () {

                    });

                });

            }



        });

        await database.collection('coupons').where('resturant_id', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('coupons').doc(item_data.id).delete().then(function () {

                    });

                });

            }



        });

        await database.collection('favorite_restaurant').where('restaurant_id', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('favorite_restaurant').doc(item_data.id).delete().then(function () {

                    });

                });

            }

        })

        await database.collection('favorite_item').where('store_id', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('favorite_item').doc(item_data.id).delete().then(function () {

                    });

                });

            }

        })

        await database.collection('payouts').where('vendorID', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('payouts').doc(item_data.id).delete().then(function () {

                    });

                });

            }



        });

        await database.collection('booked_table').where('vendorID', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('booked_table').doc(item_data.id).delete().then(function () {

                    });

                });

            }



        });

        await database.collection('story').where('vendorID', '==', storeId).get().then(async function (snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {

                snapshotsItem.docs.forEach((temData) => {

                    var item_data = temData.data();

                    database.collection('story').doc(item_data.id).delete().then(function () {

                    });

                });

            }



        });

    }



    $(document).on("click", "a[name='vendor-clone']", async function (e) {



        var id = $(this).attr('vendor_id');

        var author = $(this).attr('author');



        await database.collection('users').doc(author).get().then(async function (snapshotsusers) {

            userData = snapshotsusers.data();

        });

        await database.collection('vendors').doc(id).get().then(async function (snapshotsvendors) {

            vendorData = snapshotsvendors.data();

        });

        await database.collection('vendor_products').where('vendorID', '==', id).get().then(async function (snapshotsproducts) {

            vendorProducts = [];

            snapshotsproducts.docs.forEach(async (product) => {

                vendorProducts.push(product.data());

            });



        });





        if (userData && vendorData) {

            jQuery("#create_vendor").modal('show');

            jQuery("#vendor_title_lable").text(vendorData.title);

        }

    });





    $(document).on("click", ".save-form-btn", async function (e) {

        var vendor_id = database.collection("tmp").doc().id;



        if (userData && vendorData) {



            var vendor_title = jQuery("#vendor_title").val();

            var userFirstName = jQuery("#user_name").val();

            var userLastName = jQuery("#user_last_name").val();

            var email = jQuery("#user_email").val();

            var password = jQuery("#user_password").val();



            if (userFirstName == '') {



                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.user_name_required')}}</p>");

                window.scrollTo(0, 0);



            } else if (userLastName == '') {



                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.user_last_name_required')}}</p>");

                window.scrollTo(0, 0);



            } else if (vendor_title == '') {



                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.vendor_title_required')}}</p>");

                window.scrollTo(0, 0);



            } else if (email == '') {



                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.user_email_required')}}</p>");

                window.scrollTo(0, 0);

            } else if (password == '') {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.enter_owners_password_error')}}</p>");

                window.scrollTo(0, 0);

            } else {



                jQuery("#data-table_processing2").show();



                firebase.auth().createUserWithEmailAndPassword(email, password).then(async function (firebaseUser) {



                    var user_id = firebaseUser.user.uid;



                    userData.email = email;

                    userData.firstName = userFirstName;

                    userData.lastName = userLastName;

                    userData.id = user_id;

                    userData.vendorID = vendor_id;

                    userData.createdAt = createdAt;

                    userData.wallet_amount = 0;



                    vendorData.author = user_id;

                    vendorData.authorName = userFirstName + ' ' + userLastName;

                    vendorData.title = vendor_title;

                    vendorData.id = vendor_id;

                    coordinates = new firebase.firestore.GeoPoint(vendorData.latitude, vendorData.longitude);

                    vendorData.coordinates = coordinates;

                    vendorData.createdAt = createdAt;



                    await database.collection('users').doc(user_id).set(userData).then(async function (result) {



                        await geoFirestore.collection('vendors').doc(vendor_id).set(vendorData).then(async function (result) {

                            var count = 0;

                            await vendorProducts.forEach(async (product) => {

                                var product_id = await database.collection("tmp").doc().id;

                                product.id = product_id;

                                product.vendorID = vendor_id;

                                await database.collection('vendor_products').doc(product_id).set(product).then(function (result) {

                                    count++;

                                    if (count == vendorProducts.length) {

                                        jQuery("#data-table_processing2").hide();

                                        alert('Successfully created.');

                                        jQuery("#create_vendor").modal('hide');

                                        location.reload();

                                    }

                                });



                            });





                        });

                    }) 



                }).catch(function (error) {

                    $(".error_top").show();

                    jQuery("#data-table_processing2").hide();

                    $(".error_top").html("");

                    $(".error_top").append("<p>" + error + "</p>");

                });



            }

        }

    });





</script>







@endsection