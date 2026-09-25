<?php

use App\Http\Controllers\Core\AuthController;
use App\Http\Controllers\Module\ForMe\TradeTimeController;
use App\Http\Controllers\Modules\People\Employee\BathroomController;
use App\Http\Controllers\Modules\People\Employee\EmployeeSelectController;
use App\Http\Controllers\Modules\TacticalCenter\Report\ReportController;
use App\Http\Middleware\RoutePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Rotas não autenticadas
Route::middleware(['guest'])->group(function () {
    require_once __DIR__ . '/modules/Guest.php';
});

// Rotas autenticadas
// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['auth:sanctum', 'route.permission'])->group(function () {
    Route::get('/home', fn() => '');
    require_once __DIR__ . '/modules/Core.php';
    require_once __DIR__ . '/modules/Addon.php';
    require_once __DIR__ . '/modules/ForMe.php';
    require_once __DIR__ . '/modules/Administration.php';
    require_once __DIR__ . '/modules/Management.php';
    require_once __DIR__ . '/modules/People.php';
    require_once __DIR__ . '/modules/TacticalCenter.php';
});
Route::get('/download/{report:uuid}', [ReportController::class, 'download']);
Route::post('/download/{report:uuid}', [ReportController::class, 'download']);


Route::get('/bathroom/{username}', [BathroomController::class, 'index']); 

