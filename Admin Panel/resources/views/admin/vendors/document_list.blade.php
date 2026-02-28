@extends('layouts.app')



@section('content')

<div class="page-wrapper">

        <div class="row page-titles">



            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor restaurantTitle">{{trans('lang.vendor_document_details')}}</h3>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                    <li class="breadcrumb-item"><a href="{!! route('vendors') !!}">{{trans('lang.vendor')}}</a></li>

                    <li class="breadcrumb-item active">{{trans('lang.vendor_document_details')}}</li>

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

                                    <a class="nav-link active vendor-name"

                                       href="{!! url()->current() !!}">{{trans('lang.vendor_document_details')}}</a>

                                </li>

                            </ul>

                        </div>

                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"

                                 style="display: none;">{{trans('lang.processing')}}

                            </div>



                            <div class="table-responsive m-t-10 doc-body"></div>

                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"

                                 aria-labelledby="exampleModalLabel" aria-hidden="true">

                                <div class="modal-dialog" role="document" style="max-width: 50%;">

                                    <div class="modal-content">



                                        <div class="modal-header">

                                            <button type="button" class="close"

                                                    data-dismiss="modal"

                                                    aria-label="Close">

                                                <span aria-hidden="true">&times;</span>

                                            </button>

                                        </div>



                                        <div class="modal-body">

                                            <div class="form-group">

                                                <embed id="docImage"

                                                       src=""

                                                       frameBorder="0"

                                                       scrolling="auto"

                                                       height="100%"

                                                       width="100%"

                                                       style="height: 540px;"

                                                ></embed>

                                            </div>



                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-secondary"

                                                        data-dismiss="modal">{{trans('lang.close')}}</button>

                                            </div>

                                        </div>

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



@section('scripts')z



<script>
    // Firebase has been removed from the application
    // Document verification functionality requires backend migration to MySQL
    
    $(document).ready(function () {
        jQuery("#data-table_processing").hide();
        // Placeholder: Document listing from Firebase is no longer available
        // This page requires migration to use Laravel backend for document management
    });

</script>

@endsection

