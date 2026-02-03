<?php

use Illuminate\Support\Facades\Route;
use Webkul\VnRegionalShipping\Http\Controllers\ShippingController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('vn-regional-shipping/shipping/estimate', [ShippingController::class, 'getBaseRate'])
        ->name('vn-regional-shipping.shipping.estimate');
    
    Route::post('vn-regional-shipping/shipping/calculate', [ShippingController::class, 'calculateShippingFee'])
        ->name('vn-regional-shipping.shipping.calculate');
});
