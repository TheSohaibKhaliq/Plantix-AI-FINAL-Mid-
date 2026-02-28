@extends('layouts.app')

@section('content')

<div class="page-wrapper">
  <div class="row page-titles mb-4 pb-3 border-bottom">
      <div class="col-md-5 align-self-center">
          <h3 class="text-themecolor fw-bold mb-0"><i class="fa fa-money-bill-wave text-success me-2"></i>{{trans('lang.drivers_payout_plural')}}</h3>
      </div>
      <div class="col-md-7 align-self-center">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
              <li class="breadcrumb-item"><a href="{{url('/driversPayouts')}}">{{trans('lang.drivers_payout_plural')}}</a></li>
              <li class="breadcrumb-item active">{{trans('lang.drivers_payout_create')}}</li>
          </ol>
      </div>
  </div>

      <div class="container-fluid">
          <div class="row justify-content-center">
              <div class="col-md-10">
                  <div class="card border-0 shadow-sm" style="border-radius:16px;">
                      <div class="card-header bg-white border-bottom py-3">
                          <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-plus me-2 text-primary"></i>{{trans('lang.drivers_payout_create')}}</h5>
                      </div>
                      <div class="card-body p-4">
                          <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
                          <div class="error_top alert alert-danger rounded border-0 shadow-sm" style="display:none"></div>
                          <div class="row restaurant_payout_create">
                              <div class="restaurant_payout_create-inner">
                                  <fieldset>
                                      <div class="form-group row width-50">
                                          <label class="col-4 control-label fw-semibold text-muted">{{ trans('lang.drivers_payout_driver_id')}}</label>
                                          <div class="col-7">
                                              <select id="select_restaurant" class="form-control shadow-sm rounded-pill border-0" style="background:#f8f9fa;">
                                                  <option value="">{{ trans('lang.select_driver') }}</option>
                                              </select>
                                              <div class="form-text text-muted mt-2">
                                                  <i class="fa fa-info-circle me-1"></i>{{ trans("lang.drivers_payout_driver_id_help") }}
                                              </div>
                                          </div>
                                      </div>

                                      <div class="form-group row width-50">
                                          <label class="col-4 control-label fw-semibold text-muted">{{trans('lang.drivers_payout_amount')}}</label>
                                          <div class="col-7">
                                              <input type="number" class="form-control payout_amount shadow-sm rounded-pill border-0" style="background:#f8f9fa;">          
                                              <div class="form-text text-muted mt-2">
                                                  <i class="fa fa-info-circle me-1"></i>{{ trans("lang.drivers_payout_amount_placeholder") }}
                                              </div>
                                          </div>
                                      </div>

                                      <div class="form-group row width-100">
                                          <label class="col-2 control-label fw-semibold text-muted">{{ trans('lang.stores_payout_note')}}</label>
                                          <div class="col-12">
                                              <textarea type="text" rows="5" class="form-control payout_note shadow-sm rounded border-0" style="background:#f8f9fa; border-radius:12px;"></textarea>
                                          </div>
                                      </div>
                                  </fieldset>
                              </div>
                          </div>
                      </div>

                      <div class="card-footer bg-white border-top py-4 d-flex justify-content-end gap-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                          <a href="{!! route('admin.driversPayouts') !!}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                              <i class="fa fa-undo me-2"></i>{{trans('lang.cancel')}}
                          </a>
                          <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold save_restaurant_payout_btn">
                              <i class="fa fa-save me-2"></i> {{trans('lang.save')}}
                          </button>
                      </div>
                  </div>
              </div>
          </div>
      </div>    


 @endsection

@section('scripts')

<script>

var database = firebase.firestore();

async function remainingPrice(driverID){

  var paid_price = 0;
  var total_price = 0;
  var remaining = 0;
  
  await database.collection('driver_payouts').where('driverID','==',driverID).get().then( async function(payoutSnapshots){ 
       payoutSnapshots.docs.forEach((payout)=>{
          var payoutData = payout.data();
          paid_price = parseFloat(paid_price) + parseFloat(payoutData.amount);
        })

        await database.collection('restaurant_orders').where('driverID','==',driverID).where("status","in",["Order Completed"]).get().then( async function(orderSnapshots){

            orderSnapshots.docs.forEach((order)=>{
              var orderData = order.data();
                
                if(orderData.deliveryCharge!=undefined && orderData.tip_amount!=undefined){
                    var orderDataTotal = parseInt(orderData.deliveryCharge)+parseInt(orderData.tip_amount);
                    total_price = total_price + orderDataTotal;      
                  }else if(orderData.deliveryCharge!=undefined){
                    var orderDataTotal = parseInt(orderData.deliveryCharge);
                    total_price = total_price + orderDataTotal;      
                  }else if(orderData.tip_amount!=undefined){
                    var orderDataTotal = parseInt(orderData.tip_amount);
                    total_price = total_price + orderDataTotal;      
                  }
                  
            })

             remaining = total_price - paid_price;
             
        });   
   });

  return remaining; 
}

$(document).ready(function(){
    $("#data-table_processing").show();
    database.collection('users').where('role','==','driver').get().then( async function(snapshots){

      snapshots.docs.forEach((listval) => {
        var data = listval.data();
        $('#select_restaurant').append($("<option></option>")
              .attr("value", data.id)
              .text(data.firstName+' '+data.lastName));
      })

  });
  
  $("#data-table_processing").hide();

    var payoutId = "<?php echo uniqid(); ?>";

      $(".save_restaurant_payout_btn").click( async function(){
        var driverID = $("#select_restaurant").val();
        var remaining=await remainingPrice(driverID);
    
        if(remaining >0){
          var amount = parseFloat($(".payout_amount").val());
          var note = $(".payout_note").val();
          var date = new Date(Date.now());
          if(driverID != '' && $(".payout_amount").val() != ''){
            database.collection('driver_payouts').doc(payoutId).set({'driverID':driverID,'amount':amount,'note':note,'id':payoutId,'paidDate':date}).then(function(){
              
                window.location.href = "{{ route('admin.driversPayouts') }}";
            })
          }else{
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.please_enter_details')}}</p>");
          }
        }else{
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.driver_insufficient_payment_error')}}</p>");
        }

      })

  });

</script>

@endsection