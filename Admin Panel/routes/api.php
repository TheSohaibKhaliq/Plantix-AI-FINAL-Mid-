<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\VendorDocumentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;

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
