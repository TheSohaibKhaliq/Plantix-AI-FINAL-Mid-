@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-bell text-success me-2"></i>{{trans('lang.notification')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('notification') }}">{{trans('lang.notifications')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.notification')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-plus me-2 text-primary"></i>{{trans('lang.notification')}}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                            {{trans('lang.processing')}}
                        </div>
                        <div class="error_top alert alert-danger rounded border-0 shadow-sm" style="display:none"></div>
                        <div class="success_top alert alert-success rounded border-0 shadow-sm" style="display:none"></div>

                        <div class="row restaurant_payout_create">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>
                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label fw-semibold text-muted">{{trans('lang.subject')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control shadow-sm rounded-pill border-0" style="background:#f8f9fa;" id="subject">
                                        </div>
                                    </div>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label fw-semibold text-muted">{{trans('lang.message')}}</label>
                                        <div class="col-7">
                                            <textarea class="form-control shadow-sm rounded border-0" style="background:#f8f9fa; border-radius:12px;" rows="5" id="message"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50 mt-3">
                                        <label class="col-3 control-label fw-semibold text-muted">{{trans('lang.send_to')}}</label>
                                        <div class="col-7">
                                            <select id='role' class="form-control shadow-sm rounded-pill border-0" style="background:#f8f9fa;">
                                                <option value="vendor">{{trans('lang.vendor')}}</option>
                                                <option value="customer">{{trans('lang.customer')}}</option>
                                                <option value="driver">{{trans('lang.driver')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
                    <div class="card-footer bg-white border-top py-4 d-flex justify-content-end gap-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <a href="{{url('/dashboard')}}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                            <i class="fa fa-undo me-2"></i>{{ trans('lang.cancel')}}
                        </a>
                        <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold save-form-btn">
                            <i class="fa fa-save me-2"></i>{{ trans('lang.send')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

<script>

var id = "<?php echo $id;?>";
var database = firebase.firestore();
var ref = database.collection('notifications').where("id", "==", id);
var users = database.collection('users').where("fcmToken", "!=", "");
var pagesize = 20;
var start = '';

$(document).ready(function () {

    ref.get().then(async function (snapshots) {
        if (snapshots.docs.length) {
            var np = snapshots.docs[0].data();
            $("#message").val(np.message);
            $("#role").val(np.role);
        }
    });

    $(".save-form-btn").click(async function () {

        $(".success_top").hide();
        $(".error_top").hide();
        var message = $("#message").val();
        var subject = $("#subject").val();
        var role = $("#role").val();

        if (subject == "") {

            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.please_enter_subject')}}</p>");
            window.scrollTo(0, 0);
            return false;

        } else if (message == "") {

            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.please_enter_message')}}</p>");
            window.scrollTo(0, 0);
            return false;

        }else{

            jQuery("#data-table_processing").show();

            $.ajax({
                method: 'POST',
                dataType: "json",
                url: '<?php echo route('admin.broadcastnotification'); ?>',
                data: {
                    'role': role,
                    'subject': subject,
                    'message': message,
                    '_token': '<?php echo csrf_token() ?>'
                },
                success:function(response) {

                    jQuery("#data-table_processing").hide();
                    if(response.success == true){
                        var id = database.collection("tmp").doc().id;
                        database.collection('notifications').doc(id).set({
                            id: id,
                            message: message,
                            subject: subject,
                            role: role,
                            createdAt: firebase.firestore.FieldValue.serverTimestamp()
                        });
                        $(".success_top").show();
                        $(".success_top").html("");
                        $(".success_top").append("<p>"+response.message+"</p>");
                        window.scrollTo(0, 0);
                        setTimeout(function(){
                            window.location.href = '{{ route("admin.notification")}}';
                        },3000);
                    }else{
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>"+response.message+"</p>");
                        window.scrollTo(0, 0);
                    }
                }
            });
        }

    });

});

</script>

@endsection
