<?php

namespace App\Http\Controllers\Modules\Management\ControlCenter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Management\ControlCenter\ControlCenterGroupSectorRequest;
use App\Http\Requests\Modules\Management\ControlCenter\ControlCenterGroupSectorUpdateRequest;
use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Management\ControlCenter\ControlCenterGroupSector;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ControlCenterGroupSectorController extends Controller
{
    use ApiResponser;

   

    /**
     * Store a newly created resource in storage.
     */
    public function store(ControlCenterGroupSectorRequest $request)
    {
        try {
            $data = $request->all();
            $data['sectors'] = collect($data['sectors'])->map(function ($sector) {
                $sector['sector_n1_id'] = (int) $sector['sector_n1_id'];
                return $sector;
            })->toArray();

            ControlCenterGroupSector::create($data);

            Cache::forget('CDC_SECTORS');
            Cache::forget('CDC_KPIS');
            Cache::forget('CDC_SECTORS_ALL');
            return ApiResponser::success('Grupo criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar grupo', [['Erro ao criar grupo']]);
        }
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
    public function update(ControlCenterGroupSectorUpdateRequest $request, ControlCenterGroupSector $group)
    {
        try {
            $group->active = $request->active;
            $group->save();

            Cache::forget('CDC_SECTORS');
            Cache::forget('CDC_KPIS');

            return ApiResponser::success('Grupo atualizado com sucesso', null, null);
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
