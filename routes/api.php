<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ApiController;

Route::middleware('api_key')->group(function() {
    Route::get('/orders', [ApiController::class, 'ordersList']);
    Route::get('/order/create', [ApiController::class, 'orderCreate']);
    Route::get('/order/read', [ApiController::class, 'orderRead']);
    Route::get('/order/update', [ApiController::class, 'orderUpdate']);
    Route::get('/order/delete', [ApiController::class, 'orderDelete']);
});

