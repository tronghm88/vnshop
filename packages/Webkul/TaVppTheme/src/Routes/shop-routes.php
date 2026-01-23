<?php

use Illuminate\Support\Facades\Route;
use Webkul\TaVppTheme\Http\Controllers\ShippingController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('tavpp/shipping/estimate', [ShippingController::class, 'getBaseRate'])
        ->name('tavpp.shipping.estimate');
    
    Route::post('tavpp/shipping/calculate', [ShippingController::class, 'calculateShippingFee'])
        ->name('tavpp.shipping.calculate');
});
