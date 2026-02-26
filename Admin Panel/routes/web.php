<?php

use Illuminate\Support\Facades\Route;

/*
|=============================================================================
| Plantix AI â€” Unified Route File
|=============================================================================
|
| Structure:
|   1.  /admin/*  â†’  Admin Panel   (EnsureAdminGuard middleware)
|   2.  /vendor/* â†’  Vendor Panel  (EnsureVendorGuard middleware)
|   3.  (root)    â†’  Customer/Frontend (web guard)
|
| All three panels share ONE database. Auth isolation via
| Laravel guards: 'admin', 'vendor', 'web'.
|
*/

// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
// 1. ADMIN PANEL  (/admin/*)
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

Route::prefix('admin')->name('admin.')->group(function () {

    // â”€â”€ Admin Auth (guest-only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login',  [App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'login']);

        Route::get('/password/email',         [App\Http\Controllers\Admin\Auth\AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/password/email',        [App\Http\Controllers\Admin\Auth\AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/password/reset/{token}', [App\Http\Controllers\Admin\Auth\AdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/password/reset',        [App\Http\Controllers\Admin\Auth\AdminResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::post('/logout', [App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'logout'])->name('logout');

    // â”€â”€ Protected Admin Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/',          [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

        // Language switcher
        Route::get('/lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('lang.change');

        // â”€â”€ Users â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:users,users'])->group(function () {
            Route::get('/users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');
            Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
        });
        Route::middleware(['permission:users,users.edit'])->group(function () {
            Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        });
        Route::middleware(['permission:users,users.create'])->group(function () {
            Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        });
        Route::middleware(['permission:users,users.view'])->group(function () {
            Route::get('/users/view/{id}', [App\Http\Controllers\UserController::class, 'view'])->name('users.view');
        });

        Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
        Route::post('/users/profile/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.profile.update');
        Route::post('/pay-to-user', [App\Http\Controllers\UserController::class, 'payToUser'])->name('pay.user');
        Route::post('/check-payout-status', [App\Http\Controllers\UserController::class, 'checkPayoutStatus'])->name('check.payout.status');

        // Admin Users management
        Route::middleware(['permission:admins,admin.users'])->group(function () {
            Route::get('admin-users', [App\Http\Controllers\UserController::class, 'adminUsers'])->name('admin.users');
        });
        Route::middleware(['permission:admins,admin.users.create'])->group(function () {
            Route::get('admin-users/create', [App\Http\Controllers\UserController::class, 'createAdminUsers'])->name('admin.users.create');
        });
        Route::middleware(['permission:admins,admin.users.store'])->group(function () {
            Route::post('admin-users/store', [App\Http\Controllers\UserController::class, 'storeAdminUsers'])->name('admin.users.store');
        });
        Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
            Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');
        });
        Route::middleware(['permission:admins,admin.users.edit'])->group(function () {
            Route::get('admin-users/edit/{id}', [App\Http\Controllers\UserController::class, 'editAdminUsers'])->name('admin.users.edit');
        });
        Route::middleware(['permission:admins,admin.users.update'])->group(function () {
            Route::post('admin-users/update/{id}', [App\Http\Controllers\UserController::class, 'updateAdminUsers'])->name('admin.users.update');
        });

        // â”€â”€ Vendors / Stores â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:vendors,vendors'])->group(function () {
            Route::get('/vendors', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors');
        });
        Route::middleware(['permission:approve_vendors,approve.vendors.list'])->group(function () {
            Route::get('/vendors/approved', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors.approved');
        });
        Route::middleware(['permission:pending_vendors,pending.vendors.list'])->group(function () {
            Route::get('/vendors/pending', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors.pending');
        });
        Route::middleware(['permission:stores,stores'])->group(function () {
            Route::get('/stores', [App\Http\Controllers\StoreController::class, 'index'])->name('stores');
        });
        Route::middleware(['permission:stores,stores.create'])->group(function () {
            Route::get('/stores/create', [App\Http\Controllers\StoreController::class, 'create'])->name('stores.create');
        });
        Route::middleware(['permission:stores,stores.edit'])->group(function () {
            Route::get('/stores/edit/{id}', [App\Http\Controllers\StoreController::class, 'edit'])->name('stores.edit');
        });
        Route::middleware(['permission:stores,stores.view'])->group(function () {
            Route::get('/stores/view/{id}', [App\Http\Controllers\StoreController::class, 'view'])->name('stores.view');
        });
        Route::get('/stores/promos/{id}', [App\Http\Controllers\StoreController::class, 'promos'])->name('stores.promos');
        Route::middleware(['permission:vendors-document,vendor.document.list'])->group(function () {
            Route::get('vendors/document-list/{id}', [App\Http\Controllers\StoreController::class, 'DocumentList'])->name('vendors.document');
        });
        Route::middleware(['permission:vendors-document,vendor.document.edit'])->group(function () {
            Route::get('/vendors/document/upload/{driverId}/{id}', [App\Http\Controllers\StoreController::class, 'DocumentUpload'])->name('vendors.document.upload');
        });

        // â”€â”€ Products (new AdminProductController) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::prefix('/products')->name('products.')->group(function () {
            Route::get('/',          [App\Http\Controllers\Admin\AdminProductController::class, 'index'])->name('index');
            Route::get('/create',    [App\Http\Controllers\Admin\AdminProductController::class, 'create'])->name('create');
            Route::post('/',         [App\Http\Controllers\Admin\AdminProductController::class, 'store'])->name('store');
            Route::get('/{id}',      [App\Http\Controllers\Admin\AdminProductController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\Admin\AdminProductController::class, 'edit'])->name('edit');
            Route::put('/{id}',      [App\Http\Controllers\Admin\AdminProductController::class, 'update'])->name('update');
            Route::delete('/{id}',   [App\Http\Controllers\Admin\AdminProductController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-featured', [App\Http\Controllers\Admin\AdminProductController::class, 'toggleFeatured'])->name('toggle-featured');
        });

        // â”€â”€ Items (legacy food-delivery item controller) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:items,items'])->group(function () {
            Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])->name('items');
            Route::get('/items/{id}', [App\Http\Controllers\ItemController::class, 'index'])->name('stores.items');
        });
        Route::middleware(['permission:items,items.edit'])->group(function () {
            Route::get('/Items/edit/{id}', [App\Http\Controllers\ItemController::class, 'edit'])->name('items.edit');
        });
        Route::middleware(['permission:items,items.create'])->group(function () {
            Route::get('/item/create', [App\Http\Controllers\ItemController::class, 'create'])->name('items.create');
            Route::get('/item/create/{id}', [App\Http\Controllers\ItemController::class, 'create']);
        });

        // â”€â”€ Orders â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::prefix('/orders')->name('orders.')->group(function () {
            Route::get('/',           [App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('index');
            Route::get('/{id}',       [App\Http\Controllers\Admin\AdminOrderController::class, 'show'])->name('show');
            Route::post('/{id}/status',        [App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus'])->name('status');
            Route::post('/{id}/assign-driver', [App\Http\Controllers\Admin\AdminOrderController::class, 'assignDriver'])->name('assign-driver');
        });
        // Legacy order routes (backward compat)
        Route::middleware(['permission:orders,orders'])->group(function () {
            Route::get('/orders-legacy/', [App\Http\Controllers\OrderController::class, 'index'])->name('orders-legacy');
            Route::get('/orders-legacy/{id}', [App\Http\Controllers\OrderController::class, 'index'])->name('stores.orders');
        });
        Route::middleware(['permission:orders,orders.edit'])->group(function () {
            Route::get('/orders/edit/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');
        });
        Route::middleware(['permission:orders,vendors.orderprint'])->group(function () {
            Route::get('/orders/print/{id}', [App\Http\Controllers\OrderController::class, 'orderprint'])->name('vendors.orderprint');
        });
        Route::post('/order-status-notification', [App\Http\Controllers\OrderController::class, 'sendNotification'])->name('order-status-notification');
        Route::get('/usersorders/{type}', [App\Http\Controllers\OrderController::class, 'index'])->name('usersorders');

        // â”€â”€ Appointments â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::prefix('/appointments')->name('appointments.')->group(function () {
            Route::get('/',              [App\Http\Controllers\Admin\AdminAppointmentController::class, 'index'])->name('index');
            Route::get('/{id}',          [App\Http\Controllers\Admin\AdminAppointmentController::class, 'show'])->name('show');
            Route::post('/{id}/confirm', [App\Http\Controllers\Admin\AdminAppointmentController::class, 'confirm'])->name('confirm');
            Route::post('/{id}/cancel',  [App\Http\Controllers\Admin\AdminAppointmentController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/complete',[App\Http\Controllers\Admin\AdminAppointmentController::class, 'complete'])->name('complete');
        });

        // â”€â”€ Returns & Refunds â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::prefix('/returns')->name('returns.')->group(function () {
            Route::get('/',                [App\Http\Controllers\Admin\AdminReturnController::class, 'index'])->name('index');
            Route::get('/{id}',            [App\Http\Controllers\Admin\AdminReturnController::class, 'show'])->name('show');
            Route::post('/{id}/approve',   [App\Http\Controllers\Admin\AdminReturnController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject',    [App\Http\Controllers\Admin\AdminReturnController::class, 'reject'])->name('reject');
            Route::post('/{id}/refund',    [App\Http\Controllers\Admin\AdminReturnController::class, 'processRefund'])->name('refund');
            Route::get('/reasons',         [App\Http\Controllers\Admin\AdminReturnController::class, 'reasons'])->name('reasons');
            Route::post('/reasons',        [App\Http\Controllers\Admin\AdminReturnController::class, 'storeReason'])->name('reasons.store');
            Route::delete('/reasons/{id}', [App\Http\Controllers\Admin\AdminReturnController::class, 'destroyReason'])->name('reasons.destroy');
        });

        // â”€â”€ Categories â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:category,categories'])->group(function () {
            Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
        });
        Route::middleware(['permission:category,categories.edit'])->group(function () {
            Route::get('/categories/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');
        });
        Route::middleware(['permission:category,categories.create'])->group(function () {
            Route::get('/categories/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
        });

        // â”€â”€ Coupons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:coupons,coupons'])->group(function () {
            Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');
            Route::get('/coupon/{id}', [App\Http\Controllers\CouponController::class, 'index'])->name('stores.coupons');
        });
        Route::middleware(['permission:coupons,coupons.edit'])->group(function () {
            Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');
        });
        Route::middleware(['permission:coupons,coupons.create'])->group(function () {
            Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');
            Route::get('/coupon/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);
            Route::get('/coupons/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);
        });

        // â”€â”€ Drivers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:drivers,drivers'])->group(function () {
            Route::get('/drivers', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
        });
        Route::middleware(['permission:approve_drivers,approve.driver.list'])->group(function () {
            Route::get('/drivers/approved', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.approved');
        });
        Route::middleware(['permission:pending_drivers,pending.driver.list'])->group(function () {
            Route::get('/drivers/pending', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.pending');
        });
        Route::middleware(['permission:drivers,drivers.edit'])->group(function () {
            Route::get('/drivers/edit/{id}', [App\Http\Controllers\DriverController::class, 'edit'])->name('drivers.edit');
        });
        Route::middleware(['permission:drivers,drivers.create'])->group(function () {
            Route::get('/drivers/create', [App\Http\Controllers\DriverController::class, 'create'])->name('drivers.create');
        });
        Route::middleware(['permission:drivers,drivers.view'])->group(function () {
            Route::get('/drivers/view/{id}', [App\Http\Controllers\DriverController::class, 'view'])->name('drivers.view');
        });
        Route::middleware(['permission:drivers-document,driver.document.list'])->group(function () {
            Route::get('drivers/document-list/{id}', [App\Http\Controllers\DriverController::class, 'DocumentList'])->name('drivers.document');
        });
        Route::middleware(['permission:drivers-document,driver.document.edit'])->group(function () {
            Route::get('/drivers/document/upload/{driverId}/{id}', [App\Http\Controllers\DriverController::class, 'DocumentUpload'])->name('drivers.document.upload');
        });

        // â”€â”€ Store Filters â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::get('/storeFilters', [App\Http\Controllers\StoreFiltersController::class, 'index'])->name('storeFilters');
        Route::get('/storeFilters/create', [App\Http\Controllers\StoreFiltersController::class, 'create'])->name('storeFilters.create');
        Route::get('/storeFilters/edit/{id}', [App\Http\Controllers\StoreFiltersController::class, 'edit'])->name('storeFilters.edit');

        // â”€â”€ Payments & Payouts â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:payments,payments'])->group(function () {
            Route::get('/payments', [App\Http\Controllers\AdminPaymentsController::class, 'index'])->name('payments');
        });
        Route::middleware(['permission:driver-payments,driver.driverpayments'])->group(function () {
            Route::get('/driverpayments', [App\Http\Controllers\AdminPaymentsController::class, 'driverIndex'])->name('driver.driverpayments');
        });
        Route::middleware(['permission:store-payouts,storesPayouts'])->group(function () {
            Route::get('/storesPayouts', [App\Http\Controllers\StorePayoutController::class, 'index'])->name('storesPayouts');
            Route::get('/storesPayout/{id}', [App\Http\Controllers\StorePayoutController::class, 'index'])->name('stores.payout');
        });
        Route::middleware(['permission:store-payouts,storesPayouts.create'])->group(function () {
            Route::get('/storesPayouts/create', [App\Http\Controllers\StorePayoutController::class, 'create'])->name('storesPayouts.create');
            Route::get('/storesPayouts/create/{id}', [App\Http\Controllers\StorePayoutController::class, 'create']);
        });
        Route::middleware(['permission:driver-payouts,driversPayouts'])->group(function () {
            Route::get('/driversPayouts', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driversPayouts');
            Route::get('/driverPayout/{id}', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driver.payout');
        });
        Route::middleware(['permission:driver-payouts,driversPayouts.create'])->group(function () {
            Route::get('/driversPayouts/create', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driversPayouts.create');
            Route::get('/driverPayout/create/{id}', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driver.payout.create');
        });
        Route::middleware(['permission:payout-request,payoutRequests.drivers'])->group(function () {
            Route::get('/payoutRequests/drivers', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers');
            Route::get('/payoutRequests/drivers/{id}', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers.view');
        });
        Route::middleware(['permission:payout-request,payoutRequests.stores'])->group(function () {
            Route::get('/payoutRequests/stores', [App\Http\Controllers\PayoutRequestController::class, 'store'])->name('payoutRequests.stores');
            Route::get('/payoutRequests/stores/{id}', [App\Http\Controllers\PayoutRequestController::class, 'store'])->name('payoutRequests.stores.view');
        });

        // â”€â”€ Wallet transactions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:wallet-transaction,walletstransaction'])->group(function () {
            Route::get('/walletstransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('walletstransaction');
            Route::get('/walletstransaction/{id}', [App\Http\Controllers\TransactionController::class, 'index'])->name('users.walletstransaction');
        });

        // â”€â”€ Order Transactions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::get('/order_transactions', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions');
        Route::get('/order_transactions/{id}', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions.index');

        // â”€â”€ Dynamic Notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:dynamic-notifications,dynamic-notification.index'])->group(function () {
            Route::get('/dynamic-notification', [App\Http\Controllers\DynamicNotificationController::class, 'index'])->name('dynamic-notification.index');
        });
        Route::middleware(['permission:dynamic-notifications,dynamic-notification.save'])->group(function () {
            Route::get('/dynamic-notification/save/{id?}', [App\Http\Controllers\DynamicNotificationController::class, 'save'])->name('dynamic-notification.save');
        });
        Route::middleware(['permission:dynamic-notifications,dynamic-notification.delete'])->group(function () {
            Route::get('/dynamic-notification/delete/{id}', [App\Http\Controllers\DynamicNotificationController::class, 'delete'])->name('dynamic-notification.delete');
        });

        // â”€â”€ General Notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:general-notifications,notification'])->group(function () {
            Route::get('/notification', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification');
        });
        Route::middleware(['permission:general-notifications,notification.send'])->group(function () {
            Route::get('/notification/send', [App\Http\Controllers\NotificationController::class, 'send'])->name('notification.send');
        });
        Route::post('/broadcastnotification', [App\Http\Controllers\NotificationController::class, 'broadcastnotification'])->name('broadcastnotification');
        Route::post('/send-notification', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('send-notification');
        Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');

        // â”€â”€ God Eye / Map â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:god-eye,map'])->group(function () {
            Route::get('/map', [App\Http\Controllers\MapController::class, 'index'])->name('map');
            Route::post('/map/get_order_info', [App\Http\Controllers\MapController::class, 'getOrderInfo'])->name('map.getOrderInfo');
        });

        // â”€â”€ Dine-in / Book Table â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:dinein-orders,stores.booktable'])->group(function () {
            Route::get('/booktable/{id}', [App\Http\Controllers\BookTableController::class, 'index'])->name('stores.booktable');
        });
        Route::middleware(['permission:dinein-orders,booktable.edit'])->group(function () {
            Route::get('/booktable/edit/{id}', [App\Http\Controllers\BookTableController::class, 'edit'])->name('booktable.edit');
        });

        // â”€â”€ Reviews â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:review-attribute,reviewattributes'])->group(function () {
            Route::get('/reviewattributes', [App\Http\Controllers\ReviewAttributeController::class, 'index'])->name('reviewattributes');
        });
        Route::middleware(['permission:review-attribute,reviewattributes.edit'])->group(function () {
            Route::get('/reviewattributes/edit/{id}', [App\Http\Controllers\ReviewAttributeController::class, 'edit'])->name('reviewattributes.edit');
        });
        Route::middleware(['permission:review-attribute,reviewattributes.create'])->group(function () {
            Route::get('/reviewattributes/create', [App\Http\Controllers\ReviewAttributeController::class, 'create'])->name('reviewattributes.create');
        });

        // â”€â”€ Attributes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:item-attribute,attributes'])->group(function () {
            Route::get('/attributes', [App\Http\Controllers\AttributeController::class, 'index'])->name('attributes');
        });
        Route::middleware(['permission:item-attribute,attributes.edit'])->group(function () {
            Route::get('/attributes/edit/{id}', [App\Http\Controllers\AttributeController::class, 'edit'])->name('attributes.edit');
        });
        Route::middleware(['permission:item-attribute,attributes.create'])->group(function () {
            Route::get('/attributes/create', [App\Http\Controllers\AttributeController::class, 'create'])->name('attributes.create');
        });

        // â”€â”€ Roles & Permissions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:roles,role.index'])->group(function () {
            Route::get('/role', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
        });
        Route::middleware(['permission:roles,role.save'])->group(function () {
            Route::get('/role/save', [App\Http\Controllers\RoleController::class, 'save'])->name('role.save');
        });
        Route::middleware(['permission:roles,role.store'])->group(function () {
            Route::post('/role/store', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
        });
        Route::middleware(['permission:roles,role.delete'])->group(function () {
            Route::get('/role/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->name('role.delete');
        });
        Route::middleware(['permission:roles,role.edit'])->group(function () {
            Route::get('/role/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
        });
        Route::middleware(['permission:roles,role.update'])->group(function () {
            Route::post('/role/update/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update');
        });

        // â”€â”€ Zones â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:zone,zone.list'])->group(function () {
            Route::get('/zone', [App\Http\Controllers\ZoneController::class, 'index'])->name('zone');
        });
        Route::middleware(['permission:zone,zone.create'])->group(function () {
            Route::get('/zone/create', [App\Http\Controllers\ZoneController::class, 'create'])->name('zone.create');
        });
        Route::middleware(['permission:zone,zone.edit'])->group(function () {
            Route::get('/zone/edit/{id}', [App\Http\Controllers\ZoneController::class, 'edit'])->name('zone.edit');
        });

        // â”€â”€ Documents â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:documents,documents.list'])->group(function () {
            Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents');
        });
        Route::middleware(['permission:documents,documents.create'])->group(function () {
            Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
        });
        Route::middleware(['permission:documents,documents.edit'])->group(function () {
            Route::get('/documents/edit/{id}', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
        });

        // â”€â”€ Settings â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::prefix('/settings')->group(function () {
            Route::middleware(['permission:currency,currencies'])->group(function () {
                Route::get('/currencies', [App\Http\Controllers\CurrencyController::class, 'index'])->name('currencies');
            });
            Route::middleware(['permission:currency,currencies.edit'])->group(function () {
                Route::get('/currencies/edit/{id}', [App\Http\Controllers\CurrencyController::class, 'edit'])->name('currencies.edit');
            });
            Route::middleware(['permission:currency,currencies.create'])->group(function () {
                Route::get('/currencies/create', [App\Http\Controllers\CurrencyController::class, 'create'])->name('currencies.create');
            });
            Route::middleware(['permission:global-setting,settings.app.globals'])->group(function () {
                Route::get('/app/globals', [App\Http\Controllers\SettingsController::class, 'globals'])->name('settings.app.globals');
            });
            Route::middleware(['permission:admin-commission,settings.app.adminCommission'])->group(function () {
                Route::get('/app/adminCommission', [App\Http\Controllers\SettingsController::class, 'adminCommission'])->name('settings.app.adminCommission');
            });
            Route::middleware(['permission:radius,settings.app.radiusConfiguration'])->group(function () {
                Route::get('/app/radiusConfiguration', [App\Http\Controllers\SettingsController::class, 'radiosConfiguration'])->name('settings.app.radiusConfiguration');
            });
            Route::middleware(['permission:dinein,settings.app.bookTable'])->group(function () {
                Route::get('/app/bookTable', [App\Http\Controllers\SettingsController::class, 'bookTable'])->name('settings.app.bookTable');
            });
            Route::middleware(['permission:delivery-charge,settings.app.deliveryCharge'])->group(function () {
                Route::get('/app/deliveryCharge', [App\Http\Controllers\SettingsController::class, 'deliveryCharge'])->name('settings.app.deliveryCharge');
            });
            Route::middleware(['permission:document-verification,settings.app.documentVerification'])->group(function () {
                Route::get('/app/documentVerification', [App\Http\Controllers\SettingsController::class, 'documentVerification'])->name('settings.app.documentVerification');
            });
            Route::get('/app/notifications', [App\Http\Controllers\SettingsController::class, 'notifications'])->name('settings.app.notifications');
            Route::get('/mobile/globals', [App\Http\Controllers\SettingsController::class, 'mobileGlobals'])->name('settings.mobile.globals');
            Route::middleware(['permission:payment-method,payment-method'])->group(function () {
                Route::get('/payment/stripe',       [App\Http\Controllers\SettingsController::class, 'stripe'])->name('payment.stripe');
                Route::get('/payment/applepay',     [App\Http\Controllers\SettingsController::class, 'applepay'])->name('payment.applepay');
                Route::get('/payment/razorpay',     [App\Http\Controllers\SettingsController::class, 'razorpay'])->name('payment.razorpay');
                Route::get('/payment/cod',          [App\Http\Controllers\SettingsController::class, 'cod'])->name('payment.cod');
                Route::get('/payment/paypal',       [App\Http\Controllers\SettingsController::class, 'paypal'])->name('payment.paypal');
                Route::get('/payment/paytm',        [App\Http\Controllers\SettingsController::class, 'paytm'])->name('payment.paytm');
                Route::get('/payment/wallet',       [App\Http\Controllers\SettingsController::class, 'wallet'])->name('payment.wallet');
                Route::get('/payment/payfast',      [App\Http\Controllers\SettingsController::class, 'payfast'])->name('payment.payfast');
                Route::get('/payment/paystack',     [App\Http\Controllers\SettingsController::class, 'paystack'])->name('payment.paystack');
                Route::get('/payment/flutterwave',  [App\Http\Controllers\SettingsController::class, 'flutterwave'])->name('payment.flutterwave');
                Route::get('/payment/mercadopago',  [App\Http\Controllers\SettingsController::class, 'mercadopago'])->name('payment.mercadopago');
                Route::get('/payment/xendit',       [App\Http\Controllers\SettingsController::class, 'xendit'])->name('payment.xendit');
                Route::get('/payment/orangepay',    [App\Http\Controllers\SettingsController::class, 'orangepay'])->name('payment.orangepay');
                Route::get('/payment/midtrans',     [App\Http\Controllers\SettingsController::class, 'midtrans'])->name('payment.midtrans');
            });
            Route::middleware(['permission:language,settings.app.languages'])->group(function () {
                Route::get('/app/languages', [App\Http\Controllers\SettingsController::class, 'languages'])->name('settings.app.languages');
            });
            Route::middleware(['permission:language,settings.app.languages.create'])->group(function () {
                Route::get('/app/languages/create', [App\Http\Controllers\SettingsController::class, 'languagescreate'])->name('settings.app.languages.create');
            });
            Route::middleware(['permission:language,settings.app.languages.edit'])->group(function () {
                Route::get('/app/languages/edit/{id}', [App\Http\Controllers\SettingsController::class, 'languagesedit'])->name('settings.app.languages.edit');
            });
            Route::middleware(['permission:special-offer,setting.specialOffer'])->group(function () {
                Route::get('/app/specialOffer', [App\Http\Controllers\SettingsController::class, 'specialOffer'])->name('setting.specialOffer');
            });
            Route::get('/app/story', [App\Http\Controllers\SettingsController::class, 'story'])->name('setting.story');
        });

        // â”€â”€ Banners â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:banners,setting.banners'])->group(function () {
            Route::get('/banners', [App\Http\Controllers\SettingsController::class, 'menuItems'])->name('setting.banners');
        });
        Route::middleware(['permission:banners,setting.banners.create'])->group(function () {
            Route::get('/banners/create', [App\Http\Controllers\SettingsController::class, 'menuItemsCreate'])->name('setting.banners.create');
        });
        Route::middleware(['permission:banners,setting.banners.edit'])->group(function () {
            Route::get('/banners/edit/{id}', [App\Http\Controllers\SettingsController::class, 'menuItemsEdit'])->name('setting.banners.edit');
        });

        // â”€â”€ CMS Pages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:cms,cms'])->group(function () {
            Route::get('/cms', [App\Http\Controllers\CmsController::class, 'index'])->name('cms');
        });
        Route::middleware(['permission:cms,cms.edit'])->group(function () {
            Route::get('/cms/edit/{id}', [App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
        });
        Route::middleware(['permission:cms,cms.create'])->group(function () {
            Route::get('/cms/create', [App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
        });

        // â”€â”€ Reports â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:reports,report.index'])->group(function () {
            Route::get('/report/{type}', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
        });

        // â”€â”€ Tax â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:tax,tax'])->group(function () {
            Route::get('/tax', [App\Http\Controllers\TaxController::class, 'index'])->name('tax');
        });
        Route::middleware(['permission:tax,tax.edit'])->group(function () {
            Route::get('/tax/edit/{id}', [App\Http\Controllers\TaxController::class, 'edit'])->name('tax.edit');
        });
        Route::middleware(['permission:tax,tax.create'])->group(function () {
            Route::get('/tax/create', [App\Http\Controllers\TaxController::class, 'create'])->name('tax.create');
        });

        // â”€â”€ Email Templates â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:email-template,email-templates.index'])->group(function () {
            Route::get('/email-templates', [App\Http\Controllers\SettingsController::class, 'emailTemplatesIndex'])->name('email-templates.index');
        });
        Route::middleware(['permission:email-template,email-templates.edit'])->group(function () {
            Route::get('/email-templates/save/{id?}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesSave'])->name('email-templates.save');
        });
        Route::middleware(['permission:email-template,email-templates.delete'])->group(function () {
            Route::get('/email-templates/delete/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesDelete'])->name('email-templates.delete');
        });
        Route::post('/send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

        // â”€â”€ Gift Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:gift-cards,gift-card.index'])->group(function () {
            Route::get('/gift-card', [App\Http\Controllers\GiftCardController::class, 'index'])->name('gift-card.index');
        });
        Route::middleware(['permission:gift-cards,gift-card.save'])->group(function () {
            Route::get('/gift-card/save/{id?}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.save');
        });
        Route::middleware(['permission:gift-cards,gift-card.edit'])->group(function () {
            Route::get('/gift-card/edit/{id}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.edit');
        });

        // â”€â”€ On-board Screens â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::get('/on-board', [App\Http\Controllers\OnBoardController::class, 'index'])->name('on-board');
        Route::get('/on-board/save/{id}', [App\Http\Controllers\OnBoardController::class, 'show'])->name('on-board.save');

        // â”€â”€ Terms / Privacy â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['permission:terms,termsAndConditions'])->group(function () {
            Route::get('/termsAndConditions', [App\Http\Controllers\TermsAndConditionsController::class, 'index'])->name('termsAndConditions');
        });
        Route::middleware(['permission:privacy,privacyPolicy'])->group(function () {
            Route::get('/privacyPolicy', [App\Http\Controllers\TermsAndConditionsController::class, 'privacyindex'])->name('privacyPolicy');
        });

    }); // end admin middleware group
}); // end /admin prefix


// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
// 2. VENDOR PANEL  (/vendor/*)
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

Route::prefix('vendor')->name('vendor.')->group(function () {

    // â”€â”€ Vendor Auth (guest-only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::middleware('guest:vendor')->group(function () {
        Route::get('/login',  [App\Http\Controllers\Vendor\Auth\VendorLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Vendor\Auth\VendorLoginController::class, 'login']);
    });

    Route::post('/logout', [App\Http\Controllers\Vendor\Auth\VendorLoginController::class, 'logout'])->name('logout');

    // â”€â”€ Protected Vendor Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::middleware('vendor.auth')->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Vendor\VendorDashboardController::class, 'index'])->name('dashboard');

        // Products (scoped to this vendor)
        Route::get('/products',           [App\Http\Controllers\Vendor\VendorProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',    [App\Http\Controllers\Vendor\VendorProductController::class, 'create'])->name('products.create');
        Route::post('/products',          [App\Http\Controllers\Vendor\VendorProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}',      [App\Http\Controllers\Vendor\VendorProductController::class, 'show'])->name('products.show');
        Route::get('/products/{id}/edit', [App\Http\Controllers\Vendor\VendorProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}',      [App\Http\Controllers\Vendor\VendorProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}',   [App\Http\Controllers\Vendor\VendorProductController::class, 'destroy'])->name('products.destroy');

        // Orders (scoped to this vendor)
        Route::get('/orders',              [App\Http\Controllers\Vendor\VendorOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}',         [App\Http\Controllers\Vendor\VendorOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [App\Http\Controllers\Vendor\VendorOrderController::class, 'updateStatus'])->name('orders.status');

        // Earnings / Payouts
        Route::get('/earnings', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('earnings.index');
        Route::get('/payouts',  [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payouts.index');
        Route::post('/payouts', [App\Http\Controllers\PayoutRequestController::class, 'store'])->name('payouts.request');

        // Profile
        Route::get('/profile', [App\Http\Controllers\UserController::class, 'vendorProfile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\UserController::class, 'updateVendorProfile'])->name('profile.update');

    }); // end vendor.auth
}); // end /vendor prefix


// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
// 3. CUSTOMER / FRONTEND  (root â€” no prefix)
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

// â”€â”€ Public Pages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/',             fn () => view('pages.index'))->name('home');
Route::get('/about-us',     fn () => view('pages.about-us'))->name('about');
Route::get('/contact',      fn () => view('pages.contact'))->name('contact');

// â”€â”€ Plantix AI Feature Pages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/plantix-ai',                 fn () => view('pages.plantix-ai'))->name('plantix-ai');
Route::get('/crop-recommendation',        fn () => view('pages.crop-recommendation'))->name('crop-recommendation');
Route::get('/crop-planning',              fn () => view('pages.crop-planning'))->name('crop-planning');
Route::get('/disease-identification',     fn () => view('pages.disease-identification'))->name('disease-identification');
Route::get('/fertilizer-recommendation',  fn () => view('pages.fertilizer-recommendation'))->name('fertilizer-recommendation');

// â”€â”€ Customer Auth â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware('guest:web')->group(function () {
    Route::get('/signin',  [App\Http\Controllers\Frontend\Auth\CustomerLoginController::class, 'showLoginForm'])->name('signin');
    Route::post('/signin', [App\Http\Controllers\Frontend\Auth\CustomerLoginController::class, 'login'])->name('login');

    Route::get('/signup',  [App\Http\Controllers\Frontend\Auth\CustomerRegisterController::class, 'showRegistrationForm'])->name('signup');
    Route::post('/signup', [App\Http\Controllers\Frontend\Auth\CustomerRegisterController::class, 'register'])->name('register');

    Route::get('/password/forgot',        [App\Http\Controllers\Frontend\Auth\CustomerForgotPasswordController::class, 'showLinkRequestForm'])->name('password.forgot');
    Route::post('/password/email',        [App\Http\Controllers\Frontend\Auth\CustomerForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [App\Http\Controllers\Frontend\Auth\CustomerResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset',        [App\Http\Controllers\Frontend\Auth\CustomerResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/signout', [App\Http\Controllers\Frontend\Auth\CustomerLoginController::class, 'logout'])->name('logout');

// â”€â”€ Shop (public) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/shop',       [App\Http\Controllers\Frontend\ShopController::class, 'index'])->name('shop');
Route::get('/shop/{id}',  [App\Http\Controllers\Frontend\ShopController::class, 'show'])->name('shop.single');

// â”€â”€ Forum (public read, auth required for create/reply) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/forum',              [App\Http\Controllers\Frontend\ForumController::class, 'index'])->name('forum');
Route::get('/forum/{id}',         [App\Http\Controllers\Frontend\ForumController::class, 'show'])->name('forum.thread');
Route::get('/forum/new-thread',   [App\Http\Controllers\Frontend\ForumController::class, 'create'])->name('forum.new');
Route::post('/forum',             [App\Http\Controllers\Frontend\ForumController::class, 'store'])->name('forum.store');
Route::post('/forum/{id}/reply',  [App\Http\Controllers\Frontend\ForumController::class, 'reply'])->name('forum.reply');
Route::redirect('/blog',          '/forum')->name('blog');

// â”€â”€ Authenticated Customer Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware('customer')->group(function () {

    // Cart
    Route::get('/cart',          [App\Http\Controllers\Frontend\CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',     [App\Http\Controllers\Frontend\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}',   [App\Http\Controllers\Frontend\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}',  [App\Http\Controllers\Frontend\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',       [App\Http\Controllers\Frontend\CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout',      [App\Http\Controllers\Frontend\CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout',     [App\Http\Controllers\Frontend\CartController::class, 'placeOrder'])->name('checkout.place');

    // Orders
    Route::get('/orders',             [App\Http\Controllers\Frontend\CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}',        [App\Http\Controllers\Frontend\CustomerOrderController::class, 'show'])->name('order.details');
    Route::get('/order/success/{id}', [App\Http\Controllers\Frontend\CustomerOrderController::class, 'success'])->name('order.success');
    Route::post('/orders/{id}/return',[App\Http\Controllers\Frontend\CustomerOrderController::class, 'requestReturn'])->name('order.return');

    // Appointments
    Route::get('/appointments',             [App\Http\Controllers\Frontend\CustomerAppointmentController::class, 'index'])->name('appointments');
    Route::get('/appointment/book',         [App\Http\Controllers\Frontend\CustomerAppointmentController::class, 'create'])->name('appointment.book');
    Route::post('/appointment/book',        [App\Http\Controllers\Frontend\CustomerAppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/{id}',         [App\Http\Controllers\Frontend\CustomerAppointmentController::class, 'show'])->name('appointment.details');
    Route::post('/appointment/{id}/cancel', [App\Http\Controllers\Frontend\CustomerAppointmentController::class, 'cancel'])->name('appointment.cancel');

    // Reviews
    Route::post('/products/{id}/reviews', [App\Http\Controllers\Frontend\ProductReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{id}',        [App\Http\Controllers\Frontend\ProductReviewController::class, 'destroy'])->name('reviews.destroy');

    // Profile
    Route::get('/account/profile',   [App\Http\Controllers\Frontend\CustomerProfileController::class, 'show'])->name('account.profile');
    Route::put('/account/profile',   [App\Http\Controllers\Frontend\CustomerProfileController::class, 'update'])->name('account.profile.update');
    Route::post('/account/password', [App\Http\Controllers\Frontend\CustomerProfileController::class, 'changePassword'])->name('account.password');

}); // end customer middleware

// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
// 4. SHARED / UTILITY  (accessible without prefix, kept for legacy compat)
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

Route::post('payments/razorpay/createorder',   [App\Http\Controllers\RazorPayController::class, 'createOrderid']);
Route::post('payments/getpaytmchecksum',        [App\Http\Controllers\PaymentController::class, 'getPaytmChecksum']);
Route::post('payments/validatechecksum',        [App\Http\Controllers\PaymentController::class, 'validateChecksum']);
Route::post('payments/initiatepaytmpayment',    [App\Http\Controllers\PaymentController::class, 'initiatePaytmPayment']);
Route::get('payments/paytmpaymentcallback',     [App\Http\Controllers\PaymentController::class, 'paytmPaymentcallback']);
Route::post('payments/paypalclientid',          [App\Http\Controllers\PaymentController::class, 'getPaypalClienttoken']);
Route::post('payments/paypaltransaction',       [App\Http\Controllers\PaymentController::class, 'createBraintreePayment']);
Route::post('payments/stripepaymentintent',     [App\Http\Controllers\PaymentController::class, 'createStripePaymentIntent']);

Route::get('payment/success',  [App\Http\Controllers\PaymentController::class, 'paymentsuccess'])->name('payment.success');
Route::get('payment/failed',   [App\Http\Controllers\PaymentController::class, 'paymentfailed'])->name('payment.failed');
Route::get('payment/pending',  [App\Http\Controllers\PaymentController::class, 'paymentpending'])->name('payment.pending');
