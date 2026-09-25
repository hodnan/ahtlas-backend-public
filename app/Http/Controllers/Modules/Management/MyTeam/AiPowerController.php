<?php

namespace App\Http\Controllers\Modules\Management\MyTeam;

use App\Http\Controllers\Controller;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Management\MyTeam\AiEmployeePower;
use App\Models\Modules\Management\MyTeam\AiEmployeePowerAction;
use App\Services\Modules\Management\MyTeam\AiPowerService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class AiPowerController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $managers = $request->isMethod('get') ? Employee::with(['avatar'])->where('active', 1)
                ->whereIn('hierarchical_level', [2, 3, 4, 5])
                ->get(['username', 'name', 'position_summary', 'hierarchical_level']) : [];
            $aiPowers = AiPowerService::list($request->all());
            $actions = $request->isMethod('get') ? AiEmployeePowerAction::distinct('action')->orderBy('action')->pluck('action') : [];
            $sectorsN1 = $request->isMethod('get') ? SectorN1::get() : [];

            return ApiResponser::success(null, null, ['managers' => $managers, 'aiPowers' => $aiPowers, 'actions' => $actions, 'sectorsN1' =>  $sectorsN1]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [['Erro ao carregar lista ']]);
        }
    }

   
}
