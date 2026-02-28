@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
            <div class="col-md-5">
                <h3 class="text-dark fw-bold mb-0">
                    <i class="fa fa-tags text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px;"></i>
                    {{trans('lang.category_edit')}}
                </h3>
            </div>
            <div class="col-md-7 text-end">
                <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('admin.categories') !!}" class="text-muted text-decoration-none">{{trans('lang.category_plural')}}</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.category_edit')}}</li>
                </ol>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container-fluid pl-0 pr-0">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-white border-bottom py-3 px-5">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100 border-0 m-0">
                                <li role="presentation" class="nav-item">
                                    <a href="#category_information" aria-controls="description" role="tab" data-toggle="tab" class="nav-link active text-success border-success border-bottom border-2 bg-transparent fw-bold pb-3" style="border-radius:0;">
                                        <i class="fa fa-info-circle me-2"></i>{{trans('lang.category_information')}}
                                    </a>
                                </li>
                                <li role="presentation" class="nav-item">
                                    <a href="#review_attributes" aria-controls="review_attributes" role="tab" data-toggle="tab" class="nav-link text-muted border-0 bg-transparent pb-3" style="border-radius:0;">
                                        <i class="fa fa-star me-2"></i>{{trans('lang.reviewattribute_plural')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-5">
                            <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">
                                {{trans('lang.processing')}}
                            </div>
                            <div class="alert alert-danger error_top rounded-4 border-0 shadow-sm mb-4" style="display:none"></div>

                            <div class="row restaurant_payout_create" role="tabpanel">
                                <div class="restaurant_payout_create-inner tab-content w-100">
                                    <div role="tabpanel" class="tab-pane active" id="category_information">
                                        <h4 class="mb-4 fw-bold text-dark">{{trans('lang.category_edit')}}</h4>

                                        <!-- Modern Form Layout -->
                                        <div class="row">
                                            <!-- Category Name -->
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.category_name')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control cat-name px-4 py-3 border-0 bg-light rounded-4" placeholder="Enter category name">
                                                <div class="form-text text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ trans("lang.category_name_help") }} </div>
                                            </div>

                                            <!-- Category Description -->
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.category_description')}}</label>
                                                <textarea rows="5" class="category_description form-control px-4 py-3 border-0 bg-light rounded-4" id="category_description" placeholder="Enter category description"></textarea>
                                                <div class="form-text text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ trans("lang.category_description_help") }}</div>
                                            </div>

                                            <!-- Category Image -->
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.category_image')}}</label>
                                                <input type="file" id="category_image" class="form-control px-4 py-3 border-0 bg-light rounded-4">
                                                <div class="placeholder_img_thumb cat_image mt-3"></div>
                                                <div id="uploding_image"></div>
                                                <div class="form-text text-muted mt-2"><i class="fa fa-image me-1"></i>{{ trans("lang.category_image_help") }}</div>
                                            </div>
                                        </div>

                                        <!-- Toggles Section -->
                                        <hr class="my-4 border-light">
                                        <h5 class="fw-bold text-dark mb-4">Visibility Settings</h5>

                                        <div class="row">
                                            <!-- Publish Toggle -->
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex align-items-center bg-light p-3 rounded-4 custom-switch-card" style="border: 1px solid #e9ecef;">
                                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center w-100">
                                                        <input class="form-check-input item_publish ms-0 my-0 me-3 text-success custom-switch" type="checkbox" id="item_publish" style="width: 40px; height: 20px;">
                                                        <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="item_publish" style="cursor: pointer;">{{trans('lang.item_publish')}}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Show in Home Toggle -->
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex align-items-center bg-light p-3 rounded-4 custom-switch-card flex-column align-items-start" style="border: 1px solid #e9ecef;">
                                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center w-100 mb-2">
                                                        <input class="form-check-input ms-0 my-0 me-3 custom-switch text-success" type="checkbox" id="show_in_homepage" style="width: 40px; height: 20px;">
                                                        <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="show_in_homepage" style="cursor: pointer;">{{trans('lang.show_in_home')}}</label>
                                                    </div>
                                                    <div class="form-text text-muted small ms-5 ps-3">{{trans('lang.show_in_home_desc')}}<span id="forsection"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="review_attributes">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer / Actions -->
                        <div class="card-footer bg-light border-0 py-4 px-5 d-flex justify-content-end gap-3 rounded-bottom-4">
                            <a href="{!! route('admin.categories') !!}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                <i class="fa fa-undo me-2"></i>{{trans('lang.cancel')}}
                            </a>
                            <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold edit-form-btn">
                                <i class="fa fa-save me-2"></i> {{trans('lang.save')}}
                            </button>
                        </div>
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
    var ref = database.collection('vendor_categories').where("id", "==", id);
    var photo = "";
    var fileName="";
    var catImageFile="";
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    var ref_review_attributes = database.collection('review_attributes');
    var category = '';
    var storageRef = firebase.storage().ref('images');
    var storage = firebase.storage();

    placeholder.get().then(async function (snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    })

    $(document).ready(function () {

        jQuery("#data-table_processing").show();
        ref.get().then(async function (snapshots) {
            category = snapshots.docs[0].data();
            $(".cat-name").val(category.title);
            $(".category_description").val(category.description);

            if (category.photo != '' && category.photo != null) {
                photo = category.photo;
                catImageFile=category.photo;
                $(".cat_image").append('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded shadow-sm" style="width:50px" src="' + photo + '" alt="image">');
            } else {

                $(".cat_image").append('<img class="rounded shadow-sm" style="width:50px" src="' + placeholderImage + '" alt="image">');
            }

            if (category.publish) {
                $("#item_publish").prop('checked', true);
            }
            
            if (category.show_in_homepage) {
                $("#show_in_homepage").prop('checked', true);
            }


            jQuery("#data-table_processing").hide();
        })

        ref_review_attributes.get().then(async function (snapshots) {
            var ra_html = '';
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                ra_html += '<div class="form-check width-100" >';
                var checked = $.inArray(data.id, category.review_attributes) !== -1 ? 'checked' : '';
                ra_html += '<input type="checkbox" id="review_attribute_' + data.id + '" value="' + data.id + '" ' + checked + '>';
                ra_html += '<label class="col-3 control-label" for="review_attribute_' + data.id + '">' + data.title + '</label>';
                ra_html += '</div>';
            })
            $('#review_attributes').html(ra_html);
        })


        $(".edit-form-btn").click(async function () {

            var title = $(".cat-name").val();
            var description = $(".category_description").val();
            var item_publish = $("#item_publish").is(":checked");
            var show_in_homepage = $("#show_in_homepage").is(":checked");

            var review_attributes = [];
            $('#review_attributes input').each(function () {
                if ($(this).is(':checked')) {
                    review_attributes.push($(this).val());
                }
            });

            if (title == '') {

                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_cat_title_error')}}</p>");
                window.scrollTo(0, 0);
            } else {

                var count_vendor_categories = 0;
                if (show_in_homepage) {

                    await database.collection('vendor_categories').where('show_in_homepage', "==", true).where("id", "!=", id).get().then(async function (snapshots) {

                        count_vendor_categories = snapshots.docs.length;

                    });
                }

                if (count_vendor_categories >= 5) {
                    alert("Already 5 categories are active for show in homepage..");
                    return false;
                    
                } else {

                jQuery("#data-table_processing").show();
                storeImageData().then(IMG => {
                database.collection('vendor_categories').doc(id).update({
                    'title': title,
                    'description': description,
                    'photo': IMG,
                    'review_attributes': review_attributes,
                    'publish': item_publish,
                    'show_in_homepage': show_in_homepage,
                }).then(function (result) {
                    jQuery("#data-table_processing").hide();
                    window.location.href = '{{ route("admin.categories")}}';
                });
                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });

            }
            }

        });

    });


    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {

                var filePayload = e.target.result;
                var val = $('#category_image').val().toLowerCase();
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = $('#category_image').val().replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                var uploadTask = storageRef.child(filename).put(theFile);
                uploadTask.on('state_changed', function (snapshot) {
                    var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                }, function (error) {
                }, function () {
                    uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
                        jQuery("#uploding_image").text("Upload is completed");
                        photo = downloadURL;
                        $(".cat_image").empty();
                        $(".cat_image").append('<img class="rounded shadow-sm" style="width:50px" src="' + photo + '" alt="image">');

                    });
                });

            };
        })(f);
        reader.readAsDataURL(f);
    }

    async function storeImageData() {
        var newPhoto = '';
        try {
            if (catImageFile != "" && photo != catImageFile) {
                var catOldImageUrlRef = await storage.refFromURL(catImageFile);
                imageBucket = catOldImageUrlRef.bucket; 
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if (imageBucket == envBucket) {
                    await catOldImageUrlRef.delete().then(() => {
                        console.log("Old file deleted!")
                    }).catch((error) => {
                        console.log("ERR File delete ===", error);
                    });
                } else {
                    console.log('Bucket not matched');  
                }
            } 
            if (photo != catImageFile) {
                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto = downloadURL;
                photo = downloadURL;

            } else {
                newPhoto = photo;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }  

    //upload image with compression
    $("#category_image").resizeImg({
        
        callback: function(base64str) {
            
            var val = $('#category_image').val().toLowerCase();
            var ext = val.split('.')[1];
            var docName = val.split('fakepath')[1];
            var filename = $('#category_image').val().replace(/C:\\fakepath\\/i, '')
            var timestamp = Number(new Date());
            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
            photo=base64str;
            fileName=filename;
            $(".cat_image").empty();
            $(".cat_image").append('<img class="rounded shadow-sm" style="width:50px" src="' + photo + '" alt="image">');
            $("#category_image").val('');

        }
    });

</script>
@endsection