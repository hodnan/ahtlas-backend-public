<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Administration\Intelligence\KpiResult;
use App\Models\Modules\Administration\Intelligence\KpiSector;
use App\Models\Modules\Administration\Intelligence\KpiSectorDaily;
use App\Models\Modules\Employee\SectorN1Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class KpiEmployeeService
{
    public static function getKpis()
    {
        return  KpiRelated::groupBy(['indicator_id'])->get(['indicator_id']);
    }

    public static function setKpiEmployeeH0SubResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->groupBy([
                'kpi_results.username',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'employee_dailies.sector_n2_id',
                'employee_dailies.hierarchical_level',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                kpi_results.username as username,             
                employee_dailies.sector_n1_id, 
                employee_dailies.sector_n2_id, 
                employee_dailies.hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                    WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                0 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiEmployeeH1SubResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->whereNotNull('employee_dailies.manager_n1_id')
            ->groupBy([
                'employee_dailies.manager_n1_id',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'employee_dailies.sector_n2_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                employee_dailies.manager_n1_id as username,             
                employee_dailies.sector_n1_id, 
                employee_dailies.sector_n2_id, 
                1 as hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                    WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                0 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiEmployeeH2SubResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->groupBy([
                'employee_dailies.manager_n2_id',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'employee_dailies.sector_n2_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                employee_dailies.manager_n2_id as username,             
                employee_dailies.sector_n1_id, 
                employee_dailies.sector_n2_id, 
                2 as hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                    WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                0 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiEmployeeH0ConResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->groupBy([
                'kpi_results.username',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'employee_dailies.hierarchical_level',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                kpi_results.username as username,             
                employee_dailies.sector_n1_id, 
                0 as sector_n2_id, 
                employee_dailies.hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                    WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                1 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiEmployeeH1ConResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->groupBy([
                'employee_dailies.manager_n1_id',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                employee_dailies.manager_n1_id as username,             
                employee_dailies.sector_n1_id, 
                0 as sector_n2_id, 
                1 as hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                1 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function setKpiEmployeeH2ConResults($month, $indicatorId, $chunkN = 100)
    {
        $startCurrentMonth = Carbon::parse($month)->startOfDay()->firstOfMonth()->toDateString();
        $endCurrentMonth = Carbon::parse($month)->startOfDay()->endOfMonth()->toDateString();
        $updatedAt = Carbon::now()->format('Y-m-d H:i:s');

        $kpis = KpiResult::leftJoin('employee_dailies', function ($join) {
            $join->on('kpi_results.username', '=', 'employee_dailies.username')
                ->on('kpi_results.date_ref', '=', 'employee_dailies.date_ref');
        })
            ->leftJoin('indicators', 'indicators.id', '=', 'kpi_results.indicator_id')
            ->whereBetween('kpi_results.date_ref', [$startCurrentMonth, $endCurrentMonth])
            ->where('kpi_results.indicator_id', $indicatorId)
            ->where('employee_dailies.hierarchical_level', 0)
            ->whereNotNull('employee_dailies.sector_n2_id')
            ->groupBy([
                'employee_dailies.manager_n2_id',
                'kpi_results.indicator_id',
                'employee_dailies.sector_n1_id',
                'indicators.is_percent',
                'indicators.calc'
            ])
            ->selectRaw("
                employee_dailies.manager_n2_id as username,             
                employee_dailies.sector_n1_id, 
                0 as sector_n2_id, 
                2 as hierarchical_level,
                kpi_results.indicator_id, 
                sum(factor_0) as factor_0, 
                sum(factor_1) as factor_1, 
                sum(factor_1) / NULLIF(sum(factor_0),0) as avg,
                CASE 
                WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                1 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
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
                WHEN indicators.is_percent = true THEN COALESCE((sum(factor_1) / NULLIF(sum(factor_0),0)) * 100,0)
                    WHEN indicators.calc = 0 THEN COALESCE(sum(factor_1) / NULLIF(sum(factor_0),0),0)
                    WHEN indicators.calc = 1 THEN COALESCE(sum(factor_1),0)
                end as result,
                0 as consolidated,
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
                $item['month_ref'] = $startCurrentMonth;
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
                $item['updated_at'] = $updatedAt;
                return  $item;
            }, $data);

            try {
                KpiEmployee::insert($data);
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }

    public static function getKpiEmployeeResults($data)
    {
        return   KpiEmployee::with(['sectorN1Id', 'sectorN2Id', 'indicatorId'])
            ->where('report_kpi_employees.date_ref', $data['date_ref'])
            ->where(function ($query) use ($data) {
                if ($data['username']) {
                    $query->where('report_kpi_employees.username', $data['username']);
                }
            })
            ->where(function ($query) use ($data) {
                if ($data['manager']) {
                    $query->where('employees.manager_n1_id', $data['manager']);
                    $query->where('report_kpi_employees.username', '<>', $data['manager']);
                }
            })
            ->where('consolidated', $data['consolidated'])
            ->leftJoin('employees', 'employees.username', '=', 'REPORT_KPI_EMPLOYEES.username')
            ->orderBy('employees.name')
            ->orderBy('report_kpi_employees.sector_n1_id')
            ->orderBy('report_kpi_employees.indicator_id')
            ->orderBy('report_kpi_employees.sector_n2_id')
            ->orderBy('report_kpi_employees.consolidated', 'desc')
            ->get([
                'report_kpi_employees.username',
                'employees.name',
                'report_kpi_employees.hierarchical_level',
                'report_kpi_employees.sector_n1_id',
                'report_kpi_employees.sector_n2_id',
                'report_kpi_employees.indicator_id',
                'report_kpi_employees.consolidated',
                'employees.manager_n1_id',
                'employees.manager_n2_id',
                'report_kpi_employees.result',
                'report_kpi_employees.updated_at',
                'report_kpi_employees.created_at',
            ]);
    }

    public static function getManagers($request  = null)
    {
        $date = $request['date'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');

        $sectors = KpiEmployee::where(1, 1)
            ->where('month_ref', $date)
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
