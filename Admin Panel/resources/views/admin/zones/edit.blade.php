@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-map-marker text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px; width: 44px; text-align: center;"></i>
                {{trans('lang.zone_edit')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('admin.zone') !!}" class="text-muted text-decoration-none">{{trans('lang.zone_plural')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.zone_edit')}}</li>
            </ol>
        </div>
    </div>
    <!-- Main Content -->
    <div class="container-fluid pl-0 pr-0">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5">
                        <h4 class="mb-0 fw-bold text-dark">{{trans('lang.zone_edit')}}</h4>
                        <p class="text-muted small mt-1">Update geographic zone settings and boundaries.</p>
                    </div>

                    <div class="card-body p-5">
                        <div class="alert alert-danger error_top rounded-4 border-0 shadow-sm mb-4" style="display:none"></div>

                        <div class="row mt-2">
                            <!-- Zone Information Section -->
                            <div class="col-md-12 mb-5">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.zone_name')}} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control px-4 py-3 border-0 bg-light rounded-4" id="name" placeholder="Enter zone name">
                                        <div class="form-text text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ trans("lang.zone_name_help") }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark mb-2">{{trans('lang.status')}}</label>
                                        <div class="d-flex align-items-center bg-light p-3 rounded-4" style="height: 52px; border: 1px solid #eee;">
                                            <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                                                <input class="form-check-input publish ms-0 my-0 me-3 custom-switch text-success" type="checkbox" id="publish" style="width: 40px; height: 20px;">
                                                <label class="form-check-label fw-semibold text-dark mb-0 ms-2" for="publish" style="cursor: pointer;">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Map Section -->
                            <div class="col-md-12 mt-4 px-0">
                                <div class="row g-4 m-0">
                                    <div class="col-lg-4 col-md-5">
                                        <div class="bg-light p-4 rounded-4 h-100 shadow-sm border border-white">
                                            <h5 class="fw-bold text-dark mb-4"><i class="fa fa-info-circle text-success me-2"></i>{{trans('lang.instructions')}}</h5>
                                            <p class="text-muted small mb-4">{{trans('lang.instructions_help')}}</p>
                                            
                                            <div class="d-flex flex-column gap-3">
                                                <div class="d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border-start border-success border-4">
                                                    <i class="fa fa-hand-pointer-o text-success fs-4 me-3"></i>
                                                    <span class="fw-semibold text-dark">{{trans('lang.instructions_hand_tool')}}</span>
                                                </div>
                                                <div class="d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border-start border-primary border-4">
                                                    <i class="fa fa-plus-circle text-primary fs-4 me-3"></i>
                                                    <span class="fw-semibold text-dark">{{trans('lang.instructions_shape_tool')}}</span>
                                                </div>
                                                <div class="d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border-start border-danger border-4">
                                                    <i class="fa fa-trash text-danger fs-4 me-3"></i>
                                                    <span class="fw-semibold text-dark">{{trans('lang.instructions_trash_tool')}}</span>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-2">
                                                <img src="{{asset('images/zone_info.gif')}}" alt="GIF" width="100%" class="rounded-4 shadow-sm border border-white">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8 col-md-7">
                                        <div class="position-relative h-100 shadow-sm rounded-4 overflow-hidden border">
                                            <div class="position-absolute top-0 start-0 w-100 px-4 py-3" style="z-index: 5;">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-0 ps-4 rounded-start-pill"><i class="fa fa-search text-muted"></i></span>
                                                    <input type="text" placeholder="{{ trans('lang.search_location') }}" id="search-box" class="form-control border-0 py-3 pe-4 rounded-end-pill shadow-none" />
                                                </div>
                                            </div>
                                            
                                            <div id="map" style="min-height: 500px; width: 100%;"></div>

                                            <!-- Floating Toolbar -->
                                            <div class="position-absolute bottom-0 end-0 p-4 mb-4" style="z-index: 5;">
                                                <ul class="d-flex gap-2 p-3 bg-white rounded-pill shadow-lg list-unstyled m-0 border">
                                                    <li>
                                                        <button type="button" id="select-button" onclick="drawingManager.setDrawingMode(null)" class="btn btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center hover-card" style="width: 48px; height: 48px;" title="Selection Tool">
                                                            <i class="fa fa-hand-pointer-o text-success fs-5"></i>
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" id="add-button" onclick="drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON)" class="btn btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center hover-card" style="width: 48px; height: 48px;" title="Draw Tool">
                                                            <i class="fa fa-plus-circle text-primary fs-5"></i>
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" id="delete-all-button" onclick="clearMap()" class="btn btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center hover-card" style="width: 48px; height: 48px;" title="Clear Map">
                                                            <i class="fa fa-trash text-danger fs-5"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-hidden">
                            <input type="hidden" id="coordinates" name="coordinates" value="">
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="card-footer bg-light border-0 py-4 px-5 d-flex justify-content-end gap-3 rounded-bottom-4">
                        <a href="{!! route('admin.zone') !!}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
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
    
