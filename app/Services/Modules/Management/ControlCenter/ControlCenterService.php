<?php

namespace App\Services\Modules\Management\ControlCenter;

use App\Jobs\Modules\Management\ControlCenter\ControlCenterTrackingEmailJob;
use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Administration\Intelligence\KpiResult;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Management\ControlCenter\ControlCenterGroupSector;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultEmployee;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultSector;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Core\User\UserService;
use App\Services\Modules\Administration\IndicatorInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ControlCenterService implements ControlCenterInterface
{
    public static function getSectors()
    {
        $combinedResults =  Cache::remember(
            'CDC_SECTORS',
            60 * 60 * 24,
            function () {
                $kpisSectors = KpiRelated::distinct('sector_n1_id')->pluck('sector_n1_id');

                $sectorsN1 = SectorN1::whereIn('id', $kpisSectors)
                    ->where('active', 1)
                    ->select(
                        'id',
                        'name',
                        DB::raw("jsonb_build_array(jsonb_build_object('sector_n1_id', id)) as sectors")
                    );

                $controlCenterSectors = ControlCenterGroupSector::where('active', 1)->select('id', 'name', DB::raw("sectors::jsonb as sectors"));

                return   $sectorsN1->union($controlCenterSectors)->orderBy('id')->get();
            }
        );
        return $combinedResults;
    }
    public static function getSectorsAll()
    {
        $combinedResults =  Cache::remember(
            'CDC_SECTORS_ALL',
            60 * 60 * 24,
            function () {
                $kpisSectors = KpiRelated::distinct('sector_n1_id')->pluck('sector_n1_id');

                $sectorsN1 = SectorN1::whereIn('id', $kpisSectors)                  
                    ->select(
                        'id',
                        'name',
                        DB::raw("jsonb_build_array(jsonb_build_object('sector_n1_id', id)) as sectors")
                    );

                $controlCenterSectors = ControlCenterGroupSector::select('id', 'name', DB::raw("sectors::jsonb as sectors"));

                return   $sectorsN1->union($controlCenterSectors)->orderBy('id')->get();
            }
        );
        return $combinedResults;
    }

    public static function getGroupSectorIds(int $sectorN1Id)
    {
        $sectors = ControlCenterService::getSectorsAll();

        try {
            $trackingSectors = $sectors->where('id', $sectorN1Id)->first();
            $trackingSectors = array_map(function ($item) {
                return $item['sector_n1_id'];
            }, $trackingSectors->sectors);

            return   $trackingSectors;
        } catch (\Throwable $th) {
            Log::error('CCP - getGroupSectorIds', [$sectorN1Id, $th->getMessage()]);
        }
    }

    public static function getKpis()
    {
        $sectorsWithIndicators = Cache::remember(
            'CDC_KPIS',
            60 * 60 * 24,
            function () {
                $sectorsN1 = self::getSectors();

                $kpis = KpiRelated::with(['indicator'])
                    ->orderBy('sector_n1_id')
                    ->orderBy('indicator_id')
                    ->get(['indicator_id', 'sector_n1_id']);

                // Mapeando cada setor em $sectorsN1 e associando os indicadores
                return $sectorsN1->map(function ($sector) use ($kpis) {
                    // Coletar todos os sector_n1_id deste setor
                    $sectorIds = collect($sector->sectors)->pluck('sector_n1_id')->toArray();

                    // Filtrar os KPIs que têm sector_n1_id presente no array de sectorIds
                    $matchingKpis = $kpis->whereIn('sector_n1_id', $sectorIds);

                    // Adicionar os indicator_id na lista
                    $sector->indicators = $matchingKpis->pluck('indicator')->map(function ($indicator) {
                        if ($indicator) {
                            return [
                                'id' => $indicator['id'],
                                'name' => $indicator['name'],
                                'label' => $indicator['label'],
                            ];
                        }
                    })->unique()->values()->toArray();
                    return $sector;
                });
            }
        );
        return $sectorsWithIndicators;
    }

    public static function getTrackings($monthRef)
    {
        $monthRef = Carbon::parse($monthRef)->format('Y-m-d');

        $trackings = Cache::remember(
            'CDC_TRACKINGS_' . $monthRef,
            60 * 60 * 24,
            function () use ($monthRef) {
                $trackings = ControlCenterTrackingModel::with(['sectorN1', 'sectorN1Group', 'indicator'])
                    ->where('month_ref', $monthRef)
                    ->orderBy('sector_n1_id')
                    ->orderBy('indicator_id')
                    ->get()
                    ->toArray();

                return collect($trackings)->map(function ($tracking) {
                    $tracking['sector_n1'] = $tracking['sector_n1'] ?? $tracking['sector_n1_group'];
                    $tracking['chart'] =  ControlCenterService::getChartData($tracking['sector_n1'], $tracking['indicator_id'], $tracking['month_ref']);
                    return $tracking;
                });
            }
        );
        return $trackings;
    }

    public static function getStages($request)
    {
        $monthRef =  $request->month_ref ? Carbon::parse($request->month_ref)->format('Y-m-d') : null; 
        $dateRef = $request->date_ref ? Carbon::parse($request->date_ref)->format('Y-m-d') : null;
        $createdAt = $request->created_at ? Carbon::parse($request->created_at)->format('Y-m-d') : null;

        $trackings = ControlCenterTrackingStage::with(['sectorN1', 'sectorN1Group', 'indicator'])
            ->where(function ($query) use ($dateRef) {
                if ($dateRef) {
                    $query->where('date_ref', $dateRef );
                }
            })
            ->where(function ($query) use ($createdAt) {
                if ($createdAt) {
                    $query->where('created_at', 'like', $createdAt . "%");
                }
            })
            ->where(function ($query) use ($monthRef) {
                if ($monthRef) {
                    $query->where('month_ref',  $monthRef);
                }
            })
            ->orderBy('sector_n1_id')
            ->orderBy('indicator_id')
            ->get()
            ->toArray();

        return collect($trackings)->map(function ($tracking) {
            $tracking['sector_n1'] = $tracking['sector_n1'] ?? $tracking['sector_n1_group'];
            return $tracking;
        });

        return $trackings;
    }

    public static function getStagesForFilter($monthRef)
    {
        $monthRef = Carbon::parse($monthRef)->format('Y-m-d');

        $trackings = Cache::remember(
            'CDC_STAGES_' . $monthRef,
            60 * 60 * 24,
            function () use ($monthRef) {
                $trackings = ControlCenterTrackingStage::with(['sectorN1', 'sectorN1Group', 'indicator'])
                    ->where('month_ref', $monthRef)
                    ->where('stage', '>', 0)
                    ->where('validated', 1)
                    ->orderBy('sector_n1_id')
                    ->orderBy('indicator_id')
                    ->orderBy('date_ref', 'desc')
                    ->get()
                    ->toArray();

                return collect($trackings)->map(function ($tracking) {

                    $tracking['sector_n1'] = $tracking['sector_n1'] ?? $tracking['sector_n1_group'];
                    $tracking['sector_n1'] = $tracking['sector_n1'] ??  $tracking['sector_n1'] = ['id' =>  $tracking['sector_n1_id'], 'label' =>  $tracking['sector_n1_id'] . ' - Não localizado'];

                    $temp['id'] = (int) $tracking['id'];
                    $temp['month_ref'] = $tracking['month_ref'];
                    $temp['date_ref'] = $tracking['date_ref'];
                    $temp['indicator_id'] = $tracking['indicator_id'];
                    $temp['sector_n1_id'] = $tracking['sector_n1_id'];
                    $temp['label'] = $tracking['date_ref']
                        . ' | '
                        . $tracking['sector_n1']['label']
                        . ' | '
                        . $tracking['indicator']['label']
                        . " Stage: "
                        . $tracking['stage']['id'];

                    return $temp;
                });
            }
        );
        return $trackings;
    }

    public static function getOwners(int $indicator, int $sector)
    {
        $sectors = ControlCenterService::getSectorsAll()->where('id', $sector)->first();

        $sectors = array_map(function ($item) {
            return $item['sector_n1_id'];
        }, $sectors->sectors);

        $kpi = KpiRelated::with(['source.owner', 'sectorN1'])
            ->whereIn('sector_n1_id', $sectors)
            ->where('indicator_id', $indicator)
            ->get(['source_id', 'sector_n1_id'])->toArray();

        // Mapeia o array para pegar apenas o 'owner' de cada 'source'
        $owners = array_map(function ($item) {
            $temp['owner']['username'] = $item['source']['owner']['username'];
            $temp['owner']['name'] = $item['source']['owner']['name'];
            $temp['owner']['label'] = $item['source']['owner']['label'];
            $temp['sector_n1'] = $item['sector_n1'];
            return $temp;
        }, $kpi);

        // Remove duplicados
        $uniqueOwners = collect($owners)->unique()->values()->toArray();

        return $uniqueOwners;
    }

    public static function setResultSector($tracking, $trackingSectors)
    {
        $calc = $tracking->indicator->calc['id'];
        $is_percent = $tracking->indicator->is_percent['value'];

        foreach ($tracking->daily_goals as $daily_goal) {

            $result = [
                'month_ref' => $tracking->month_ref,
                'date_ref' => $daily_goal['date_ref'],
                'sector_n1_id' => $tracking->sector_n1_id,
                'indicator_id' => $tracking->indicator_id,
                'goal' => $daily_goal['goal'],
                'bypass' => $tracking->bypass,
                'factor_0' => null,
                'factor_1' => null,
                'result' => null,
            ];

            try {
                $data = KpiResult::whereIn('sector_n1_id', $trackingSectors)
                    ->where('indicator_id', $tracking->indicator_id)
                    ->whereBetween('date_ref', [$tracking->month_ref, $daily_goal['date_ref']])
                    ->groupBy('indicator_id')
                    ->select(
                        DB::raw("'$tracking->month_ref' as month_ref"),
                        DB::raw("'" . $daily_goal['date_ref'] . "' as date_ref"),
                        DB::raw("'$tracking->sector_n1_id' as sector_n1_id"),
                        DB::raw("'$tracking->indicator_id' as indicator_id"),
                        DB::raw("'" . $daily_goal['goal'] . "' as goal"),
                        DB::raw("'$tracking->bypass' as bypass"),
                        DB::raw('MAX(date_ref) as max_date'),
                        DB::raw('SUM(factor_0) as factor_0'),
                        DB::raw('SUM(factor_1) as factor_1'),
                        DB::raw('ROUND(CAST(SUM(factor_1) / NULLIF(SUM(factor_0), 0) AS numeric), 4) as result')
                    )
                    ->first()
                    ->toArray();
            } catch (\Throwable $th) {
                //throw $th;
            }

            if (isset($data['max_date'])) {
                $dailyGoal = Carbon::parse($daily_goal['date_ref']);
                $maxDate = Carbon::parse($data['max_date']);
                if ($dailyGoal->lte($maxDate)) {
                    $result = $data;
                }
            }

            $result['result'] = $calc == IndicatorInterface::CALC_SUM ? $result['factor_1'] : $result['result'];
            $result['result'] = $is_percent ? $result['result'] * 100 : $result['result'];

            $result['result']  = $result['factor_0'] == null &&  $result['factor_1'] == null ? null : $result['result'];

            $unique = [
                'month_ref' => $result['month_ref'],
                'date_ref' => $result['date_ref'],
                'sector_n1_id' => $result['sector_n1_id'],
                'indicator_id' => $result['indicator_id'],
            ];

            try {
                ControlCenterTrackingDailyResultSector::updateOrCreate($unique, $result);
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
    }

    public static function setTrackingStage($tracking, $result)
    {

        // se existir resultado prossegue a verificação e o mês de referência seja o mesmo
        if ($result && $result['month_ref'] == $tracking->month_ref) {

            $bypass = $result['goal'] * (1 + $result['bypass']);

            // Verifica se está fora da meta
            $offGoal = $result->indicator['direction']['id'] ? $result['result'] < $bypass : $result['result'] > $bypass;

            // Pega o ultimo stage
            $lastStage = self::getTrackingLastStage($tracking, $tracking->month_ref);

            // data do ultimo resultado
            $resultDateRef = Carbon::parse($result['date_ref']);
            // data do resultado do ultimo stage
            $lastStageDateRef = Carbon::parse($lastStage['date_ref'])->addDay(ControlCenterInterface::STAGE_INTERVAL)->startOfDay();
            // data de criação do ultimo stage
            $lastStageCreatedAt = Carbon::parse($lastStage['created_at'])->addDay(ControlCenterInterface::STAGE_INTERVAL)->startOfDay();

            $stage = [
                'month_ref' => $result['month_ref'],
                'date_ref' => $result['date_ref'],
                'sector_n1_id' => $tracking->sector_n1_id,
                'indicator_id' => $tracking->indicator_id,
                'factor_0' => $result['factor_0'],
                'factor_1' => $result['factor_1'],
                'result' => $result['result'],
                'goal' => $result['goal'],
                'bypass' => $result['bypass'],
            ];

            $stage['stage'] = null;

            // sendo fora da meta verifica se a data do resultado é maior ou igual a data do ultimo stage + 5 e data da criação +5
            if ($resultDateRef->gte($lastStageDateRef) && $resultDateRef->gte($lastStageCreatedAt) && $offGoal) {
                $stage['stage'] = $lastStage['stage'] < 4  ? $lastStage['stage'] + 1 : $lastStage['stage'];
            }

            // sendo dentro da meta e data do resultado for superior ao ultimo stage registra stage 0
            if (!$offGoal && (int) $lastStage['stage'] > 0) {
                $stage['stage'] = 0;
            }

            // se existir stage salva o dado na tabela 
            if (isset($stage['stage']) && $stage['stage'] >= 0) {
                self::saveStage($tracking, $stage);
            }
        }
    }

    public static function saveStage($tracking, $stage)
    {
        try {
            $newStage = ControlCenterTrackingStage::create($stage);
            // self::setTrackingEmployeeResult($tracking, $newStage);
        } catch (\Throwable $th) {
            Log::error('CCP salva stage', [$th->getMessage(), $stage]);
        }
    }

    public static function getTrackingLastResult($tracking)
    {
        // Pega o ultimo resultado do kpi dentro do mês 
        $result = ControlCenterTrackingDailyResultSector::with(['indicator'])
            ->whereNotNull('result')
            ->where('month_ref', $tracking->month_ref)
            ->where('sector_n1_id', $tracking->sector_n1_id)
            ->where('indicator_id', $tracking->indicator_id)
            ->orderBy('date_ref', 'desc')
            ->select(
                'month_ref',
                'date_ref',
                'sector_n1_id',
                'indicator_id',
                'factor_0',
                'factor_1',
                'result',
                'goal',
                'bypass',
            )
            ->first();

        return $result;
    }

    public static function getTrackingLastStage($tracking)
    {
        $lastStageDefault = [
            'month_ref' => '1900-01-01',
            'date_ref' => '1900-01-01',
            'created_at' => '1900-01-01',
            'stage' => [
                'id' => 0
            ],
        ];

        // Pega o ultimo stage gerado dentro do mesmo mês
        $lastStage = ControlCenterTrackingStage::where('sector_n1_id', $tracking->sector_n1_id)
            ->where('indicator_id', $tracking->indicator_id)
            ->where('month_ref', $tracking->month_ref)
            ->where('validated', 1)
            ->orderBy('date_ref', 'desc')
            ->select(
                'month_ref',
                'date_ref',
                'created_at',
                'stage',
            )
            ->first();


        $lastStage = $lastStage ? $lastStage->toArray() : $lastStageDefault;

        $lastStage['stage'] =  $lastStage['stage']['id'];

        // usa valores padrão se não existir um ultimo stage 
        return $lastStage;
    }

    public static function getChartData($sectorN1Id, $indicatorId, $monthRef)
    {
        $trackings = ControlCenterTrackingDailyResultSector::where('month_ref', $monthRef)
            ->where('sector_n1_id', $sectorN1Id)
            ->where('indicator_id', $indicatorId)
            ->orderBY('date_ref')
            ->get([
                'month_ref',
                'date_ref',
                'result',
                'goal',
                DB::RAW(" goal * (1+ bypass) as bypass"),
                'stage'
            ])->toArray();

        $chart = [
            'date_ref' => [],
            'result' => [],
            'goal' => [],
            'bypass' => [],
            'color' => [],
            'border' => [],
        ];

        foreach ($trackings as $tracking) {

            $chart['date_ref'][] =  substr($tracking['date_ref'], -2);
            $chart['result'][] =  $tracking['result'] == null ? null : (float) $tracking['result'];
            $chart['goal'][] = (float) $tracking['goal'];
            $chart['bypass'][] = (float) $tracking['bypass'];
            $chart['color'][] = $tracking['stage']['rgb'];
            $chart['border'][] = $tracking['stage']['border'];
        }

        return $chart;
    }

    public static function sendMail($tracking, $stage)
    {
        try {
            $mails = self::getEmailList($tracking, $stage);
            ControlCenterTrackingEmailJob::dispatch($stage->id, $mails)
                ->onQueue(ControlCenterInterface::QUEUE);
        } catch (\Throwable $th) {
            LOG::error("CCP MAIL $stage->id", [$th->getMessage()]);
        }
    }

    public static function getStageById($stageId)
    {
        $stage = ControlCenterTrackingStage::with(['sectorN1', 'sectorN1Group',  'indicator'])
            ->find($stageId)->toArray();

        $stage['sector_n1'] = $stage['sector_n1'] ?? $stage['sector_n1_group'];

        unset($stage['sector_n1_group']);

        return $stage;
    }

    public static function setTrackingEmployeeResult($tracking, $stage)
    {
        $calc = $stage->indicator['calc']['id'];
        $direction = $stage->indicator['direction']['id'];
        $is_percent = $stage->indicator['is_percent']['value'];
        $bypass = $stage->goal * (1 + $stage->bypass);
        $sectors = self::getGroupSectorIds($stage->sector_n1_id);

        $kpiResults = KpiResult::whereBetween('date_ref', [$stage->month_ref, $stage->date_ref])
            ->whereIn('sector_n1_id', $sectors)
            ->where('indicator_id', $stage->indicator_id)
            ->groupBy(['sector_n1_id', 'indicator_id', 'username'])
            ->select(
                DB::raw("'$stage->month_ref' as month_ref"),
                DB::raw("'$stage->date_ref' as date_ref"),
                DB::raw("sector_n1_id"),
                DB::raw("indicator_id"),
                DB::raw("'$stage->id' as stage_id"),
                DB::raw("username"),
                DB::raw($stage->goal . " as goal"),
                DB::raw($stage->bypass . " as bypass"),
                DB::raw('SUM(factor_0) as factor_0'),
                DB::raw('SUM(factor_1) as factor_1'),
                DB::raw('ROUND(CAST(SUM(factor_1) / NULLIF(SUM(factor_0), 0) AS numeric), 4) as result'),
                DB::raw($stage->stage['id'] . " as stage"),
            )
            ->get();


        // calcula o quadrante
        $kpiResults->map(function ($kpiResult) use ($calc, $is_percent, $direction, $bypass, $tracking) {
            $kpiResult['result'] = $calc == IndicatorInterface::CALC_SUM ? $kpiResult['factor_1'] : $kpiResult['result'];
            $kpiResult['result'] = $is_percent ? $kpiResult['result'] * 100 : $kpiResult['result'];

            $kpiResult['delta'] =  $direction ? $kpiResult['result'] - $bypass : $bypass - $kpiResult['result'];
            $kpiResult['delta'] = round((($kpiResult['delta'] / $bypass) + 1) * 100, 2);

            $kpiResult['quadrant'] = $kpiResult['delta'] < $tracking->q4 ? 4 : 0; // 90
            $kpiResult['quadrant'] = $kpiResult['delta'] >= (float) $tracking->q3 ? 3 : $kpiResult['quadrant']; // 90
            $kpiResult['quadrant'] = $kpiResult['delta'] >= (float) $tracking->q2 ? 2 : $kpiResult['quadrant']; // 100
            $kpiResult['quadrant'] = $kpiResult['delta'] >= (float) $tracking->q1 ? 1 : $kpiResult['quadrant']; //120

            return $kpiResult;
        });

        // verifica se existem resultados
        if ($kpiResults) {
            $data = $kpiResults->toArray();

            try {
                // deleta resultados dentro dos mesmos critérios para evitar duplicicades
                ControlCenterTrackingDailyResultEmployee::where('date_ref', $stage->date_ref)
                    ->whereIn('sector_n1_id', $sectors)
                    ->where('indicator_id', $stage->indicator_id)
                    ->delete();
            } catch (\Throwable $e) {
                Log::error('CCP - Colaboradores - Deleta dados', [$e->getMessage()]);
            }

            LazyCollection::make($data)
                ->chunk(999)
                ->each(function ($chunk) {
                    try {
                        foreach ($chunk as $item) {
                            if ($item['quadrant'] > 2) {
                                ControlCenterTrackingDailyResultEmployee::create($item);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::error('CCP - Colaboradores - Gera dados', [$e->getMessage(), $chunk->toArray()]);
                    }
                });
        }
    }

    public static function getEmployeeResults($stageId)
    {
        return ControlCenterTrackingDailyResultEmployee::with([
            'indicator',
            'sectorN1',
            'sectorN1Group',
            'employee.sectorN1',
            'employee.managerN1'
        ])
            ->where('stage_id', $stageId)
            ->get();
    }

    public static function getEmailList($tracking, $stage)
    {
        $mailsTo = collect([]);
        $mailsCC = collect(config('ahtlas.control_center.cdc_users'));
        $mailsBCC = collect(config('ahtlas.admins'));

        // Se não existir nivel de notificação, retorna array vazio
        if (!$tracking->notify_level) return [];

       
        // Se noticação for Gestores, lista gestores com fensores
        // if ($tracking->notify_level >= self::NOTIFY_MANAGERS) {

        //     $employees = ControlCenterTrackingDailyResultEmployee::leftJoin('employees', 'employees.username', '=', 'control_center_tracking_daily_result_employees.username')
        //         ->where('stage_id', $stage->id)
        //         ->groupBy([
        //             'employees.manager_n1_id',
        //             'employees.manager_n2_id',
        //             'employees.manager_n3_id',
        //             'employees.manager_n4_id',
        //             'employees.manager_n5_id'
        //         ])
        //         ->get([
        //             'employees.manager_n1_id',
        //             'employees.manager_n2_id',
        //             'employees.manager_n3_id',
        //             'employees.manager_n4_id',
        //             'employees.manager_n5_id'
        //         ]);

        //     if ($employees) {
        //         $employees =  $employees->toArray();

        //         $mailsTo = $mailsTo
        //             ->merge(collect($employees)->pluck('manager_n1_id'))
        //             ->merge(collect($employees)->pluck('manager_n2_id'))
        //             ->merge(collect($employees)->pluck('manager_n3_id'))
        //             ->merge(collect($employees)->pluck('manager_n4_id'));
        //     }
        // }

        // if (!$tracking->notify_level >= self::NOTIFY_DIRECTOR) {
        //     $mailsTo = $mailsTo->merge(collect($employees)->pluck('manager_n5_id'));
        // };

        // if (!$tracking->notify_level >= self::NOTIFY_CEO) {
        //     $mailsTo = $mailsTo->merge(config('ahtlas.ceo'));
        // };

        $mailsTo = $mailsTo->unique()->values();
        $mailsCC = $mailsCC->unique()->values();

        $mailsTo = $mailsTo->map(function ($item) {
            return UserService::getMail($item);
        });

        $mailsCC = $mailsCC->map(function ($item) {
            return UserService::getMail($item);
        });

        $mailsBCC = $mailsBCC->map(function ($item) {
            return UserService::getMail($item);
        });

        $mails = [
            'to' => $mailsTo,
            'cc' => $mailsCC,
            'bcc' => $mailsBCC,
        ];

        return $mails;
    }

   
}
