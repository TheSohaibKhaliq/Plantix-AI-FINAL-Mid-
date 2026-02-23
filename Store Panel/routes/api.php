<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreOrderController;
use App\Http\Controllers\Api\StoreCouponController;
use App\Http\Controllers\Api\StorePayoutController;
use App\Http\Controllers\Api\WithdrawMethodApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Store Panel
|--------------------------------------------------------------------------
|
| All Firestore JS calls in Blade views should migrate to these REST
| endpoints. The views use fetch() / axios to hit these routes instead
| of calling database.collection('...') directly.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:vendor,admin'])->group(function () {

    // -------------------------------------------------------------------------
    // Products (replaces vendor_products Firestore collection)
    // -------------------------------------------------------------------------
    Route::apiResource('products', ProductController::class);
    Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggleActive']);

    // -------------------------------------------------------------------------
    // Orders (replaces Firestore order queries in orders/*.blade.php)
    // -------------------------------------------------------------------------
    Route::get('/orders',                        [StoreOrderController::class, 'index']);
    Route::get('/orders/{order}',                [StoreOrderController::class, 'show']);
    Route::patch('/orders/{order}/status',       [StoreOrderController::class, 'updateStatus']);
    Route::get('/orders/{order}/print',          [StoreOrderController::class, 'printData']);
    Route::post('/orders/{order}/notification',  [StoreOrderController::class, 'sendNotification']);

    // -------------------------------------------------------------------------
    // Coupons (replaces coupons Firestore collection)
    // -------------------------------------------------------------------------
    Route::apiResource('coupons', StoreCouponController::class);

    // -------------------------------------------------------------------------
    // Payouts / wallet transactions
    // -------------------------------------------------------------------------
    Route::get('/payouts',             [StorePayoutController::class, 'index']);
    Route::post('/payouts/request',    [StorePayoutController::class, 'requestPayout']);
    Route::get('/wallet-transactions', [StorePayoutController::class, 'walletTransactions']);

    // -------------------------------------------------------------------------
    // Withdraw Methods (replaces Firestore stripe/paypal keys on vendor doc)
    // -------------------------------------------------------------------------
    Route::get('/withdraw-methods',          [WithdrawMethodApiController::class, 'index']);
    Route::post('/withdraw-methods',         [WithdrawMethodApiController::class, 'store']);
    Route::put('/withdraw-methods/{method}', [WithdrawMethodApiController::class, 'update']);
    Route::delete('/withdraw-methods/{method}', [WithdrawMethodApiController::class, 'destroy']);
});

