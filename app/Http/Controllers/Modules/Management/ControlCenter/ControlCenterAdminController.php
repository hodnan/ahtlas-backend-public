<?php

namespace App\Http\Controllers\Modules\Management\ControlCenter;

use App\Http\Controllers\Controller;
use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Management\ControlCenter\ControlCenterGroupSector;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class ControlCenterAdminController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $monthRef = Carbon::parse($request->month_ref);

            $kpisSectors = KpiRelated::distinct('sector_n1_id')->pluck('sector_n1_id');

            $sectors = SectorN1::whereIn('id', $kpisSectors )->orderBy('id')->get(['id', 'name']);
            
            $groups = ControlCenterGroupSector::orderBy('id')->get();
           
            $kpis = ControlCenterService::getKpis();
           
            $trackings = ControlCenterService::getTrackings($monthRef);

            $indicators =  $request->isMethod('get') ? Indicator::where('active',1)->get(['id', 'name']) : null;
            $notifications = ControlCenterInterface::NOTIFICATIONS;

            return ApiResponser::success(null, null, [
                'indicators'=> $indicators,
                'notifications'=> $notifications,
                'sectors'=> $sectors,
                'groups'=> $groups,
                'kpis'=> $kpis,
                'trackings'=> $trackings,
            ]);

        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
