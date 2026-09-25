<?php

use App\Http\Controllers\Core\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'index']);
    Route::post('/checking-session', [AuthController::class, 'show']);
    Route::delete('/logout', [AuthController::class, 'destroy']);   
});

