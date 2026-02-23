@extends('layouts.app')



@section('content')



<div class="page-wrapper">



    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.languages')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.languages')}}</li>

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

                                            class="fa fa-list mr-2"></i>{{trans('lang.languages')}}</a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link" href="{!! route('settings.app.languages.create') !!}"><i

                                            class="fa fa-plus mr-2"></i>{{trans('lang.language_create')}}</a>

                            </li>

                        </ul>

                    </div>

                    <div class="card-body">

                        <div id="data-table_processing" class="dataTables_processing panel panel-default"

                             style="display: none;">{{trans('lang.processing')}}

                        </div>



                        <div class="table-responsive m-t-10">

                            <table id="languageTable"

                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"

                                   cellspacing="0" width="100%">

                                <thead>

                                <tr>

                                    <?php if (in_array('language.delete', json_decode(@session('user_permissions'),true))) { ?>

                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll"

                                    class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>

                                    <?php } ?>

                                    <th>{{trans('lang.image')}}</th>

                                    <th>{{trans('lang.name')}}</th>

                                    <th>{{trans('lang.slug')}}</th>

                                    <th>{{trans('lang.active')}}</th>

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



    var database = firebase.firestore();



    var offest = 1;

    var pagesize = 10;

    var end = null;

    var endarray = [];

    var start = null;

    var user_number = [];

    var languages = [];

    var ref = database.collection('settings').doc('languages');

    var placeholderImage = '';

    var placeholder = database.collection('settings').doc('placeHolderImage');

    placeholder.get().then(async function (snapshotsimage) {

        var placeholderImageData = snapshotsimage.data();

        placeholderImage = placeholderImageData.image;

    });

    var append_list = '';

    var user_permissions = '<?php echo @session("user_permissions")?>';

    user_permissions = Object.values(JSON.parse(user_permissions));

    var checkDeletePermission = false;

    if ($.inArray('language.delete', user_permissions) >= 0) {

        checkDeletePermission = true;

    }



    $(document).ready(function () {



        $(document.body).on('click', '.redirecttopage', function () {

            var url = $(this).attr('data-url');

            window.location.href = url;

        });



        var inx = parseInt(offest) * parseInt(pagesize);

        jQuery("#data-table_processing").show();



        append_list = document.getElementById('append_list1');

        append_list.innerHTML = '';



        ref.get().then(async function (snapshots) {

            html = '';

            snapshots = snapshots.data();

            if (snapshots) {

                snapshots = snapshots.list;

                languages = snapshots;

                html = await buildHTML(snapshots);

                if (html != '') {

                    append_list.innerHTML = html;

                }

            }



            if (checkDeletePermission) {
                
                $('#languageTable').DataTable({

                    order: [['2', 'asc']],

                    columnDefs: [



                        {orderable: false, targets: [0,1,4,5]},

                    ],



                    "language": {

                        "zeroRecords": "{{trans("lang.no_record_found")}}",

                        "emptyTable": "{{trans("lang.no_record_found")}}"

                    },

                    responsive: true

                });

            }

            else

            {

                 $('#languageTable').DataTable({

                    order: [['1', 'asc']],

                    columnDefs: [



                        {orderable: false, targets: [0,3,4]},

                    ],



                    "language": {

                        "zeroRecords": "{{trans("lang.no_record_found")}}",

                        "emptyTable": "{{trans("lang.no_record_found")}}"

                    },

                    responsive: true

                });

            }



            jQuery("#data-table_processing").hide();

        });



    });





    function buildHTML(snapshots) {

        var html = '';

        var alldata = [];

        var number = [];

        if (snapshots.length) {

            snapshots.forEach((listval) => {

                var datas = listval;

                datas.id = listval.id;

                alldata.push(datas);

            });

            var count = 0;

            alldata.forEach((listval) => {

                var val = listval;

                html = html + '<tr>';

                newdate = '';

                var id = val.slug;

                var route1 = '{{route("settings.app.languages.edit",":id")}}';

                route1 = route1.replace(':id', id);

                if(checkDeletePermission){
                    html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label" for="is_open_' + id + '" ></label></td>';   
                }
                
                if (val.image == '' || val.image == null) {
                    var image = placeholderImage;
                } else {
                    var image = val.image;
                }
                html = html + '<td><a href="' + route1 + '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" src="' + image + '" class="rounded" style="width:50px" ></a></td>';
                
        
                html = html + '<td><a href="' + route1 + '" class="redirecttopage">' + val.title + '</a></td>';



                html = html + '<td>' + val.slug + '</td>';



                if (val.isActive) {

                    html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.slug + '" name="isActive"><span class="slider round"></span></label></td>';

                } else {

                    html = html + '<td><label class="switch"><input type="checkbox" id="' + val.slug + '" name="isActive"><span class="slider round"></span></label></td>';

                }



                html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';

                if (checkDeletePermission) {

                html = html + '<a id="' + val.slug + '" class="delete-btn" name="lang-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>';

                }

                html = html + '</tr>';

                count = count + 1;

            });

        }

        return html;

    }



    $(document).on("click", "input[name='isActive']", function (e) {

        var ischeck = $(this).is(':checked');

        var id = this.id;

        var language_key = '';

        var error = 0;

        ref.get().then(async function (snapshots) {



            snapshots = snapshots.data();

            snapshots = snapshots.list;

            if (snapshots.length) {

                languages = snapshots;

            }

            for (var key in snapshots) {

                if (snapshots[key]['slug'] == id) {

                    language_key = key;

                }

                if (snapshots[key]['isActive'] != true) {

                    error++;

                }

            }



            if (ischeck) {

                languages[language_key]['isActive'] = true;

                database.collection('settings').doc('languages').update({'list': languages}).then(function (result) {

                });

            } else {

                if (error > 0) {

                    alert("{{trans('lang.lang_error')}}");

                    $("#" + id).prop('checked', true);

                    return false;

                }

                languages[language_key]['isActive'] = false;

                database.collection('settings').doc('languages').update({'list': languages}).then(function (result) {

                });

            }

        });

    });



    $(document).on("click", "a[name='lang-delete']", function (e) {

        var id = this.id;

        var newlanguage = [];

        languages.forEach((language) => {

            if (id != language.slug) {

                delete language['id'];

                newlanguage.push(language);

            }

        });

        jQuery("#data-table_processing").show();



        database.collection('settings').doc('languages').update({'list': newlanguage}).then(function (result) {

            jQuery("#data-table_processing").hide();

            window.location.href = '{{ route("settings.app.languages") }}';

        });

    });

    $("#is_active").click(function () {

        $("#languageTable .is_open").prop('checked', $(this).prop('checked'));

    });

    $("#deleteAll").click(function () {

        if ($('#languageTable .is_open:checked').length) {

            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#data-table_processing").show();

                var deletelanguage = [];
                $('#languageTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    deletelanguage.push(dataId);
                });
                
                var newlanguage = [];
                languages.forEach((language) => {
                    if($.inArray(language.slug,deletelanguage) === -1) {
                        delete language['id'];
                        newlanguage.push(language);
                    }
                });

                database.collection('settings').doc('languages').update({'list': newlanguage}).then(function (result) {
                    jQuery("#data-table_processing").hide();
                    window.location.href = '{{ route("settings.app.languages") }}';
                });
            }

        } else {

            alert("{{trans('lang.select_delete_alert')}}");

        }
    });

</script>

@endsection

