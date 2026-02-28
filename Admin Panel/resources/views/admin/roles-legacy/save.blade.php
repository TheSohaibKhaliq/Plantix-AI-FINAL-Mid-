@extends('layouts.app')



@section('content')

<div class="page-wrapper">

    <div class="row page-titles mb-4 pb-3 border-bottom align-items-center">
        <div class="col-md-5">
            <h3 class="text-dark fw-bold mb-0">
                <i class="fa fa-plus-circle text-success me-2" style="background: rgba(40, 167, 69, 0.1); padding: 12px; border-radius: 12px; width: 44px; text-align: center;"></i>
                {{trans('lang.create_role')}}
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <ol class="breadcrumb d-inline-flex bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}" class="text-muted text-decoration-none">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('role') }}" class="text-muted text-decoration-none">{{trans('lang.role_plural')}}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold">{{trans('lang.create_role')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid pl-0 pr-0">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-5">
                        <h4 class="mb-0 fw-bold text-dark">{{trans('lang.role_details')}}</h4>
                        <p class="text-muted small mt-1">Configure role name and assign specific modular permissions.</p>
                    </div>

                    <div class="card-body p-5">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default text-success" style="display: none;">
                            {{trans('lang.processing')}}
                        </div>
                        
                        <div class="alert alert-danger error_top rounded-4 border-0 shadow-sm mb-4 fw-semibold" style="display:none"></div>
                        <div class="alert alert-success success_top rounded-4 border-0 shadow-sm mb-4 fw-semibold" style="display:none"></div>

                        <form action="{{route('admin.role.store')}}" method="post" id="roleForm">
                            @csrf
                            
                            <div class="mb-5 bg-light p-5 rounded-4 border border-white shadow-sm">
                                <div class="row align-items-end">
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold text-dark mb-2">{{trans('lang.name')}} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control px-4 py-3 border-0 bg-white shadow-sm rounded-4" id="name" name="name" placeholder="e.g. Content Manager">
                                        <div class="form-text mt-2 ms-1 text-muted small">Unique name to identify this set of permissions.</div>
                                    </div>
                                    <div class="col-md-5 text-md-end mt-4 mt-md-0">
                                        <div class="bg-white p-3 rounded-4 shadow-sm d-inline-block border">
                                            <div class="form-check form-switch m-0 d-flex align-items-center gap-3">
                                                <input class="form-check-input ms-0" type="checkbox" name="all_permission" id="all_permission" style="width: 3.5em; height: 1.8em; cursor: pointer;">
                                                <label class="form-check-label fw-bold text-success cursor-pointer mb-0 pt-1" for="all_permission">{{trans('lang.all_permissions')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="permission-matrix mt-4">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="fa fa-th-list me-2 text-success"></i>Modular Permissions Matrix</h5>
                                <div class="table-responsive rounded-4 border overflow-hidden">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="fw-bold text-uppercase text-dark small py-3 ps-4" style="width: 200px;">Resource / Menu</th>
                                                <th class="fw-bold text-uppercase text-dark small py-3 pe-4 text-center">Action Permissions Control</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.god_eye')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="god-eye" value="map" name="god-eye[]"

                                                            class="permission">

                                                        <label class="control-label2"

                                                            for="god-eye">{{trans('lang.view')}}</label>

                                                    </td>

                                                </tr>

                                                 <tr>

                                                    <td>

                                                        <strong>{{trans('lang.zone')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="zone-list" value="zone.list"

                                                            name="zone[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="zone-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="zone-create" value="zone.create"

                                                            name="zone[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="zone-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="zone-edit" value="zone.edit"

                                                            name="zone[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="zone-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="zone-delete" value="zone.delete"

                                                            name="zone[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="zone-delete">{{trans('lang.delete')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.role_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="role-list" value="role.index"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="role-save" value="role.save"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-save">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="role-store" value="role.store"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-store">{{trans('lang.store')}}</label>



                                                        <input type="checkbox" id="role-edit" value="role.edit"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-edit">{{trans('lang.edit')}}</label>



                                                        <input type="checkbox" id="role-update" value="role.update"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-update">{{trans('lang.update')}}</label>



                                                        <input type="checkbox" id="role-delete" value="role.delete"

                                                            name="roles[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="role-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.admin_plural')}}</strong>



                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="admin-list" value="admin.users"

                                                            name="admins[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="admin-create"

                                                            value="admin.users.create" name="admins[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="admin-store"

                                                            value="admin.users.store" name="admins[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-store">{{trans('lang.store')}}</label>



                                                        <input type="checkbox" id="admin-edit" value="admin.users.edit"

                                                            name="admins[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-edit">{{trans('lang.edit')}}</label>



                                                        <input type="checkbox" id="admin-update"

                                                            value="admin.users.update" name="admins[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-update">{{trans('lang.update')}}</label>



                                                        <input type="checkbox" id="admin-delete"

                                                            value="admin.users.delete" name="admins[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.user_customer')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="user-list" value="users"

                                                            name="users[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="user-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="user-create" value="users.create"

                                                            name="users[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="user-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="user-edit" value="users.edit"

                                                            name="users[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="user-edit">{{trans('lang.edit')}}</label>



                                                        <input type="checkbox" id="user-view" value="users.view"

                                                            name="users[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="user-view">{{trans('lang.view')}}</label>



                                                        <input type="checkbox" id="user-delete" value="user.delete"

                                                            name="user[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="user-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.owner_vendor')}}</strong>



                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="vendors-list" value="vendors"

                                                            name="vendors[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="vendors-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="vendors-delete" value="vendors.delete"

                                                        name="vendors[]" class="permission">

                                                        <label class=" control-label2" for="vendors-delete">{{trans('lang.delete')}}</label>

                                                       



                                                    </td>

                                                </tr>



                                            <tr>

                                                <td>

                                                    <strong>{{trans('lang.approved_vendors')}}</strong>

                                                </td> 

                                                <td>

                                                    <input type="checkbox" id="approved-vendors-list" value="approve.vendors.list"

                                                        name="approve_vendors[]" class="permission">  

                                                    <label class="contol-label2"

                                                        for="approved-vendors-list">{{trans('lang.list')}}</label>

                                                    <input type="checkbox" id="approved-vendors-delete" value="approve.vendors.delete"

                                                        name="approve_vendors[]" class="permission">

                                                    <label class=" control-label2" for="approved-vendors-delete">{{trans('lang.delete')}}</label>



                                                </td>

                                            </tr>

                                              <tr>

                                                <td>

                                                    <strong>{{trans('lang.approval_pending_vendors')}}</strong>



                                                </td> 

                                                <td>

                                                    <input type="checkbox" id="pending-vendors-list" value="pending.vendors.list"

                                                        name="pending_vendors[]" class="permission">

                                                    <label class="contol-label2"

                                                        for="pending-vendors-list">{{trans('lang.list')}}</label>

                                                    <input type="checkbox" id="pending-vendors-delete" value="pending.vendors.delete"

                                                        name="pending_vendors[]" class="permission">

                                                    <label class=" control-label2" for="pending-vendors-delete">{{trans('lang.delete')}}</label>



                                                </td>

                                            </tr>

                                               

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.vendor_document_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="vendor-document-list" value="vendor.document.list" name="vendors-document[]" class="permission">



                                                        <label class=" control-label2" for="vendor-document-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="vendor-document-edit" value="vendor.document.edit" name="vendors-document[]" class="permission" >



                                                        <label class=" control-label2" for="vendor-document-edit">{{trans('lang.edit')}}</label>



                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.store_plural')}}</strong>



                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="stores-list" value="stores"

                                                            name="stores[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="stores-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="stores-create"

                                                            value="stores.create" name="stores[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="stores-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="stores-edit"

                                                            value="stores.edit" name="stores[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="stores-edit">{{trans('lang.edit')}}</label>



                                                        <input type="checkbox" id="stores-view"

                                                            value="stores.view" name="stores[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="stores-view">{{trans('lang.view')}}</label>

                                                        <input type="checkbox" id="store-delete" value="store.delete"

                                                            name="store[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="store-delete">{{trans('lang.delete')}}</label>





                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.driver_plural')}}</strong>



                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="drivers-list" value="drivers"

                                                            name="drivers[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="drivers-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="drivers-create"

                                                            value="drivers.create" name="drivers[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="drivers-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="drivers-edit" value="drivers.edit"

                                                            name="drivers[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="drivers-edit">{{trans('lang.edit')}}</label>



                                                        <input type="checkbox" id="drivers-view" value="drivers.view"

                                                            name="drivers[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="drivers-view">{{trans('lang.view')}}</label>

                                                         <input type="checkbox" id="driver-delete" value="driver.delete"

                                                            name="driver[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="driver-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>



                                                 <tr>

                                                <td>

                                                    <strong>{{trans('lang.approved_drivers')}}</strong>

                                                </td> 

                                                <td>

                                                    <input type="checkbox" id="drivers-list-approved" value="approve.driver.list"

                                                        name="approve_drivers[]" class="permission">  

                                                    <label class="contol-label2"

                                                        for="drivers-list-approved">{{trans('lang.list')}}</label>

                                                    <input type="checkbox" id="drivers-approved-delete" value="approve.driver.delete"

                                                        name="approve_drivers[]" class="permission">  

                                                    <label class="contol-label2"

                                                        for="drivers-approved-delete">{{trans('lang.delete')}}</label>



                                                </td>

                                            </tr>

                                              <tr>

                                                <td>

                                                    <strong>{{trans('lang.approval_pending_drivers')}}</strong>



                                                </td> 

                                                <td>

                                                    <input type="checkbox" id="drivers-list-pending" value="pending.driver.list"

                                                        name="pending_drivers[]" class="permission">

                                                    <label class="contol-label2"

                                                        for="drivers-list-pending">{{trans('lang.list')}}</label>

                                                    <input type="checkbox" id="drivers-pending-delete" value="pending.driver.delete"

                                                        name="pending_drivers[]" class="permission">  

                                                    <label class="contol-label2"

                                                        for="drivers-pending-delete">{{trans('lang.delete')}}</label>

                                                </td>

                                            </tr>



                                            

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.driver_document_plural')}}</strong>

                                                    </td>

                                                    <td> 

                                                        <input type="checkbox" id="driver-document-list" value="driver.document.list" name="drivers-document[]" class="permission">



                                                        <label class=" control-label2" for="driver-document-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="driver-document-edit" value="driver.document.edit" name="drivers-document[]" 

                                                        class="permission" >



                                                        <label class=" control-label2" for="driver-document-edit">{{trans('lang.edit')}}</label>



                                                    </td>

                                                </tr>





                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.reports_sale')}}</strong>



                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="report" value="report.index"

                                                            name="reports[]" class="permission">

                                                        <label class="control-label2"

                                                            for="report">{{trans('lang.create')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.category_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="categories-list" value="categories"

                                                            name="category[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="categories-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="categories-create"

                                                            value="categories.create" name="category[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="categories-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="categories-edit"

                                                            value="categories.edit" name="category[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="categories-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="category-delete" value="category.delete"

                                                        name="category[]" class="permission">

                                                        <label class=" control-label2" for="category-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.item_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="items-list" value="items"

                                                            name="items[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="items-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="items-create" value="items.create"

                                                            name="items[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="items-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="items-edit" value="items.edit"

                                                            name="items[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="items-edit">{{trans('lang.edit')}}</label>

                                                         <input type="checkbox" id="items-delete" value="items.delete"

                                                        name="items[]" class="permission">

                                                        <label class=" control-label2" for="items-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.item_attribute_id')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="attributes-list" value="attributes"

                                                            name="item-attribute[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="attributes-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="attributes-create"

                                                            value="attributes.create" name="item-attribute[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="attributes-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="attributes-edit"

                                                            value="attributes.edit" name="item-attribute[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="attributes-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="attributes-delete" value="attributes.delete"

                                                        name="attributes[]" class="permission">

                                                        <label class=" control-label2" for="attributes-delete">{{trans('lang.delete')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.review_attribute_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="reviewattributes-list"

                                                            value="reviewattributes" name="review-attribute[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="reviewattributes-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="reviewattributes-create"

                                                            value="reviewattributes.create" name="review-attribute[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="reviewattributes-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="reviewattributes-edit"

                                                            value="reviewattributes.edit" name="review-attribute[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="reviewattributes-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="reviewattributes-delete" value="reviewattributes.delete"

                                                        name="review-attribute[]" class="permission">

                                                        <label class=" control-label2" for="reviewattributes-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.order_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="orders-list" value="orders"

                                                            name="orders[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="orders-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="orders-print"

                                                            value="vendors.orderprint" name="orders[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="orders-print">{{trans('lang.print')}}</label>



                                                        <input type="checkbox" id="orders-edit" value="orders.edit"

                                                            name="orders[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="orders-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="orders-delete" value="orders.delete"

                                                        name="orders[]" class="permission">

                                                        <label class=" control-label2" for="orders-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                
                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.gift_card_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="gift-card" value="gift-card.index"

                                                            name="gift-cards[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="gift-card">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="gift-card-save"

                                                            value="gift-card.save" name="gift-cards[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="gift-card-save">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="gift-card-edit"

                                                            value="gift-card.edit" name="gift-cards[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="gift-card-edit">{{trans('lang.edit')}}</label>

                                                         <input type="checkbox" id="gift-card-delete" value="gift-card.delete"

                                                        name="gift-cards[]" class="permission">

                                                        <label class=" control-label2" for="gift-card-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.coupon_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="coupons" value="coupons"

                                                            name="coupons[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="coupons">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="coupons-create"

                                                            value="coupons.create" name="coupons[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="coupons-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="coupons-edit" value="coupons.edit"

                                                            name="coupons[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="coupons-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="coupons-delete" value="coupons.delete"

                                                        name="coupons[]" class="permission">

                                                        <label class=" control-label2" for="coupons-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.document_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="documents-list" value="documents.list"

                                                            name="documents[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="documents-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="documents-create"

                                                            value="documents.create" name="documents[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="documents-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="documents-edit" value="documents.edit"

                                                            name="documents[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="documents-edit">{{trans('lang.edit')}}</label>

                                                             <input type="checkbox" id="documents-delete" value="documents.delete"

                                                            name="documents[]" class="permission">

                                                        <label class=" control-label2" for="documents-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.general_notification')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="notification-list"

                                                            value="notification" name="general-notifications[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="notification-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="notification-send"

                                                            value="notification.send" name="general-notifications[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="notification-send">{{trans('lang.create')}}</label>

                                                         <input type="checkbox" id="notification-delete" value="notification.delete"

                                                            name="general-notifications[]" class="permission">

                                                        <label class=" control-label2" for="notification-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.dynamic_notification')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="dynamicnotification-list"

                                                            value="dynamic-notification.index"

                                                            name="dynamic-notifications[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="dynamicnotification-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="dynamicnotification-save"

                                                            value="dynamic-notification.save"

                                                            name="dynamic-notifications[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="dynamicnotification-save">{{trans('lang.edit')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.payment_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="payments" value="payments"

                                                            name="payments[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="payments">{{trans('lang.list')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.stores_payout_plural')}}</label>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="store-payouts-list"

                                                            value="storesPayouts" name="store-payouts[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="store-payouts-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="store-payouts-create"

                                                            value="storesPayouts.create"

                                                            name="store-payouts[]" class="permission">

                                                         <label class=" control-label2"

                                                            for="store-payouts-create">{{trans('lang.create')}}</label>

                                                       



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.driver_plural')}}

                                                            {{trans('lang.payment_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="driver-payments-list"

                                                            value="driver.driverpayments" name="driver-payments[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="driver-payments-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="driver-payments-delete" value="driver.driverpayments.delete"

                                                            name="driver-payments[]" class="permission">

                                                        <label class=" control-label2" for="driverpayments-delete">{{trans('lang.delete')}}</label>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.drivers_payout')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="driver-payouts-list"

                                                            value="driversPayouts" name="driver-payouts[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="driver-payouts-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="driver-payouts-create"

                                                            value="driversPayouts.create" name="driver-payouts[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="driver-payouts-create">{{trans('lang.create')}}</label>

                                                       



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.wallet_transaction')}}</strong>

                                                    </td>

                                                    <td>



                                                        <input type="checkbox" id="wallet-transaction-list"

                                                            value="walletstransaction" name="wallet-transaction[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="wallet-transaction-list">{{trans('lang.list')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.payout_request')}}</strong>

                                                    </td>

                                                    <td>



                                                        <input type="checkbox" id="payout-request-driver"

                                                            value="payoutRequests.drivers" name="payout-request[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="payout-request-driver">{{trans('lang.driver')}}

                                                            {{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="payout-request-store"

                                                            value="payoutRequests.stores" name="payout-request[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="payout-request-store">{{trans('lang.store')}}

                                                            {{trans('lang.list')}}</label>



                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.menu_items')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="banners" value="setting.banners"

                                                            name="banners[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="banners">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="banners-create"

                                                            value="setting.banners.create" name="banners[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="banners-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="banners-edit"

                                                            value="setting.banners.edit" name="banners[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="banners-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="banners-delete" value="banners.delete"

                                                            name="banners[]" class="permission">

                                                        <label class=" control-label2" for="banners-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.cms_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="cms" value="cms" name="cms[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="cms">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="cms-create" value="cms.create"

                                                            name="cms[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="cms-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="cms-edit" value="cms.edit"

                                                            name="cms[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="cms-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="cms-delete" value="cms.delete"

                                                            name="cms[]" class="permission">

                                                        <label class=" control-label2" for="cms-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>
                                                    <td>
                                                        <strong>{{trans('lang.on_board_plural')}}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="on-board-list" value="onboard.list"
                                                            name="on-board[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="on-board-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="on-board-edit" value="onboard.edit"
                                                            name="on-board[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="on-board-edit">{{trans('lang.edit')}}</label>

                                                    </td>
                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.email_templates')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="email-template"

                                                            value="email-templates.index" name="email-template[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="email-template">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="email-template-edit"

                                                            value="email-templates.edit" name="email-template[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="email-template-edit">{{trans('lang.edit')}}</label>



                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.app_setting_globals')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="global-setting"

                                                            value="settings.app.globals" name="global-setting[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="global-setting">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.currency_plural')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="currency-list" value="currencies"

                                                            name="currency[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="currency-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="currency-create"

                                                            value="currencies.create" name="currency[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="currency-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="currency-edit"

                                                            value="currencies.edit" name="currency[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="currency-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="currency-delete" value="currency.delete"

                                                            name="currency[]" class="permission">

                                                        <label class=" control-label2" for="currency-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.payment_methods')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="payment-method-list"

                                                            value="payment-method" name="payment-method[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="payment-method-list">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.store_admin_commission')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="admin-commission"

                                                            value="settings.app.adminCommission"

                                                            name="admin-commission[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="admin-commission">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.radios_configuration')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="radius"

                                                            value="settings.app.radiusConfiguration" name="radius[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="radius">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>


                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.vat_setting')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="tax-list" value="tax" name="tax[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="tax-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="tax-create" value="tax.create"

                                                            name="tax[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="tax-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="tax-edit" value="tax.edit"

                                                            name="tax[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="tax-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="tax-delete" value="tax.delete"

                                                            name="tax[]" class="permission">

                                                        <label class=" control-label2" for="tax-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.deliveryCharge')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="delivery-charge"

                                                            value="settings.app.deliveryCharge" name="delivery-charge[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="delivery-charge">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                <td>

                                                    <strong>{{trans('lang.documentVerification')}}</strong>

                                                </td>

                                                <td> 

                                                    <input type="checkbox" id="document-verification"

                                                        value="settings.app.documentVerification" name="document-verification[]"

                                                        class="permission">

                                                    <label class="contol-label2"

                                                        for="document-verification">{{trans('lang.update')}}</label>

                                                </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.languages')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="language-list"

                                                            value="settings.app.languages" name="language[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="language-list">{{trans('lang.list')}}</label>



                                                        <input type="checkbox" id="language-create"

                                                            value="settings.app.languages.create" name="language[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="language-create">{{trans('lang.create')}}</label>



                                                        <input type="checkbox" id="language-edit"

                                                            value="settings.app.languages.edit" name="language[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="language-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="language-delete" value="language.delete"

                                                            name="language[]" class="permission">

                                                        <label class=" control-label2" for="language-delete">{{trans('lang.delete')}}</label>



                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.special_offer')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="special-offer"

                                                            value="setting.specialOffer" name="special-offer[]"

                                                            class="permission">

                                                        <label class=" control-label2"

                                                            for="special-offer">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.terms_and_conditions')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="terms" value="termsAndConditions"

                                                            name="terms[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="terms">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.privacy_policy')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="privacy" value="privacyPolicy"

                                                            name="privacy[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="privacy">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.homepageTemplate')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="home-page" value="homepageTemplate"

                                                            name="home-page[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="home-page">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td>

                                                        <strong>{{trans('lang.footer_template')}}</strong>

                                                    </td>

                                                    <td>

                                                        <input type="checkbox" id="footer" value="footerTemplate"

                                                            name="footer[]" class="permission">

                                                        <label class=" control-label2"

                                                            for="footer">{{trans('lang.update')}}</label>

                                                    </td>

                                                </tr>



                                    </tbody>
                                </table>
                            </div>
                        </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                <a href="{{url('role')}}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                    <i class="fa fa-undo me-2"></i>{{ trans('lang.cancel')}}
                                </a>
                                <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold save-form-btn">
                                    <i class="fa fa-save me-2"></i> {{ trans('lang.save')}}
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection



    @section('scripts')



    <script>



        $(".save-form-btn").click(async function () {



            $(".success_top").hide();

            $(".error_top").hide();

            var name = $("#name").val();



            if (name == "") {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.user_name_help')}}</p>");

                window.scrollTo(0, 0);

                return false;

            }

            else {

                $('form#roleForm').submit();



            }



        });



        $('#all_permission').on('click', function () {



            if ($(this).is(':checked')) {

                $.each($('.permission'), function () {

                    $(this).prop('checked', true);

                });

            } else {

                $.each($('.permission'), function () {

                    $(this).prop('checked', false);

                });

            }



        });





    </script>



    @endsection