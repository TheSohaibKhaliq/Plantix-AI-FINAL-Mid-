<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CustomerAuthApiController;
use App\Http\Controllers\Api\CustomerCartApiController;
use App\Http\Controllers\Api\CustomerOrderApiController;
use App\Http\Controllers\Api\CustomerAppointmentApiController;

/*
|--------------------------------------------------------------------------
| Plantix AI — API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by RouteServiceProvider within the "api" group.
|
| Roles: admin, vendor, customer, expert
| Auth:  Laravel Sanctum (Bearer token)
|
*/

// =============================================================================
// Customer API  (prefix: /api/customer)
// =============================================================================
Route::prefix('customer')->group(function () {

    // ── Public auth (no token required) ──────────────────────────────────────
    Route::post('/auth/register',   [CustomerAuthApiController::class, 'register']);
    Route::post('/auth/login',      [CustomerAuthApiController::class, 'login']);
    Route::post('/password/forgot', [CustomerAuthApiController::class, 'forgotPassword']);
    Route::get('/csrf',             fn () => response()->noContent());

    // ── Available experts (public, for booking page) ──────────────────────────
    Route::get('/appointments/experts', [CustomerAppointmentApiController::class, 'experts']);

    // ── Protected (Bearer token required) ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth & profile
        Route::post('/auth/logout',   [CustomerAuthApiController::class, 'logout']);
        Route::get('/auth/me',        [CustomerAuthApiController::class, 'me']);
        Route::put('/auth/profile',   [CustomerAuthApiController::class, 'updateProfile']);
        Route::post('/auth/password', [CustomerAuthApiController::class, 'changePassword']);

        // Cart
        Route::get('/cart',           [CustomerCartApiController::class, 'index']);
        Route::post('/cart/add',      [CustomerCartApiController::class, 'add']);
        Route::patch('/cart/{id}',    [CustomerCartApiController::class, 'update']);
        Route::delete('/cart/{id}',   [CustomerCartApiController::class, 'remove']);
        Route::delete('/cart',        [CustomerCartApiController::class, 'clear']);
        Route::post('/cart/coupon',   [CustomerCartApiController::class, 'applyCoupon']);
        Route::delete('/cart/coupon', [CustomerCartApiController::class, 'removeCoupon']);

        // Orders
        Route::get('/orders',                [CustomerOrderApiController::class, 'index']);
        Route::get('/orders/{id}',           [CustomerOrderApiController::class, 'show']);
        Route::post('/orders/{id}/cancel',   [CustomerOrderApiController::class, 'cancel']);
        Route::post('/orders/{id}/return',   [CustomerOrderApiController::class, 'requestReturn']);

        // Appointments
        Route::get('/appointments',                      [CustomerAppointmentApiController::class, 'index']);
        Route::post('/appointments',                     [CustomerAppointmentApiController::class, 'store']);
        Route::get('/appointments/{id}',                 [CustomerAppointmentApiController::class, 'show']);
        Route::post('/appointments/{id}/cancel',         [CustomerAppointmentApiController::class, 'cancel']);
        Route::patch('/appointments/{id}/reschedule',    [CustomerAppointmentApiController::class, 'reschedule']);

        // Notifications
        Route::get('/notifications',                          [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification}/read',    [NotificationController::class, 'markAsRead']);
        Route::patch('/notifications/mark-all-read',          [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{notification}',        [NotificationController::class, 'destroy']);
    });
});
// =============================================================================
// Admin API  (prefix: /api/admin)
// =============================================================================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::get('/dashboard/stats',          'Api\AdminDashboardController@stats')->name('api.admin.dashboard.stats');
    Route::get('/dashboard/earnings',       'Api\AdminDashboardController@earnings')->name('api.admin.dashboard.earnings');
    Route::get('/dashboard/user-orders',    'Api\AdminDashboardController@userOrders')->name('api.admin.dashboard.user-orders');
    
    // Settings
    Route::get('/settings/currency',        'Api\AdminSettingsController@currency')->name('api.admin.settings.currency');
    Route::get('/settings/placeholder',     'Api\AdminSettingsController@placeholder')->name('api.admin.settings.placeholder');
    Route::get('/settings/global',          'Api\AdminSettingsController@global')->name('api.admin.settings.global');
    Route::get('/settings/payment-methods', 'Api\AdminSettingsController@paymentMethods')->name('api.admin.settings.payment-methods');
    
    // Users
    Route::get('/users',                    'Api\AdminUsersController@index')->name('api.admin.users.index');
    Route::get('/users/{id}',               'Api\AdminUsersController@show')->name('api.admin.users.show');
    Route::post('/users',                   'Api\AdminUsersController@store')->name('api.admin.users.store');
    Route::post('/users/{id}',              'Api\AdminUsersController@update')->name('api.admin.users.update');
    Route::post('/users/{id}/send-password-reset', 'Api\AdminUsersController@sendPasswordReset')->name('api.admin.users.send-password-reset');
    Route::post('/users/{id}/wallet-topup', 'Api\AdminUsersController@walletTopup')->name('api.admin.users.wallet-topup');
    
    // Vendors
    Route::get('/vendors/top',              'Api\AdminVendorsController@top')->name('api.admin.vendors.top');
    
    // Orders
    Route::get('/orders/recent',            'Api\AdminOrdersController@recent')->name('api.admin.orders.recent');
    
    // Payouts
    Route::get('/payouts/recent',           'Api\AdminPayoutsController@recent')->name('api.admin.payouts.recent');
    
    // Categories
    Route::get('/categories',               'Api\AdminCategoriesController@index')->name('api.admin.categories.index');
    Route::post('/categories',              'Api\AdminCategoriesController@store')->name('api.admin.categories.store');
    Route::get('/categories/{id}',          'Api\AdminCategoriesController@show')->name('api.admin.categories.show');
    Route::put('/categories/{id}',          'Api\AdminCategoriesController@update')->name('api.admin.categories.update');
    Route::delete('/categories/{id}',       'Api\AdminCategoriesController@destroy')->name('api.admin.categories.destroy');
    
    // Email Templates
    Route::get('/email-templates',          'Api\AdminEmailTemplatesController@index')->name('api.admin.email-templates.index');
    Route::post('/email-templates',         'Api\AdminEmailTemplatesController@store')->name('api.admin.email-templates.store');
    Route::get('/email-templates/{id}',     'Api\AdminEmailTemplatesController@show')->name('api.admin.email-templates.show');
    Route::put('/email-templates/{id}',     'Api\AdminEmailTemplatesController@update')->name('api.admin.email-templates.update');
    Route::delete('/email-templates/{id}',  'Api\AdminEmailTemplatesController@destroy')->name('api.admin.email-templates.destroy');
    
    // Vendors (list and operations)
    Route::get('/vendors-list',             'Api\AdminVendorsListController@index')->name('api.admin.vendors-list.index');
    Route::put('/vendors/{id}/status',      'Api\AdminVendorsListController@updateStatus')->name('api.admin.vendors.update-status');
    Route::delete('/vendors/{id}',          'Api\AdminVendorsListController@destroy')->name('api.admin.vendors.destroy');
});