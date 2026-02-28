@extends('layouts.app')



@section('content')

<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-file-text-o text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px;"></i>
                {{trans('lang.document_create')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.documents') !!}" class="text-muted text-decoration-none">{{trans('lang.document_plural')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.document_create')}}</li>
            </ol>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid pl-0 pr-0">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5">
                        <h4 class="mb-0 fw-bold text-dark">{{trans('lang.document_create')}}</h4>
                        <p class="text-muted small mt-1">Create a new document request for stores or drivers.</p>
                    </div>

                    <div class="card-body p-5">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">
                            {{trans('lang.processing')}}
                        </div>
                        <div class="alert alert-danger error_top rounded-4 border-0 shadow-sm mb-4" style="display:none"></div>

                        <!-- Modern Form Layout -->
                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.title')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control title px-4 py-3 border-0 bg-light rounded-4" placeholder="Enter document title">
                                <div class="form-text text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ trans("lang.document_title_help") }}</div>
                            </div>

                            <!-- Document For -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.document_for')}} <span class="text-danger">*</span></label>
                                <select id="document_for" class="form-control px-4 py-3 border-0 bg-light rounded-4">
                                    <option value="restaurant">{{trans('lang.store')}}</option>
                                    <option value="driver">{{trans('lang.driver')}}</option>
                                </select>
                                <div class="form-text text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ trans("lang.select_document_for") }}</div>
                            </div>
                        </div>

                        <!-- Toggles Section -->
                        <hr class="my-4 border-light">
                        <h5 class="fw-bold text-dark mb-4">Required Document Sides</h5>

                        <div class="row">
                            <!-- Frontside Toggle -->
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center bg-light p-3 rounded-4 custom-switch-card" style="border: 1px solid #e9ecef;">
                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center w-100">
                                        <input class="form-check-input frontside ms-0 my-0 me-3" type="checkbox" id="frontside" style="width: 40px; height: 20px;">
                                        <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="frontside" style="cursor: pointer;">{{trans('lang.frontside')}}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Backside Toggle -->
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center bg-light p-3 rounded-4 custom-switch-card" style="border: 1px solid #e9ecef;">
                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center w-100">
                                        <input class="form-check-input backside ms-0 my-0 me-3" type="checkbox" id="backside" style="width: 40px; height: 20px;">
                                        <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="backside" style="cursor: pointer;">{{trans('lang.backside')}}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Enable Toggle -->
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center bg-light p-3 rounded-4 custom-switch-card" style="border: 1px solid #e9ecef;">
                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center w-100">
                                        <input class="form-check-input enable ms-0 my-0 me-3" type="checkbox" id="enable" style="width: 40px; height: 20px;">
                                        <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="enable" style="cursor: pointer;">{{trans('lang.active')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer / Actions -->
                    <div class="card-footer bg-light border-0 py-4 px-5 d-flex justify-content-end gap-3 rounded-bottom-4">
                        <a href="{!! route('admin.documents') !!}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="fa fa-undo me-2"></i>{{trans('lang.cancel')}}
                        </a>
                        <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold save-form-btn">
                            <i class="fa fa-save me-2"></i> {{trans('lang.save')}}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>



</div>



</div>



@endsection



@section('scripts')



<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>

<link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">



<script>



    var database = firebase.firestore();

    var alldriver = database.collection('users').where('role', '==', 'driver');

    var allvendor = database.collection('users').where('role', '==', 'vendor');



    $(document).ready(function () {



        jQuery("#data-table_processing").show();



        var id = database.collection("tmp").doc().id;



        $(".save-form-btn").click(function () {

            var title = $(".title").val();

            var document_for = $("#document_for").val();

            var isEnabled = $(".enable").is(":checked");

            var forntend = $(".frontside").is(":checked");

            var backend = $(".backside").is(":checked");



            if (title == '') {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.document_title_help')}}</p>");

                window.scrollTo(0, 0);

                return;

            } else if (forntend == false && backend == false) {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.check_atleast_one_side_of_document_from_front_or_back')}}</p>");

                window.scrollTo(0, 0);

                return;

            }

            else {

                jQuery("#data-table_processing").show();



                database.collection('documents').doc(id).set({

                    'title': title,

                    'type': document_for,

                    'frontSide': forntend,

                    'backSide': backend,

                    'enable': isEnabled,

                    'id': id,



                }).then(async function (result) {

                    if (document_for == 'driver') {

                        var enableDocIds = await getDocId('driver');

                        await alldriver.get().then(async function (snapshotsdriver) {

                            if (snapshotsdriver.docs.length > 0) {

                                var verification = await userDocVerification(enableDocIds, snapshotsdriver);

                                if (verification) {

                                    jQuery("#data-table_processing").hide();

                                    window.location.href = '{{ route("admin.documents")}}';

                                }

                            }

                        })

                    } else {

                        var enableDocIds = await getDocId('restaurant');



                        await allvendor.get().then(async function (snapshotsvendor) {



                            if (snapshotsvendor.docs.length > 0) {

                                var verification = await userDocVerification(enableDocIds, snapshotsvendor);

                                if (verification) {

                                    jQuery("#data-table_processing").hide();

                                    window.location.href = '{{ route("admin.documents")}}';

                                }

                            }



                        })

                    }





                });

            }

        });



        jQuery("#data-table_processing").hide();



    });



    async function getDocId(type) {

        var enableDocIds = [];

        await database.collection('documents').where('type', '==', type).where('enable', "==", true).get().then(async function (snapshots) {

            await snapshots.forEach((doc) => {

                enableDocIds.push(doc.data().id);

            });

        });

        return enableDocIds;

    }



    async function userDocVerification(enableDocIds, snapshots) {

        var isCompleted = false;

        var document_for = $("#document_for").val();

        await Promise.all(snapshots.docs.map(async (driver) => {

            await database.collection('documents_verify').doc(driver.id).get().then(async function (docrefSnapshot) {

                if (docrefSnapshot.data() && docrefSnapshot.data().documents.length > 0) {

                    var driverDocId = await docrefSnapshot.data().documents.filter((doc) => doc.status == 'approved').map((docData) => docData.documentId);



                    if (driverDocId.length >= enableDocIds.length) {

                        if (document_for == 'driver') {

                            await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': true, isActive: true });

                        } else {

                            await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': true });

                        }

                    } else {

                        await enableDocIds.forEach(async (docId) => {

                            if (!driverDocId.includes(docId)) {

                                if (document_for == 'driver') {

                                    await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': false, isActive: false });



                                } else {

                                    await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': false });



                                }

                            }

                        });

                    }

                } else {

                    if (document_for == 'driver') {

                        await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': false , isActive: false});



                    } else {

                        await database.collection('users').doc(driver.id).update({ 'isDocumentVerify': false });



                    }

                }

            });

            isCompleted = true;

        }));

        return isCompleted;

    }



</script>

@endsection

