<?php

use App\Http\Controllers\Core\Notify\NotificationController;
use App\Http\Controllers\Core\Notify\NotificationUserController;
use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusAdminController;
use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusFiscalYearController;
use App\Http\Controllers\Modules\Administration\Core;
use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusBlockController;
use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusBlockItemController;
use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusPanelController;
use App\Http\Controllers\Modules\Administration\Intelligence\IndicatorsController;
use App\Http\Controllers\Modules\Administration\Intelligence\KpiRelatedController;
use App\Http\Controllers\Modules\Administration\Intelligence\KpiSourceController;
use App\Http\Controllers\Modules\Administration\Planning\PlanningFileLoadController;
use App\Http\Controllers\Modules\Administration\Incentives\RV\TermController;
use App\Http\Controllers\Modules\Administration\Incentives\RV\TermCopyController;
use App\Http\Controllers\Modules\Administration\Incentives\RV\TermEvaluationController;

use App\Http\Controllers\Modules\Administration\Incentives\RV\TermSignatureController;
use App\Http\Controllers\Modules\Management\MyTeam\AiPowerController;

use Illuminate\Support\Facades\Route;


Route::prefix('administration')->group(function () {

    // Route::prefix('core')->group(function () {
    //     Route::get('/info', [Core::class, 'infoDisplay']);
    //     Route::get('/modules', fn() => '');
    //     Route::get('/accesses', fn() => '');
    //     Route::get('/notify', [NotificationController::class, 'index']);
    //     Route::post('/notify', [NotificationController::class, 'index']);
    //     Route::post('/notify/store', [NotificationController::class, 'store']);
    //     Route::post('/notify/user', [NotificationUserController::class, 'index']);
    // });

    Route::prefix('incentives')->group(function () {
        Route::prefix('rv')->group(function () {
            Route::get('/terms', [TermController::class, 'index']);
            Route::post('/terms', [TermController::class, 'index']);
            Route::post('/terms/store', [TermController::class, 'store']);
            Route::post('/terms/show/{id}', [TermController::class, 'show']);
            Route::post('/terms-copy/show/{id}', [TermCopyController::class, 'show']);
            Route::post('/terms/update/{term}', [TermController::class, 'update']);
            Route::get('/terms-evaluations', [TermEvaluationController::class, 'index']);
            Route::post('/terms-evaluations', [TermEvaluationController::class, 'index']);
            Route::post('/terms-evaluations-download', [TermEvaluationController::class, 'download']);
            Route::get('/terms-query', [TermSignatureController::class, 'index']);
            Route::post('/terms-query', [TermSignatureController::class, 'index']);
            Route::post('/terms-query/{username}', [TermSignatureController::class, 'show']);
        });
        Route::prefix('bonus')->group(function () {
            Route::get('/admin', [BonusAdminController::class, 'index']);
            Route::post('/admin', [BonusAdminController::class, 'index']);

            Route::post('/fiscalyear', [BonusFiscalYearController::class, 'index']);
            Route::post('/fiscalyear/store', [BonusFiscalYearController::class, 'store']);
            Route::post('/fiscalyear/update/{fiscalYear:public_id}', [BonusFiscalYearController::class, 'update']);

            Route::post('/block', [BonusBlockController::class, 'index']);
            Route::post('/block/store', [BonusBlockController::class, 'store']);
            Route::post('/block/item/store', [BonusBlockItemController::class, 'store']);
            Route::post('/block/item/update/{bonusBlockItem}', [BonusBlockItemController::class, 'update']);
            Route::post('/block/item/show/{bonusBlockItem}', [BonusBlockItemController::class, 'show']);
            Route::post('/block/update/{bonusBlock:public_id}', [BonusBlockController::class, 'update']);
            Route::post('/block/show/{bonusBlock:public_id}', [BonusBlockController::class, 'show']);

            Route::post('/panel/store', [BonusPanelController::class, 'store']);
            Route::post('/panel/update/{bonusPanel:public_id}', [BonusPanelController::class, 'update']);
            Route::post('/panel/validate', [BonusPanelController::class, 'validate']);
            Route::post('/panel/show/{bonusPanel:public_id}', [BonusPanelController::class, 'show']);
            Route::post('/panel', [BonusPanelController::class, 'index']);
        });
    });

    Route::prefix('intelligence')->group(function () {
        Route::get('/indicators', [IndicatorsController::class, 'index']);
        Route::post('/indicators', [IndicatorsController::class, 'index']);
        Route::post('/indicators/store', [IndicatorsController::class, 'store']);
        Route::post('/indicators/update', [IndicatorsController::class, 'update']);

        Route::get('/kpi-updates', [KpiRelatedController::class, 'index']);
        Route::post('/kpi-updates/update', [KpiRelatedController::class, 'update']);
        Route::post('/kpi-updates/download', [KpiRelatedController::class, 'download']);
        Route::get('/kpi-sources', [KpiSourceController::class, 'index']);
        Route::post('/kpi-sources', [KpiSourceController::class, 'index']);
        Route::post('/kpi-sources/store', [KpiSourceController::class, 'store']);
        Route::post('/kpi-sources/show/{kpiSource}', [KpiSourceController::class, 'show']);
        Route::post('/kpi-sources/update/{kpiSource}', [KpiSourceController::class, 'update']);

        Route::get('/tokens', [AiPowerController::class, 'index']);
    });

    Route::prefix('planning')->group(function () {
        Route::get('/file-load', [PlanningFileLoadController::class, 'index']);
        Route::post('/file-load', [PlanningFileLoadController::class, 'index']);
        Route::post('/file-load/store', [PlanningFileLoadController::class, 'store']);
        Route::get('/file-load/update/{fileLoad}', [PlanningFileLoadController::class, 'update']);
        Route::post('/file-load/download/{fileLoad}', [PlanningFileLoadController::class, 'download']);
        Route::post('/file-load/download-model/{id}', [PlanningFileLoadController::class, 'downloadModel']);
    });
});
