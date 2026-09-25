<?php

namespace App\Services\Modules\Administration;

use App\Models\Addon\Calendar;
use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Administration\Intelligence\KpiResult;
use App\Models\Modules\Administration\Intelligence\KpiSector;
use App\Models\Modules\Administration\Intelligence\KpiSectorDaily;
use App\Models\Modules\Employee\SectorN1Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class KpiSectorService
{
    public static function getKpis()
    {
        return  KpiRelated::groupBy(['indicator_id'])->get(['indicator_id']);
    }

    public static function setKpiSectorResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->groupBy([
                'kpi_results.sector_n1_id',
                'kpi_results.indicator_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                kpi_results.sector_n1_id, 
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                    WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                end as result,
                max(kpi_results.date_ref) as created_at
                ")
            ->get();

        LazyCollection::make(function () use ($kpis) {
            foreach ($kpis as $value) {
                yield $value;
            }
        })->chunk($chunkN)->each(function ($chunk) use ($startCurrentMonth, $updatedAt) {
            $data = $chunk->toArray();

            $data = array_map(function ($item) use ($startCurrentMonth, $updatedAt) {
                $item['date_ref'] = $startCurrentMonth;
                $item['updated_at'] = $updatedAt;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                return  $item;
            }, $data);

            try {
                KpiSector::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiSectorDailyResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');


        $kpis = KPIResult::leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween("date_ref", [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', '=', $indicatorId)
            // ->where('kpi_results.sector_n1_id', '=', $sectorN1Id)
            ->groupBy([
                'date_ref',
                'kpi_results.sector_n1_id',
                'kpi_results.indicator_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->orderBy('date_ref')
            ->selectRaw("
                    '$startCurrentMonth' as month_ref,
                    date_ref,
                    sector_n1_id, 
                    $indicatorId  as indicator_id, 
                    sum(factor_0) as factor_0, 
                    sum(factor_1) as factor_1, 
                    sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                    CASE 
                        WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                        WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                        WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    end as result,
                    kpi_results.date_ref as created_at")                    
            ->get();

        LazyCollection::make(function () use ($kpis) {
            foreach ($kpis as $value) {
                yield $value;
            }
        })->chunk($chunkN)->each(function ($chunk) use ($updatedAt)   {
            $data = $chunk->toArray();

            $data = array_map(function ($item) use ($updatedAt)  {
                $item['updated_at'] = $updatedAt;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                return  $item;
            }, $data);
            try {
                KpiSectorDaily::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function getKpiEmployeeResults($data)
    {
        return   KpiEmployee::with(['sectorN1Id', 'sectorN2Id', 'indicatorId'])
            ->where('kpi_result_employees.date_ref', $data['date_ref'])
            ->where(function ($query) use ($data) {
                if ($data['username']) {
                    $query->where('kpi_result_employees.username', $data['username']);
                }
            })
            ->where(function ($query) use ($data) {
                if ($data['manager']) {
                    $query->where('employees.manager_n1_id', $data['manager']);
                    $query->where('kpi_result_employees.username', '<>', $data['manager']);
                }
            })
            ->where('consolidated', $data['consolidated'])
            ->leftJoin('employees', 'employees.username', '=', 'kpi_result_employees.username')
            ->orderBy('employees.name')
            ->orderBy('kpi_result_employees.sector_n1_id')
            ->orderBy('kpi_result_employees.indicator_id')
            ->orderBy('kpi_result_employees.sector_n2_id')
            ->orderBy('kpi_result_employees.consolidated', 'desc')
            ->get([
                'kpi_result_employees.username',
                'employees.name',
                'kpi_result_employees.hierarchical_level',
                'kpi_result_employees.sector_n1_id',
                'kpi_result_employees.sector_n2_id',
                'kpi_result_employees.indicator_id',
                'kpi_result_employees.consolidated',
                'employees.manager_n1_id',
                'employees.manager_n2_id',
                'kpi_result_employees.result',
                'kpi_result_employees.updated_at',
                'kpi_result_employees.created_at',
            ]);
    }

    public static function getManagers($request  = null)
    {
        $date = $request['date'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');

        $sectors = KpiEmployee::where(1, 1)
            ->where('date_ref', $date)
            ->distinct()
            ->pluck('sector_n1_id')
            ->toArray();

        $data = SectorN1Manager::with('manager_username')
            ->whereHas('manager_username', function ($query) {
                $query->whereNotNull('name');
                $query->where('hierarchical_level', '=', 1);
            })
            ->whereIn('sector_n1_id', $sectors)
            ->distinct()
            ->get('manager_username')->toArrAy();

        $data = collect($data)->sortBy([['manager_username.hierarchical_level.id', 'desc'], ['manager_username.name', 'asc']])->values();

        return $data;
    }
}
