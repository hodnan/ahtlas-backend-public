<?php

namespace App\Http\Controllers\Modules\Management\ControlCenter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Management\ControlCenter\ControlCenterTrackingRequest;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;

use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ControlCenterTrackingController extends Controller
{
    use ApiResponser;
    
    public function index(Request $request)
    {
        try {
            $monthRef = Carbon::parse($request->month_ref);

            $trackings = ControlCenterService::getTrackings($monthRef)->where('active', 1)->values();
           
            return ApiResponser::success(null, null, [             
                'trackings' => $trackings     
            ]);

        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ControlCenterTrackingRequest $request)
    {
        try {
            $data = $request->all();
            $monthRef = Carbon::parse($data['month_ref'])->format('Y-m-d');

            $data['daily_goals'] =  $data['daily_goals'];
            $data['owners'] = ControlCenterService::getOwners($data['indicator_id'], $data['sector_n1_id']);

            ControlCenterTrackingModel::create( $data);
            Cache::forget('CDC_TRACKINGS_'. $monthRef)  ;
            return ApiResponser::success('Acompanhamento criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar Acompanhamento', [['Erro ao criar Acompanhamento']]);
        }       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ControlCenterTrackingRequest $request, ControlCenterTrackingModel $tracking)
    {
        try {

            $data = $request->all();           
            $tracking->notify_level = $data['notify_level'];
            $tracking->goal = $data['goal'];
            $tracking->bypass = $data['bypass'];
            $tracking->q1 = $data['q1'];
            $tracking->q2 = $data['q2'];
            $tracking->q3 = $data['q3'];
            $tracking->q4 = $data['q4'];
            $tracking->daily_goals = [];
            $tracking->daily_goals = $data['daily_goals'];
            $tracking->active = $data['active'];
            $tracking->owners = ControlCenterService::getOwners($data['indicator_id'], $data['sector_n1_id']);

            $tracking->save();
            Cache::forget('CDC_TRACKINGS_'.  $tracking->month_ref)  ;
            return ApiResponser::success('Grupo atualizado com sucesso', null,['xxx' => $data['daily_goals']] );
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar grupo', [['Erro ao atualizar grupo']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
