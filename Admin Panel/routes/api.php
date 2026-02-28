<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\VendorDocumentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
// Customer MVC API
use App\Http\Controllers\Api\CustomerAuthApiController;
use App\Http\Controllers\Api\CustomerCartApiController;
use App\Http\Controllers\Api\CustomerOrderApiController;
use App\Http\Controllers\Api\CustomerAppointmentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {

    // -------------------------------------------------------------------------
    // Vendor (Restaurant / Store) — replaces Firestore JS calls in Blade views
    // -------------------------------------------------------------------------
    Route::apiResource('vendors', VendorController::class);
    Route::patch('/vendors/{vendor}/approve',        [VendorController::class, 'approve']);
    Route::patch('/vendors/{vendor}/toggle-active',  [VendorController::class, 'toggleActive']);
    Route::post('/vendors/{vendor}/wallet/credit',   [VendorController::class, 'walletCredit']);

    // -------------------------------------------------------------------------
    // Orders — replaces Firestore JS order queries in Blade views
    // -------------------------------------------------------------------------
    Route::get('/orders',             [ApiOrderController::class, 'index']);
    Route::get('/orders/{order}',     [ApiOrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [ApiOrderController::class, 'updateStatus']);

    // -------------------------------------------------------------------------
    // Zones — replaces database.collection('zone') Firestore calls
    // -------------------------------------------------------------------------
    Route::apiResource('zones', ZoneController::class);
    Route::post('/zones/{zone}/areas',             [ZoneController::class, 'addArea']);
    Route::get('/zones/{zone}/areas',              [ZoneController::class, 'getAreas']);
    Route::delete('/zones/{zone}/areas/{area}',    [ZoneController::class, 'removeArea']);

    // -------------------------------------------------------------------------
    // Vendor Documents
    // -------------------------------------------------------------------------
    Route::get('/vendors/{vendorId}/documents',          [VendorDocumentController::class, 'index']);
    Route::post('/vendors/{vendorId}/documents',         [VendorDocumentController::class, 'store']);
    Route::put('/vendors/documents/{document}',          [VendorDocumentController::class, 'update']);
    Route::delete('/vendors/documents/{document}',       [VendorDocumentController::class, 'destroy']);
    Route::get('/vendors/documents/{document}/download', [VendorDocumentController::class, 'download']);

    // -------------------------------------------------------------------------
    // Notifications
    // -------------------------------------------------------------------------
    Route::get('/notifications',                       [NotificationController::class, 'index']);
    Route::post('/notifications',                      [NotificationController::class, 'store']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read',       [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}',     [NotificationController::class, 'destroy']);
});

// =============================================================================
// Customer MVC API  (prefix: /api/customer)
// =============================================================================
Route::prefix('customer')->group(function () {

    // ── Public auth (no token required) ──────────────────────────────────────
    Route::post('/auth/register',      [CustomerAuthApiController::class, 'register']);
    Route::post('/auth/login',         [CustomerAuthApiController::class, 'login']);
    Route::post('/password/forgot',    [CustomerAuthApiController::class, 'forgotPassword']);

    // ── Available experts (public, for booking page) ──────────────────────────
    Route::get('/appointments/experts', [CustomerAppointmentApiController::class, 'experts']);

    // ── CSRF cookie for SPA / static-HTML clients ─────────────────────────────
    // GET /api/customer/csrf  → sets XSRF-TOKEN cookie (Sanctum SPA flow)
    Route::get('/csrf', fn () => response()->noContent());

    // ── Protected (Bearer token required) ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth & profile
        Route::post('/auth/logout',    [CustomerAuthApiController::class, 'logout']);
        Route::get('/auth/me',         [CustomerAuthApiController::class, 'me']);
        Route::put('/auth/profile',    [CustomerAuthApiController::class, 'updateProfile']);
        Route::post('/auth/password',  [CustomerAuthApiController::class, 'changePassword']);

        // Cart
        Route::get('/cart',            [CustomerCartApiController::class, 'index']);
        Route::post('/cart/add',       [CustomerCartApiController::class, 'add']);
        Route::patch('/cart/{id}',     [CustomerCartApiController::class, 'update']);
        Route::delete('/cart/{id}',    [CustomerCartApiController::class, 'remove']);
        Route::delete('/cart',         [CustomerCartApiController::class, 'clear']);
        Route::post('/cart/coupon',    [CustomerCartApiController::class, 'applyCoupon']);
        Route::delete('/cart/coupon',  [CustomerCartApiController::class, 'removeCoupon']);

        // Orders
        Route::get('/orders',                   [CustomerOrderApiController::class, 'index']);
        Route::get('/orders/{id}',              [CustomerOrderApiController::class, 'show']);
        Route::post('/orders/{id}/cancel',      [CustomerOrderApiController::class, 'cancel']);
        Route::post('/orders/{id}/return',      [CustomerOrderApiController::class, 'requestReturn']);

        // Appointments
        Route::get('/appointments',             [CustomerAppointmentApiController::class, 'index']);
        Route::post('/appointments',            [CustomerAppointmentApiController::class, 'store']);
        Route::get('/appointments/{id}',        [CustomerAppointmentApiController::class, 'show']);
        Route::post('/appointments/{id}/cancel',    [CustomerAppointmentApiController::class, 'cancel']);
        Route::patch('/appointments/{id}/reschedule', [CustomerAppointmentApiController::class, 'reschedule']);
    });
});
