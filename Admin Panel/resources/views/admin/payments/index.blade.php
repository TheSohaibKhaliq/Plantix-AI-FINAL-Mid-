@extends('layouts.app')





@section('content')

<div class="page-wrapper">





    <div class="row page-titles">



        <div class="col-md-5 align-self-center">



            <h3 class="text-themecolor">{{trans('lang.payment_plural')}}</h3>



        </div>



        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.payment_plural')}}</li>

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

                            style="display: none;">{{ trans('lang.processing')}}

                        </div>



                        <div class="table-responsive m-t-10">





                            <table id="paymentTable"

                                class="display nowrap table table-hover table-striped table-bordered table table-striped"

                                cellspacing="0" width="100%">



                                <thead>



                                    <tr>

                                        <th>{{ trans('lang.store')}}</th>

                                        <th>{{ trans('lang.total_amount')}}</th>

                                        <th>{{trans('lang.paid_amount')}}</th>

                                        <th>{{trans('lang.remaining_amount')}}</th>

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



    var database = firebase.firestore();

    var offest = 1;

    var pagesize = 10;

    var end = null;

    var endarray = [];

    var start = null;

    var user_number = [];

    var ref = database.collection('vendors').orderBy('title');



    var append_list = '';



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





    $(document).ready(function () {



        $(document.body).on('click', '.redirecttopage', function () {

            var url = $(this).attr('data-url');

            window.location.href = url;

        });



        jQuery("#data-table_processing").show();



        const table = $('#paymentTable').DataTable({

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

                const orderableColumns = ['title', 'totalAmount', 'paidAmount', 'remainingAmount']; // Ensure this matches the actual column names

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

                        var data = await remainingPrice(childData.id);

                        var total = remaining_val = paid_price_val = 0;

                        total = data.total;

                        remaining_val =data.remaining_val;

                        paid_price_val = data.paid_price_val;

                        childData.totalAmount = total;

                        childData.paidAmount = paid_price_val;

                        childData.remainingAmount = remaining_val;

                        childData.id = doc.id; // Ensure the document ID is included in the data              

                        if (total != 0) {

                            if (searchValue) {



                                if (

                                    (childData.title && childData.title.toLowerCase().toString().includes(searchValue)) ||

                                    (total && total.toString().includes(searchValue)) ||

                                    (remaining_val && remaining_val.toString().includes(searchValue)) ||

                                    (paid_price_val && paid_price_val.toString().includes(searchValue))



                                ) {

                                    filteredRecords.push(childData);

                                }

                            } else {

                                filteredRecords.push(childData);

                            }

                        }

                    }));



                    filteredRecords.sort((a, b) => {

                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';

                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';

                        

                        if (orderByField === 'totalAmount') {

                           

                            aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;

                            bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;



                        }

                        if (orderByField === 'paidAmount') {

                            aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;

                            bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;



                        }



                        if (orderByField === 'remainingAmount') {

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

            order: [[0, 'asc']],

            columnDefs: [

                {

                    targets: [1, 2, 3],

                    type: 'num-fmt',

                    render: function (data, type, row, meta) {

                        if (type === 'display') {

                            return data;

                        }

                        return parseFloat(data.replace(/[^0-9.-]+/g, ""));

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







    async function buildHTML(val) {

        var html = [];

        newdate = '';

        var id = val.id;

        var route1 = '{{route("stores.view",":id")}}';

        route1 = route1.replace(':id', id);



        

        var data = {};

        data.total = val.totalAmount;

        data.paid_price_val = val.paidAmount;

        data.remaining_val = val.remainingAmount;

        var total = Math.abs(val.totalAmount);

        var remaining_val = Math.abs(val.remainingAmount);

        var paid_price_val = Math.abs(val.paidAmount);



        if (total != 0) {

            var total_class = 'text-green';

            var paid_price_val_class = 'text-red';

            var remaining_val_class = 'text-green';



            if (currencyAtRight) {



                if (data.total < 0) {

                    total_class = 'text-red';



                    total = Math.abs(data.total);

                    data.total = '(-' + parseFloat(total).toFixed(decimal_degits) + "" + currentCurrency + ')';



                } else {

                    data.total = parseFloat(data.total).toFixed(decimal_degits) + "" + currentCurrency;



                }





                paid_price_val = Math.abs(data.paid_price_val);

                data.paid_price_val = '(' + parseFloat(paid_price_val).toFixed(decimal_degits) + "" + currentCurrency + ')';







                if (data.remaining_val < 0) {

                    remaining_val_class = 'text-red';

                    remaining_val = Math.abs(data.remaining_val);

                    data.remaining_val = '(-' + parseFloat(remaining_val).toFixed(decimal_degits) + "" + currentCurrency + ')';

                } else {

                    data.remaining_val = parseFloat(data.remaining_val).toFixed(decimal_degits) + "" + currentCurrency;



                }

            } else {



                if (data.total < 0) {

                    total_class = 'text-red';



                    total = Math.abs(data.total);

                    data.total = '(-' + currentCurrency + "" + parseFloat(total).toFixed(decimal_degits) + ')';



                } else {

                    data.total = currentCurrency + "" + parseFloat(data.total).toFixed(decimal_degits);



                }



                paid_price_val = Math.abs(data.paid_price_val);

                data.paid_price_val = '(' + currentCurrency + "" + parseFloat(paid_price_val).toFixed(decimal_degits) + ')';



                if (data.remaining_val < 0) {

                    remaining_val_class = 'text-red';



                    remaining_val = Math.abs(data.remaining_val);



                    data.remaining_val = '(-' + currentCurrency + "" + parseFloat(remaining_val).toFixed(decimal_degits) + ')';



                } else {

                    data.remaining_val = currentCurrency + "" + parseFloat(data.remaining_val).toFixed(decimal_degits);



                }





            }



            html.push('<a href="' + route1 + '" class="redirecttopage ">' + val.title + '</a>');

            html.push('<span class="' + total_class + '">' + data.total + '</span>');

            html.push('<span class="' + paid_price_val_class + '">' + data.paid_price_val + '</span>');

            html.push('<span class="' + remaining_val_class + '">' + data.remaining_val + '</span>');



        }

        return html;

    }



    async function remainingPrice(vendorID) {



        var data = {};



        var paid_price = 0;



        var total_price = 0;



        var remaining = 0;



        var adminCommission = 0;



        await database.collection('payouts').where('vendorID', '==', vendorID).where('paymentStatus', '==', 'Success').get().then(async function (payoutSnapshots) {



            payoutSnapshots.docs.forEach((payout) => {



                var payoutData = payout.data();



                paid_price = parseFloat(paid_price) + parseFloat(payoutData.amount);



            });



            await database.collection('users').where('vendorID', '==', vendorID).get().then(async function (vendorSnapshots) {

                var vendor = [];

                var wallet_amount = 0;

                if (vendorSnapshots.docs.length) {

                    vendor = vendorSnapshots.docs[0].data();



                    if (isNaN(vendor.wallet_amount) || vendor.wallet_amount == undefined || vendor.wallet_amount == "") {

                        wallet_amount = 0;

                    } else {

                        wallet_amount = vendor.wallet_amount;

                    }



                }



                var remaining = wallet_amount;



                total_price = wallet_amount + paid_price;



                if (Number.isNaN(paid_price)) {

                    paid_price = 0;

                }



                if (Number.isNaN(total_price)) {

                    total_price = 0;

                }



                if (Number.isNaN(remaining)) {

                    remaining = 0;

                }



                data = {

                    'total': total_price,

                    'paid_price_val': paid_price,

                    'remaining_val': remaining,

                };

            });



        });





        return data;



    }



</script>



@endsection