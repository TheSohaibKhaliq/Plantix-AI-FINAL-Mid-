@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.zone_plural')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('zone') !!}">{{trans('lang.zone_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.zone_edit')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="error_top" style="display:none"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">

                        <fieldset>

                            <legend>{{trans('lang.zone_edit')}}</legend>

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
                                <h4>{{trans('lang.instructions')}}</h4>
                                <p>{{trans('lang.instructions_help')}}</p>
                                <p><i class="fa fa-hand-pointer-o map_icons"></i>{{trans('lang.instructions_hand_tool')}}</p>
                                <p><i class="fa fa-plus-circle map_icons"></i>{{trans('lang.instructions_shape_tool')}}</p>
                                <p><i class="fa fa-trash map_icons"></i>{{trans('lang.instructions_trash_tool')}}</p>
                            </div>
                            <div class="col-sm-12">
                                <img src="{{asset('images/zone_info.gif')}}" alt="GIF" width="100%">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" placeholder="{{ trans('lang.search_location') }}" id="search-box"
                            class="form-control controls" />
                        <div id="map"></div>
                    </div>

                    <div class="col-sm-2">
                        <ul style="list-style: none;padding:0">
                            <li>
                                <a id="select-button" href="javascript:void(0)"
                                    onclick="drawingManager.setDrawingMode(null)"
                                    class="btn-floating zone-add-btn btn-large waves-effect waves-light tooltipped"
                                    title="Use this tool to drag the map and select your desired location">
                                    <i class="fa fa-hand-pointer-o map_icons"></i>
                                </a>
                            </li>
                            <li>
                                <a id="add-button" href="javascript:void(0)"
                                    onclick="drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON)"
                                    class="btn-floating zone-add-btn btn-large waves-effect waves-light tooltipped"
                                    title="Use this tool to highlight areas and connect the dots">
                                    <i class="fa fa-plus-circle map_icons"></i>
                                </a>
                            </li>
                            <li>
                                <a id="delete-all-button" href="javascript:void(0)" onclick="clearMap()"
                                    class="btn-floating zone-delete-all-btn btn-large waves-effect waves-light tooltipped"
                                    title="Use this tool to delete all selected areas">
                                    <i class="fa fa-trash map_icons"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
                
                <div class="form-group col-12 text-center btm-btn">

                    <button type="button" class="btn btn-primary edit-form-btn">
                        <i class="fa fa-save"></i> {{trans('lang.save')}}
                    </button>

                    <a href="{!! route('zone') !!}" class="btn btn-default">
                        <i class="fa fa-undo"></i>{{trans('lang.cancel')}}
                    </a>

                </div>

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
    
