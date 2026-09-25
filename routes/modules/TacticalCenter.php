<?php

use App\Http\Controllers\Modules\TacticalCenter\Bulletin\BackofficeController;
use App\Http\Controllers\Modules\TacticalCenter\Bulletin\HourHourController;
use App\Http\Controllers\Modules\TacticalCenter\Report\ReportController;
use App\Http\Controllers\Modules\TacticalCenter\Report\ReportFavoriteController;
use App\Http\Controllers\Modules\TacticalCenter\Report\ReportFileController;
use Illuminate\Support\Facades\Route;


Route::prefix('tactical-center')->group(function () {
    Route::prefix('bulletin')->group(function () {
        Route::get('/hour-hour', [HourHourController::class, 'index']);
        Route::post('/hour-hour', [HourHourController::class, 'index']);
        Route::post('/hour-hour/show/{dt_data}/{nu_setor}', [HourHourController::class, 'show']);
        Route::get('/command-center', [HourHourController::class, 'index']);
        Route::post('/command-center/list', [HourHourController::class, 'list']);
        Route::get('/backoffice', [BackofficeController::class, 'index']);
        Route::post('/backoffice', [BackofficeController::class, 'index']);
        Route::post('/backoffice/filter', [BackofficeController::class, 'index']);
    });
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'index']);

        Route::get('/admin', [ReportController::class, 'index']);
        Route::post('/store', [ReportController::class, 'store']);
        Route::post('/show/{uuid}', [ReportController::class, 'show']);
        Route::post('/files/show/{uuid}/{year}', [ReportFileController::class, 'show']);
        Route::post('/files/store', [ReportFileController::class, 'store']);
        Route::post('/files/download/{report}', [ReportFileController::class, 'download']);

        Route::post('/update/{report:uuid}', [ReportController::class, 'update']);
        Route::post('/download/{report:uuid}', [ReportController::class, 'download']);
        Route::post('/favorite', [ReportFavoriteController::class, 'store']);
    });
});
