<?php

namespace App\Services\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\TacticalCenter\Bulletin\Backoffice;
use App\Models\Modules\TacticalCenter\Bulletin\BackofficeEnvironment;
use App\Models\Modules\TacticalCenter\Bulletin\BackofficeMailing;
use App\Models\Modules\TacticalCenter\Bulletin\BackofficeQueue;
use App\Models\Modules\TacticalCenter\Bulletin\BackofficeStatus;
use App\Models\Modules\TacticalCenter\HeadCount\HeadCount;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class BulletinBackofficeService
{
    public static function getSectorsN1($date_start, $date_end)
    {
        $backofficeSectors = Backoffice::wherebetween('date_ref', [$date_start, $date_end])
            ->groupBy('sector_n1_id')
            ->pluck('sector_n1_id');

        return  SectorN1::whereIn('id', $backofficeSectors)->get(['id', 'name']);
    }

    public static function getManagers()
    {
        $managerN1Id = Backoffice::groupBy('manager_n1_id')
            ->pluck('manager_n1_id');

        $managerN2Id = Backoffice::groupBy('manager_n2_id')
            ->pluck('manager_n2_id');

        $managerN3Id = Backoffice::groupBy('manager_n3_id')
            ->pluck('manager_n3_id');

        $managerN4Id = Backoffice::groupBy('manager_n4_id')
            ->pluck('manager_n4_id');

        $managerN5Id = Backoffice::groupBy('manager_n5_id')
            ->pluck('manager_n5_id');

        $allManagers = $managerN1Id
            ->merge($managerN2Id)
            ->merge($managerN3Id)
            ->merge($managerN4Id)
            ->merge($managerN5Id)
            ->filter()
            ->unique()
            ->values();

        return  $allManagers;
    }

    public static function getStatuses()
    {
        return BackofficeStatus::orderBy('name')->get(['id', 'name', 'color']);
    }

    public static function getEnvironment($date_start, $date_end)
    {
        $environmentId = Backoffice::wherebetween('date_ref', [$date_start, $date_end])
            ->groupBy('environment_id')
            ->pluck('environment_id');

        return BackofficeEnvironment::whereIn('id', $environmentId)->get(['id', 'name']);
    }

    public static function getMailing($date_start, $date_end)
    {
        $mailingId = Backoffice::wherebetween('date_ref', [$date_start, $date_end])
            ->groupBy('mailing_id')
            ->pluck('mailing_id');

        return BackofficeMailing::whereIn('id', $mailingId)->get(['id', 'name']);
    }

    public static function getQueue($date_start, $date_end)
    {
        $queueId = Backoffice::wherebetween('date_ref', [$date_start, $date_end])
            ->groupBy('queue_id')
            ->pluck('queue_id');

        return BackofficeQueue::whereIn('id', $queueId)->get(['id', 'name']);
    }

    public static function setStatuses()
    {
        $data = DB::connection('mis_primary')
            ->table('DB_QUALIFICACAO.dbo.TB_QLF_BO_UNIFICADO_PAINEL_DM_STATUS')
            ->select([
                "CD_STATUS as id",
                "NO_STATUS AS name",
            ])
            ->get()->toArray();

        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {

                $data = $chunk->map(function ($item) {
                    $item->name = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $item->name);
                    return (array) $item;
                })->toArray();

                foreach ($data as $item) {
                    try {
                        $key = [
                            'id' => $item['id'],
                        ];

                        BackofficeStatus::updateOrCreate($key, $item);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            });
    }

    public static function setEnvironment()
    {
        $data = DB::connection('mis_primary')
            ->table('DB_QUALIFICACAO.dbo.TB_QLF_BO_UNIFICADO_PAINEL_DM_AMBIENTE')
            ->select([
                "SK_AMBIENTE as id",
                "NO_AMBIENTE AS name",
            ])
            ->get()->toArray();

        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {

                $data = $chunk->map(function ($item) {
                    $item->name = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $item->name);
                    return (array) $item;
                })->toArray();

                foreach ($data as $item) {
                    try {
                        $key = [
                            'id' => $item['id'],
                        ];
                        BackofficeEnvironment::updateOrCreate($key, $item);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            });
    }

    public static function setMailing()
    {
        $data = DB::connection('mis_primary')
            ->table('DB_QUALIFICACAO.dbo.TB_QLF_BO_UNIFICADO_PAINEL_DM_MAILING')
            ->select([
                "SK_MAILING as id",
                "NO_MAILING AS name",
            ])
            ->get()->toArray();

        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {

                $data = $chunk->map(function ($item) {
                    $item->name = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $item->name);
                    return (array) $item;
                })->toArray();

                foreach ($data as $item) {
                    try {
                        $key = [
                            'id' => $item['id'],
                        ];
                        BackofficeMailing::updateOrCreate($key, $item);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            });
    }

    public static function setQueue()
    {
        $data = DB::connection('mis_primary')
            ->table('DB_QUALIFICACAO.dbo.TB_QLF_BO_UNIFICADO_PAINEL_DM_FILA')
            ->select([
                "SK_FILA as id",
                "NO_FILA AS name",
            ])
            ->get()->toArray();

        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {

                $data = $chunk->map(function ($item) {

                    $item->name = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $item->name);
                    return (array) $item;
                })->toArray();

                foreach ($data as $item) {
                    try {
                        $key = [
                            'id' => $item['id'],
                        ];
                        BackofficeQueue::updateOrCreate($key, $item);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            });
    }

    public static function setBulletin($date)
    {
        $month = carbon::parse($date)->startOfMonth();

        $data = DB::connection('mis_primary')
            ->table('DB_QUALIFICACAO.dbo.TB_QLF_BO_UNIFICADO_PAINEL_FT')
            ->where('DT_DATA', $date)
            ->select([
                DB::RAW("CONCAT('usr', CD_MAT_SUPERVISOR) as manager_n1_id"),
                DB::RAW("CONCAT('usr', CD_MAT_COORDENADOR) as manager_n2_id"),
                DB::RAW("CONCAT('usr', CD_MAT_GERENTE_LOCAL) as manager_n3_id"),
                DB::RAW("CONCAT('usr', CD_MAT_GERENTE_RELAC) as manager_n4_id"),
                DB::RAW("CONCAT('usr', CD_MAT_DIRETOR) as manager_n5_id"),
                "NU_PROT_ID as protocol",
                DB::RAW("CONCAT('usr', CD_MATRICULA) as username"),
                "CD_SETOR as sector_n1_id",
                "HR_JORNADA as working_hours",
                "DT_DATA as date_ref",
                "HR_INI as time_start",
                "HR_FIM as time_end",
                'NU_FINALIZADO as finished',
                'NU_TMP_TRATATIVA as time_handle',
                'NU_TMP_TRATATIVA as time_productive',
                "SK_AMBIENTE as environment_id",
                "SK_FILA as queue_id",
                "SK_MAILING as mailing_id",
                "SK_STATUS as status_id",
            ])
            ->get()
            ->toArray();

        if ($data) {
            // deleta dados no periodo
            Backoffice::where('date_ref', '=', $date)->delete();
        }

        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) use ($month) {
                try {
                    $data = $chunk->map(function ($item) use ($month) {
                        $item->month_ref = $month;
                        $item->unique = 0;
                        return (array) $item;
                    })->toArray();

                    Backoffice::insert($data);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            });
    }

    public static function setSimultaneous($date)
    {
        $backoffice = Backoffice::where('date_ref', $date)
            ->where('status_id', 2)
            ->orderBy('time_start')
            ->get([
                'date_ref',
                'username',
                'protocol',
                'time_start',
                'time_end',
            ]);

        foreach ($backoffice as $value) {

            $query = Backoffice::where('date_ref', $date)
                ->where('username', $value->username)
                ->where('status_id', 2)
                ->whereNull('is_simultaneous')
                ->where(function ($item) use ($value) {
                    $item->whereBetween('time_start', [$value->time_start, $value->time_end]);
                    $item->orWhereBetween('time_end', [$value->time_start, $value->time_end]);
                });

            $results = $query->orderBy('time_start')
                ->get([
                    'date_ref',
                    'username',
                    'protocol',
                    'time_start',
                    'time_end',
                ]);

            if ($results->count() > 1) {

                try {

                    $query->update(['is_simultaneous' => 1]);
                } catch (\Throwable $th) {
                    Log::error('Bolletin Backoffice - setSimultaneous',  [$th->getMessage()]);
                }
            }
        }
    }

    public static function getBackoffices($data)
    {
        $data['date_start'] = $data['date_start'];
        $data['date_end'] = $data['date_end'];
        $data['time_start'] = Carbon::parse($data['time_start'])->format('H:i:s');
        $data['time_end'] = Carbon::parse($data['time_end'])->addSeconds(59)->format('H:i:s');

        $group = $data['group'];

        // $cacheName = "bulletin_backoffices_" . implode('_', array_values($data));
        try {
            $backoffices = Backoffice::with(['environment', 'queue', 'mailing', 'sectorN1', 'employee', 'managerN1', 'managerN2', 'managerN3', 'status'])
                ->whereBetween('date_ref', [$data['date_start'],  $data['date_end']])
                ->whereBetween('time_start', [$data['time_start'],  $data['time_end']])
                ->whereBetween('time_end', [$data['time_start'],  $data['time_end']])
                ->groupBy([$group, 'date_ref'])
                // ->take(3)
                ->where(function ($item) use ($data) {

                    if (isset($data['manager'])) {
                        $manager = $data['manager'];
                        $item->whereRaw(DB::Raw("'$manager' in (manager_n1_id, manager_n2_id, manager_n3_id, manager_n4_id)"));
                    }

                    if (isset($data['sector'])) {
                        $item->where('sector_n1_id', $data['sector']);
                    }

                    if (isset($data['employee'])) {
                        $item->where('username', $data['employee']);
                    }

                    if (isset($data['environment'])) {
                        $item->where('environment_id', $data['environment']);
                    }

                    if (isset($data['mailing'])) {
                        $item->where('mailing_id', $data['mailing']);
                    }

                    if (isset($data['queue'])) {
                        $item->where('queue_id', $data['queue']);
                    }
                    if (isset($data['status'])) {
                        $item->where('status_id', $data['status']);
                    }
                })
                ->orderBy($group)
                ->orderBy('date_ref')
                ->orderBy(DB::RAW('MIN(time_start)'))
                ->get(
                    [
                        "date_ref",
                        "$group as title",
                        "$group",
                        DB::raw("round((sum(time_productive) / nullif(sum(time_handle),0)) * 100,0) AS time_productive_percente"),
                        DB::raw("MAKE_INTERVAL(secs => SUM(time_productive)) AS time_productive"),
                        DB::raw("MAKE_INTERVAL(secs => SUM(time_handle)) AS time_handle"),
                        DB::raw('SUM("unique") as unique'),
                        DB::raw('SUM(finished) as finished'),
                        DB::raw('round((SUM(finished) / nullif(cast(count(*) as numeric),0) * 100),2) as finished_percent'),
                        DB::raw('count(*) as volume'),
                        DB::raw("SUM(is_simultaneous) as is_simultaneous"),
                        DB::raw("CAST(SUM(time_productive) / nullif(count(*),0) as int)  as average_time_handle"),
                    ]
                );

            $backoffices =  $backoffices->map(function ($item) use ($data, $group) {


                $hc = HeadCount::where('date_ref',  $item['date_ref'])->where('sector_n1_id',  $item['sector_n1_id'])->first();

                $item->hc = $hc->hc_real ?? 0;

                $item->protocols = Backoffice::with([
                    'mailing',
                    'sectorN1',
                    'employee',
                    'managerN1',
                    'environment',
                    'status',
                ])->whereBetween('date_ref', [$data['date_start'], $data['date_end']])
                    ->whereBetween('time_start', [$data['time_start'], $data['time_end']])
                    ->whereBetween('time_end', [$data['time_start'], $data['time_end']])
                    ->where('date_ref', $item['date_ref'])
                    // ->take(10)
                    ->where(function ($item) use ($data) {

                        if (isset($data['manager'])) {
                            $manager = $data['manager'];
                            $item->whereRaw(DB::Raw("'$manager' in (manager_n1_id, manager_n2_id, manager_n3_id, manager_n4_id, manager_n5_id)"));
                        }
                        if (isset($data['employee'])) {
                            $item->where('username', $data['employee']);
                        }
                        if (isset($data['sector'])) {
                            $item->where('sector_n1_id', $data['sector']);
                        }
                        if (isset($data['environment'])) {
                            $item->where('environment_id', $data['environment']);
                        }
                        if (isset($data['mailing'])) {
                            $item->where('mailing_id', $data['mailing']);
                        }
                        if (isset($data['queue'])) {
                            $item->where('queue_id', $data['queue']);
                        }
                        if (isset($data['status'])) {
                            $item->where('status_id', $data['status']);
                        }
                    })
                    ->where($group, $item->title)
                    ->orderBy(DB::raw("time_end - time_start"), 'desc')
                    ->select([
                        'id',
                        'date_ref',
                        'username',
                        'sector_n1_id',
                        'manager_n1_id',
                        'protocol',
                        'environment_id',
                        'mailing_id',
                        'queue_id',
                        'unique',
                        'time_start',
                        'time_end',
                        'is_simultaneous',
                        DB::raw("time_end - time_start AS time_duration"),
                        DB::raw("ROUND(EXTRACT(EPOCH FROM (time_start - '{$data['time_start']}'::TIME)) / nullif(EXTRACT(EPOCH FROM ('{$data['time_end']}'::TIME - '{$data['time_start']}'::TIME)) ,0) * 100, 2) AS obj_start"),
                        DB::raw("ROUND(EXTRACT(EPOCH FROM (time_end - time_start)) / nullif(EXTRACT(EPOCH FROM ('{$data['time_end']}'::TIME - '{$data['time_start']}'::TIME)) ,0) * 100, 2) AS obj_width"),
                        'status_id',
                    ])
                    ->get();

                return $item;
            });
        } catch (\Throwable $th) {
            Log::error('BulletinBackofficeService::getBackoffices', [$th->getMessage()]);
            throw new Exception('Error - BulletinBackofficeService::getBackoffices');
        }
        return  $backoffices;
    }
}
