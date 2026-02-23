@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.menu_items')}}</h3>

        </div>


        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item">{{trans('lang.menu_items')}}</li>

                <li class="breadcrumb-item active">{{trans('lang.menu_items_table')}}</li>

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

                                <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.menu_items_table')}}</a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link" href="{!! route('setting.banners.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.menu_items_create')}}</a>

                            </li>


                        </ul>

                    </div>

                    <div class="card-body">

                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">

                            <table id="bannerItemsTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <?php if (in_array('banners.delete', json_decode(@session('user_permissions'), true))) { ?>
                                        <th class="delete-all">
                                            <input type="checkbox" id="select-all">
                                            <label class="col-3 control-label" for="select-all">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                                    <i class="fa fa-trash"></i> {{trans('lang.all')}}
                                                </a>
                                            </label>
                                        </th>
                                        <?php } ?>

                                        <th>{{trans('lang.photo')}}</th>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.banner_position')}}</th>
                                        <th>{{trans('lang.publish')}}</th>
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

    var refData = database.collection('menu_items').orderBy('title');


    var placeholderImage = '';

    var placeholder = database.collection('settings').doc('placeHolderImage');


    placeholder.get().then(async function(snapshotsimage) {

        var placeholderImageData = snapshotsimage.data();

        placeholderImage = placeholderImageData.image;

    })

    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('banners.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }


    $(document).ready(function() {

        jQuery("#data-table_processing").show();
        const table = $('#bannerItemsTable').DataTable({
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
                const orderableColumns =(checkDeletePermission) ? ['', '', 'title', 'position', '', ''] : ['', 'title', 'position', '', '']; // Ensure this matches the actual column names
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

                    querySnapshot.forEach(function (doc) {
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data

                        if (searchValue) {
                            if (
                                (childData.title && childData.title.toString().toLowerCase().includes(searchValue)) ||
                                (childData.position && childData.position.toString().toLowerCase().includes(searchValue))
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

                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });

                    const totalRecords = filteredRecords.length;

                    const paginatedRecords = filteredRecords.slice(start, start + length);

                    paginatedRecords.forEach(function (childData) {

                        var route1 = '{{route("setting.banners.edit", ":id")}}';

                        route1 = route1.replace(':id', childData.id);

                        records.push([
                            checkDeletePermission ? '<td class="delete-all"><input type="checkbox" name="record" id="is_open_' + childData.id + '" class="is_open" data-id="' + childData.id + '" style="width:30px;"><label class="col-3 control-label"\n' + 'for="is_open_' + childData.id + '" ></label></td>' : '',
                            childData.photo != '' && childData.photo!= null  ? '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.photo + '" alt="image">' : '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">',
                            '<a href="' + route1 + '">' + childData.title + '</a>',
                            childData.position,
                            childData.is_publish ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>',
                            '<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a><?php if (in_array('banners.delete', json_decode(@session('user_permissions'), true))) { ?> <a id="' + childData.id + '" name="vendor-delete" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i></a></span><?php } ?>'

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
            order: (checkDeletePermission) ? [2, 'asc'] : [1, 'asc'],
            columnDefs: [
                { targets: (checkDeletePermission) ? [0, 1, 4, 5] : [0, 3, 4], orderable: false }
            ],
            language: {
                zeroRecords: '{{trans("lang.no_record_found")}}',
                emptyTable: '{{trans("lang.no_record_found")}}',
                "processing": '',
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


    })

    /* toggal publish action code start*/
    $(document).on("click", "input[name='isActive']", function(e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('menu_items').doc(id).update({
                'is_publish': true
            }).then(function(result) {

            });
        } else {
            database.collection('menu_items').doc(id).update({
                'is_publish': false
            }).then(function(result) {

            });
        }

    });

    /*toggal publish action code end*/

    $('#select-all').change(function() {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"][name="record"]').prop('checked', isChecked);
    });

    $('#deleteAll').click(function() {
        if ($('#bannerItemsTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                // Loop through all selected records and delete them
                $('input[type="checkbox"][name="record"]:checked').each(function() {
                    var id = $(this).attr('data-id');
                    // Perform deletion of record with id
                    database.collection('menu_items').doc(id).delete();
                });
                // Reload the page or refresh the data as needed
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document).on("click", "a[name='vendor-delete']", function(e) {
        var id = this.id;
        database.collection('menu_items').doc(id).delete().then(function() {
            window.location.reload();
        });
    });
</script>

@endsection
