@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor fw-bold"><i class="fa fa-map-marker me-2 text-success"></i>{{trans('lang.zone_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.zone') !!}">{{trans('lang.zone_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.zone_edit')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-pencil me-2 text-primary"></i>{{trans('lang.zone_edit')}}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-danger error_top rounded border-0 shadow-sm mb-4" style="display:none"></div>

                        <div class="row restaurant_payout_create">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.zone_name')}}<span
                                            class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="name">
                                    <div class="form-text text-muted">{{ trans("lang.zone_name_help") }}</div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <div class="form-check">
                                    <input type="checkbox" class="publish" id="publish">
                                    <label class="col-3 control-label" for="publish">{{trans('lang.status')}}</label>
                                </div>
                            </div>

                            <div class="form-hidden">
                                <input type="hidden" id="coordinates" name="coordinates" value="">
                            </div>

                        </fieldset>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="fw-bold text-success"><i class="fa fa-info-circle me-2"></i>{{trans('lang.instructions')}}</h4>
                                <p class="text-muted">{{trans('lang.instructions_help')}}</p>
                                <p><i class="fa fa-hand-pointer-o map_icons rounded shadow-sm"></i><span class="ms-2 fw-semibold">{{trans('lang.instructions_hand_tool')}}</span></p>
                                <p><i class="fa fa-plus-circle map_icons rounded shadow-sm"></i><span class="ms-2 fw-semibold">{{trans('lang.instructions_shape_tool')}}</span></p>
                                <p><i class="fa fa-trash map_icons rounded shadow-sm"></i><span class="ms-2 fw-semibold">{{trans('lang.instructions_trash_tool')}}</span></p>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <img src="{{asset('images/zone_info.gif')}}" alt="GIF" width="100%" class="rounded shadow-sm border">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" placeholder="{{ trans('lang.search_location') }}" id="search-box" class="form-control controls shadow-sm rounded-pill px-4" style="border:1px solid #e0e0e0;" />
                        <div id="map" class="rounded shadow-sm border mt-3" style="min-height: 400px;"></div>
                    </div>

                    <div class="col-sm-2">
                        <ul style="list-style: none;padding:0" class="d-flex flex-column gap-3">
                            <li>
                                <a id="select-button" href="javascript:void(0)" onclick="drawingManager.setDrawingMode(null)" class="btn btn-light rounded-circle shadow-sm border p-3 tooltipped d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Use this tool to drag the map and select your desired location">
                                    <i class="fa fa-hand-pointer-o text-success fs-5"></i>
                                </a>
                            </li>
                            <li>
                                <a id="add-button" href="javascript:void(0)" onclick="drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON)" class="btn btn-light rounded-circle shadow-sm border p-3 tooltipped d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Use this tool to highlight areas and connect the dots">
                                    <i class="fa fa-plus-circle text-primary fs-5"></i>
                                </a>
                            </li>
                            <li>
                                <a id="delete-all-button" href="javascript:void(0)" onclick="clearMap()" class="btn btn-light rounded-circle shadow-sm border p-3 tooltipped d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Use this tool to delete all selected areas">
                                    <i class="fa fa-trash text-danger fs-5"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top py-4 d-flex justify-content-end gap-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <a href="{!! route('admin.zone') !!}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold border">
                    <i class="fa fa-undo me-2"></i>{{trans('lang.cancel')}}
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold edit-form-btn">
                    <i class="fa fa-save me-2"></i> {{trans('lang.save')}}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    #map {
        height: 500px;
        width: 100%;
    }

    #panel {
        width: 200px;
        font-family: Arial, sans-serif;
        font-size: 13px;
        float: right;
        margin: 10px;
        margin-top: 100px;
    }

    #delete-button,
    #add-button,
    #delete-all-button,
    #save-button {
        margin-top: 5px;
    }

    #search-box {
        background-color: #f7f7f7;
        font-size: 15px;
        font-weight: 300;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        height: 25px;
        border: 1px solid #c7c7c7;
    }

    .map_icons {
        font-size: 24px;
        color: white;
        padding: 10px;
        margin: 5px;
        background-color: {{ isset($_COOKIE['admin_panel_color']) ? $_COOKIE['admin_panel_color'] : '#072750' }};
    }
</style>

@section('scripts')

<script>
    // Firebase has been removed from the application
    // Zone editing requires backend migration to MySQL
    
    $(document).ready(function () {
        jQuery("#overlay").hide();
    });

</script>

@endsection
    
