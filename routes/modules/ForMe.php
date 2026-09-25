<?php

use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusPanelMyTeamController;
use App\Http\Controllers\Modules\Administration\Incentives\RV\TermToSignatureController;
use App\Http\Controllers\Modules\ForMe\LinkController;
use App\Http\Controllers\Modules\ForMe\TradeTimeController;
use Illuminate\Support\Facades\Route;


Route::prefix('for-me')->group(function () {
    
    Route::prefix('links')->group(function () {
        Route::get('/', [LinkController::class, 'linkDisplay']);       
    });

    Route::prefix('trade')->group(function () {
        Route::get('/time', [TradeTimeController::class, 'display']);
        Route::post('/time', [TradeTimeController::class, 'display']);
        Route::post('/time/store', [TradeTimeController::class, 'timeStore']);
        Route::post('/time/update', [TradeTimeController::class, 'timeUpdate']);
        // Route::post('/time/mytrades', [TradeTimeController::class, 'myTrades']);
        Route::get('/time-admin', [TradeTimeController::class, 'displayAdmin']);
        Route::post('/time-admin', [TradeTimeController::class, 'displayAdmin']);
        Route::post('/time-admin/alltrades/download', [TradeTimeController::class, 'allTradesDownload']);
    });

    Route::prefix('incentives')->group(function () {
        Route::get('/terms-to-signature', [TermToSignatureController::class, 'index']);       
        Route::post('/terms-to-signature', [TermToSignatureController::class, 'index']);       
        Route::post('/terms-to-signature/update/{signature}', [TermToSignatureController::class, 'update']);       
        Route::post('/terms-to-signature/show/{signature}', [TermToSignatureController::class, 'show']);    
        Route::get('/bonus', [BonusPanelMyTeamController::class, 'index']);     
        Route::post('/bonus', [BonusPanelMyTeamController::class, 'index']);        
    });
    
});
