<?php

namespace App\Http\Controllers\Modules\Administration\Intelligence;

use App\Models\Modules\Administration\Intelligence\KpiResult;
use App\Http\Controllers\Controller;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\KpiSourceService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class KpiRelatedController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $kpis = KpiRelated::with(['indicator', 'sectorN1', 'source.owner'])->get();
            return ApiResponser::success(null, null, [
                'kpis' => $kpis,

            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        try {

            return  KpiSourceService::setUpdates();
            return ApiResponser::success(null, null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar atualizar ', [[['Erro ao carregar atualizar ']]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function download()
    {
        $datehRef = Carbon::now()->startOfMonth()->format('Y-m-d');

        $kpis = KpiRelated::with(['indicator', 'sectorN1', 'source.owner.managerN2', 'report'])->get()->toArray();

        $kpis =  array_map(function ($item) {

            $temp['source_id'] = $item['source_id'];
            $temp['source_status'] = $item['source']['active']['name'] ?? 0;
            $temp['sector_n1_id'] = $item['sector_n1_id'];
            $temp['sector_n1_label'] = $item['sector_n1']['label'] ??  $item['sector_n1_id'];
            $temp['indicator_id'] = $item['indicator_id'];
            $temp['indicator_label'] = $item['indicator']['label'];            
            $temp['report_id'] = $item['report_id'];
            $temp['report_title'] = isset($item['report']) ? $item['report']['title'] : null;

            $temp['owner'] = $item['source']['owner']['username'] ?? null;
            $temp['owner_label'] = $item['source']['owner']['label'] ?? null; 

            $temp['manager_n2'] = $item['source']['owner']['manager_n2']['username'] ?? null;
            $temp['manager_n2_label'] = $item['source']['owner']['manager_n2']['label'] ?? null;

            $temp['last_update'] = $item['last_update'];
            $temp['last_result'] = $item['last_result'];

            return $temp;
        }, $kpis);

        $csvContent = makeCsv($kpis);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=" . $datehRef . "_kpi_updates.csv",
        ];

        return response()->make($csvContent, 200, $headers);
    }
}
