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
