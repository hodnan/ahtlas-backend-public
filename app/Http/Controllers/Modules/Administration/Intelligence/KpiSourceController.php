<?php

namespace App\Http\Controllers\Modules\Administration\Intelligence;

use App\Models\Modules\Administration\Intelligence\KpiSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Intelligence\KpiSourceRequest;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\TacticalCenter\Report\Report;
use App\Services\Modules\Administration\KpiSourceInterface;
use App\Services\Modules\Administration\KpiSourceService;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class KpiSourceController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $owners = $request->isMethod('get') ? Employee::with(['avatar'])->where('active', 1)
                ->where('manager_n3_id', config('ahtlas.kpi_sources.owner_manager_n3'))
                ->get(['username', 'name', 'position_summary', 'hierarchical_level']) : [];

            $indicators = $request->isMethod('get') ? Indicator::where('active', 1)->get(['id', 'name']) : [];
            $reports = $request->isMethod('get') ? Report::where('active', 1)->get(['id', 'title']) : [];
            $sectors = $request->isMethod('get') ? SectorN1::where('active', 1)->get(['id', 'name']) : [];
            $statuses = $request->isMethod('get') ?  KpiSourceInterface::STATUSES : [];

            $kpiSources = KpiSource::with(['owner'])->orderBy('id', 'desc')->get();
            return ApiResponser::success(null, null, [
                'kpiSources' => $kpiSources,
                'owners' => $owners,
                "indicators" => $indicators,
                'reports' => $reports,
                'statuses' => $statuses,
                'sectors' => $sectors
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KpiSourceRequest $request)
    {
        try {
            KpiSourceService::store($request);
            return ApiResponser::success('Fonte criada com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar fonte ', [[['Erro ao criar fonte ']]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KpiSource $kpiSource)
    {
        try {
            $kpiSource->load(['indicator', 'owner.avatar', 'sectors']);
            return ApiResponser::success(null, null, ['kpiSource' => $kpiSource]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar fonte ', [[['Erro ao criar fonte ']]]);
        }
    }

  
    /**
     * Update the specified resource in storage.
     */
    public function update(KpiSourceRequest $request, KpiSource $kpiSource)
    {
        try {
            KpiSourceService::update($request, $kpiSource);
            return ApiResponser::success('Fonte atualizado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar Fonte ', [['Erro ao atualizar Fonte']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KpiSource $kpiSource)
    {
        //
    }
}
