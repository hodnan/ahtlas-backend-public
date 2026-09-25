<?php

use App\Http\Controllers\Addons\Assistant\AssistantSessionController;
use App\Http\Controllers\Addons\User\UserExternalController;
use Illuminate\Support\Facades\Route;

Route::prefix('addon')->group(function () {
    Route::prefix('assistant')->group(function () {
        Route::post('/chat', [AssistantSessionController::class, 'chat']);
    });  
    
    Route::prefix('user')->group(function(){
        Route::get('/external-email', [UserExternalController::class, 'index']);
    });
});
