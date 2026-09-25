<?php

use App\Http\Controllers\Addons\ServicePosition\ServicePositionOccupationController;
use App\Http\Controllers\Core\AuthController;
use App\Http\Controllers\Modules\TacticalCenter\Report\ReportReadController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'store']);
});

Route::prefix('tactical-center')->group(function () {
    Route::prefix('reports')->group(function () {
        Route::post('/read', [ReportReadController::class, 'store']);
    });
});

Route::prefix('addon')->group(function () {
    Route::prefix('service-positions')->group(function () {
        Route::post('/occupation', [ServicePositionOccupationController::class, 'store']);
    });  
});
