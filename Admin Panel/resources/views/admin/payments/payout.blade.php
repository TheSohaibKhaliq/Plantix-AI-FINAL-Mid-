@extends('layouts.app')

@section('content')
	<div class="page-wrapper">
    <div class="row page-titles mb-4 pb-3 border-bottom">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold mb-0"><i class="fa fa-store text-success me-2"></i>{{trans('lang.store_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href= "{!! route('admin.stores') !!}" >{{trans('lang.store_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.store_edit')}}</li>
            </ol>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-header bg-white border-bottom py-3">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100 border-0">
      			<li class="nav-item border-0">
      					<a class="nav-link fw-bold text-muted border-0 pb-3" href="{{route('admin.stores.view',$id)}}">Basic</a>
      			</li>
      			<li class="nav-item border-0">
      					<a class="nav-link fw-bold text-muted border-0 pb-3" href="{{route('admin.products.index')}}">Foods</a>
      			</li>
      			<li class="nav-item border-0">
      					<a class="nav-link fw-bold text-muted border-0 pb-3" href="{{route('admin.orders.index')}}">Orders</a>
      			</li>
      			<li class="nav-item border-0">
      					<a class="nav-link fw-bold text-muted border-0 pb-3" href="{{route('admin.stores.promos',$id)}}">Promos</a>
      			</li>
      			<li class="nav-item border-0 active">
      					<a class="nav-link active fw-bold text-success border-0 pb-3" href="#">Payouts</a>
      			</li>
      		</ul>
        </div>
        <div class="card-body p-4">
      	<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">Processing...</div>

      <div class="row daes-top-sec">
      				<div class="col-lg-4 col-md-6">

                  <div class="card shadow-sm border-0" style="border-radius:16px;">
                      <div class="flex-row">
                          <div class="p-3 bg-success text-center" style="border-top-left-radius:16px; border-bottom-left-radius:16px; width:80px; display:flex; align-items:center; justify-content:center;">
                              <h3 class="text-white box m-b-0"><i class="mdi mdi-bank"></i></h3></div>
                          <div class="align-self-center px-4 py-3">
                              <h3 class="m-b-0 text-success fw-bold" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0 fw-semibold">Total Earning</h5>
                          </div>
                      </div>
                  </div>

            </div>

            <div class="col-lg-4 col-md-6">

                  <div class="card shadow-sm border-0" style="border-radius:16px;">
                      <div class="flex-row">
                          <div class="p-3 bg-info text-center" style="border-top-left-radius:16px; border-bottom-left-radius:16px; width:80px; display:flex; align-items:center; justify-content:center;">
                              <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3></div>
                          <div class="align-self-center px-4 py-3">
                              <h3 class="m-b-0 text-info fw-bold" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0 fw-semibold">Total Payment</h5>
                          </div>
                      </div>
                  </div>

            </div>

            <div class="col-lg-4 col-md-6">

                  <div class="card shadow-sm border-0" style="border-radius:16px;">
                      <div class="flex-row">
                          <div class="p-3 bg-warning text-center" style="border-top-left-radius:16px; border-bottom-left-radius:16px; width:80px; display:flex; align-items:center; justify-content:center;">
                              <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3></div>
                          <div class="align-self-center px-4 py-3">
                              <h3 class="m-b-0 text-warning fw-bold" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0 fw-semibold">Remaining Payment</h5>
                          </div>
                      </div>
                  </div>

            </div>

      </div>
      <div class="row restaurant_payout_create mt-4">
        <div class="restaurant_payout_create-inner">
          <fieldset>
             <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-info-circle me-2 text-primary"></i>{{trans('lang.store_details')}}</h4>
            
              <div class="form-group row width-50">
                <label class="col-3 control-label fw-semibold text-muted">{{trans('lang.store_name')}}</label>
               	<div class="col-7">
                	<input type="text" class="form-control restaurant_name shadow-sm rounded-pill border-0" style="background:#f8f9fa;">
                	<div class="form-text text-muted mt-2">
                  	{{ trans("lang.store_name_help") }}
                	</div>
              	</div>
            	</div>

      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.store_cuisines')}}</label>
        			<div class="col-9">
        				<select id='restaurant_cuisines' class="form-control">
        					<option value="">Select Cuisines</option>
        				</select>
        				<div class="form-text text-muted">
                  			{{ trans("lang.store_cuisines_help") }}
        				</div>
      				</div>
      			</div>

            <div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.store_phone')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_phone">
        				<div class="form-text text-muted">
                  	{{ trans("lang.store_phone_help") }}
        				</div>
      				</div>
      			</div>

            <div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.store_address')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_address">
        				<div class="form-text text-muted">
                  			{{ trans("lang.store_address_help") }}
        				</div>
      				</div>
      			</div>
      

      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.store_latitude')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_latitude">
        				<div class="form-text text-muted">
                  			{{ trans("lang.store_latitude_help") }}
        				</div>
      				</div>

      			</div>

      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.store_longitude')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_longitude">
        				<div class="form-text text-muted">
                  			{{ trans("lang.store_longitude_help") }}
        				</div>
      				</div>
      			</div>
          

          <div class="form-group row">
            <label class="col-3 control-label ">{{trans('lang.store_description')}}</label>
              <div class="col-7">
                <textarea rows="7" class="restaurant_description form-control" id="restaurant_description"></textarea>
              </div>
          </div>
      
          <div class="form-group row">
            <label class="col-3 control-label">{{trans('lang.store_image')}}</label>
            <div class="col-9">
              <input type="file" onChange="handleFileSelect(event)">
              <div id="uploding_image"></div>
              <div class="form-text text-muted">
                {{ trans("lang.store_image_help") }}
              </div>
            </div>
          </div>

      </fieldset>

      <fieldset class="mt-4">
        <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-user-shield me-2 text-primary"></i>{{trans('lang.admin_area')}}</h4>

        <div class="form-group row">
          <label class="col-3 control-label fw-semibold text-muted">{{trans('lang.store_users')}}</label>
          <div class="col-9">
            <input type="text" class="form-control restaurant_owners shadow-sm rounded-pill border-0" style="background:#f8f9fa; max-width:300px;" disabled>
          </div>
        </div>
      </fieldset>

    </div>
  </div>
</div>
        <div class="card-footer bg-white border-top py-4 d-flex justify-content-end gap-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <a href="{!! route('admin.stores') !!}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                <i class="fa fa-undo me-2"></i>{{trans('lang.cancel')}}
            </a>
            <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold save_restaurant_btn">
                <i class="fa fa-save me-2"></i> {{trans('lang.save')}}
            </button>
        </div>

    </div>
  </div>
</div>


 @endsection

@section('scripts')

 <script>
	var id = "<?php echo $id;?>";
	var database = firebase.firestore();
	var ref = database.collection('vendors').where("id","==",id);
	var photo ="";
	var restaurantOwnerId = "";
	var restaurantOwnerOnline = false;
	$(document).ready(function(){
  		jQuery("#data-table_processing").show();
  		ref.get().then( async function(snapshots){
			var restaurant = snapshots.docs[0].data();
			$(".restaurant_name").val(restaurant.title);
			$(".restaurant_cuisines").val(restaurant.filters.Cuisine);
			$(".restaurant_address").val(restaurant.location);
			$(".restaurant_latitude").val(restaurant.latitude);
			$(".restaurant_longitude").val(restaurant.longitude);
			$(".restaurant_description").val(restaurant.description);

			restaurantOwnerOnline = restaurant.isActive;
	   		photo = restaurant.photo;
	    	restaurantOwnerId = restaurant.author;
	 		await database.collection('users').where("id","==",restaurant.author).get().then( async function(snapshots){
	   			snapshots.docs.forEach((listval) => {
	            var user = listval.data();
				$(".restaurant_owners").val(user.firstName+" "+user.lastName);
	          })
			});

			await database.collection('vendor_categories').get().then( async function(snapshots){
	   			snapshots.docs.forEach((listval) => {
	            	var data = listval.data();
	            	if(data.id == restaurant.categoryID){
	                	$('#restaurant_cuisines').append($("<option selected></option>")
	                    	.attr("value", data.id)
	                    	.text(data.title));
	            	}else{
	                	$('#restaurant_cuisines').append($("<option></option>")
	                    	.attr("value", data.id)
	                    	.text(data.title));
			    	}
	          	})

			});  
	    
	    	if(restaurant.hasOwnProperty('phonenumber')){
	     		$(".restaurant_phone").val(restaurant.phonenumber);
	    	}
	  		jQuery("#data-table_processing").hide();
  		})


  
		$(".save_restaurant_btn").click(function(){
		  	var restaurantname = $(".restaurant_name").val();
			var cuisines = $("#restaurant_cuisines option:selected").val();
			var address = $(".restaurant_address").val();	
			var latitude = parseFloat($(".restaurant_latitude").val());
			var longitude = parseFloat($(".restaurant_longitude").val());
			var description = $(".restaurant_description").val();
			var phonenumber = $(".restaurant_phone").val();
			var categoryTitle = $( "#restaurant_cuisines option:selected" ).text();

		    database.collection('vendors').doc(id).update({'title':restaurantname,'description':description,'latitude':latitude,
		      'longitude':longitude,'location':address,'photo':photo,'categoryID':cuisines,'phonenumber':phonenumber,'categoryTitle':categoryTitle}).then(function(result) {
		                window.location.href = '{{ route("admin.stores")}}';
		             }); 
		})

	})

	var storageRef = firebase.storage().ref('images');
	function handleFileSelect(evt) {
  		var f = evt.target.files[0];
  		var reader = new FileReader();
	  	reader.onload = (function(theFile) {
		    return function(e) {
		        
		      var filePayload = e.target.result;
		    	var val =f.name;       
		      var ext=val.split('.')[1];
		      var docName=val.split('fakepath')[1];
		      var filename = (f.name).replace(/C:\\fakepath\\/i, '')

		      var timestamp = Number(new Date());      
		      var uploadTask = storageRef.child(filename).put(theFile);
		      
		      uploadTask.on('state_changed', function(snapshot){

		      var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
		      console.log('Upload is ' + progress + '% done');
		      jQuery("#uploding_image").text("Image is uploading...");
		    }, function(error) {
		    }, function() {
		        uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
		            jQuery("#uploding_image").text("Upload is completed");
		            photo = downloadURL;

		      });   
		    });
	    
	    };
	  })(f);
  reader.readAsDataURL(f);
}   

</script>
@endsection