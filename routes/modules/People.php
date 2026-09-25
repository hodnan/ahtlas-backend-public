<?php

use App\Http\Controllers\Modules\People\Employee\EmployeeBirthdateOnMonthController;
use App\Http\Controllers\Modules\People\Employee\EmployeeCurrentController;
use App\Http\Controllers\Modules\People\Employee\EmployeeDailyController;
use App\Http\Controllers\Modules\People\Employee\EmployeeSelectController;
use Illuminate\Support\Facades\Route;

Route::prefix('people')->group(function () {
    Route::prefix('employee')->group(function () {
        Route::get('/current', [EmployeeCurrentController::class, 'index']);
        Route::post('/current', [EmployeeCurrentController::class, 'index']);
        Route::post('/current/show/{username}', [EmployeeCurrentController::class, 'show']);
        Route::get('/daily', [EmployeeDailyController::class, 'index']);
        Route::post('/daily', [EmployeeDailyController::class, 'index']);
        Route::post('/select', [EmployeeSelectController::class, 'index']);
        Route::post('/birthdate-on-month', [EmployeeBirthdateOnMonthController::class, 'index']);
    });
});
