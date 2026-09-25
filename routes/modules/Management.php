<?php

use App\Http\Controllers\Modules\Administration\Incentives\Bonus\BonusPanelMyTeamController;

use App\Http\Controllers\Modules\Administration\Incentives\RV\TermAdherenceController;
use App\Http\Controllers\Modules\Administration\Incentives\RV\TermToApproveController;
use App\Http\Controllers\Modules\Management\ControlCenter\ControlCenterAdminController;
use App\Http\Controllers\Modules\Management\ControlCenter\ControlCenterGroupSectorController;
use App\Http\Controllers\Modules\Management\ControlCenter\ControlCenterTrackingController;
use App\Http\Controllers\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultEmployeeController;
use App\Http\Controllers\Modules\Management\ControlCenter\ControlCenterTrackingStageController;
use App\Http\Controllers\Modules\Management\MyTeam\AiPowerController;

use Illuminate\Support\Facades\Route;


Route::prefix('management')->group(function () {

    Route::prefix('myteam')->group(function () {
        Route::get('/aipower', [AiPowerController::class, 'index']);
        Route::post('/aipower', [AiPowerController::class, 'index']);
        Route::get('/terms-to-approve', [TermToApproveController::class, 'index']);
        Route::post('/terms-to-approve', [TermToApproveController::class, 'index']);
        Route::post('/terms-to-approve/update/{term}', [TermToApproveController::class, 'update']);       
        Route::get('/terms-adherence', [TermAdherenceController::class, 'index']);    
        Route::post('/terms-adherence', [TermAdherenceController::class, 'index']);    
        Route::post('/terms-adherence/download', [TermAdherenceController::class, 'download']);     
        Route::get('/bonus', [BonusPanelMyTeamController::class, 'index']);     
        Route::post('/bonus', [BonusPanelMyTeamController::class, 'index']);     
    });
    Route::prefix('control-center')->group(function () {
        Route::get('/admin', [ControlCenterAdminController::class, 'index']);
        Route::post('/admin', [ControlCenterAdminController::class, 'index']);
        Route::post('/group/store', [ControlCenterGroupSectorController::class, 'store']);
        Route::post('/group/update/{group}', [ControlCenterGroupSectorController::class, 'update']);
        Route::get('/tracking', [ControlCenterTrackingController::class, 'index']);
        Route::post('/tracking', [ControlCenterTrackingController::class, 'index']);
        Route::post('/tracking/store', [ControlCenterTrackingController::class, 'store']);
        Route::post('/tracking/update/{tracking}', [ControlCenterTrackingController::class, 'update']);
        Route::get('/employee-results', [ControlCenterTrackingDailyResultEmployeeController::class, 'index']);
        Route::post('/employee-results', [ControlCenterTrackingDailyResultEmployeeController::class, 'index']);
        Route::post('/employee-download', [ControlCenterTrackingDailyResultEmployeeController::class, 'download']);
        Route::post('/stage-download', [ControlCenterTrackingStageController::class, 'download']);
        
        Route::get('/stages', [ControlCenterTrackingStageController::class, 'index']);
        Route::post('/stages', [ControlCenterTrackingStageController::class, 'index']);
        Route::post('/stages/update/{stage}', [ControlCenterTrackingStageController::class, 'update']);
    });

});
