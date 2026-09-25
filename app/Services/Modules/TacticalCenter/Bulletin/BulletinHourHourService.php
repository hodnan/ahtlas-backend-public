<?php

namespace App\Services\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\Employee\SectorN1Manager;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\TacticalCenter\Bulletin\HourHourDate;
use App\Models\Modules\TacticalCenter\Bulletin\HourHourDateHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class BulletinHourHourService
{
    public static function getSectorsN1($request  = null)
    {

        $date = $request['date_ref'] ?? Carbon::now()->format('Y-m-d');

        $sectors = HourHourDate::where('dt_data', $date)
            ->pluck('nu_setor')
            ->toArray();

        $sectorN1s = SectorN1::whereIn('id', $sectors)
            ->orderBy('id', 'asc')
            ->get(['id', 'name']);

        return $sectorN1s;
    }

    public static function getBulletins($request = null)
    {
        $date = $request['date_ref'] ?? Carbon::now()->startOfDay()->format('Y-m-d');

        $data = HourHourDate::with(['sectorN1', 'managers'])
            ->where('dt_data', $date)
          
            ->orderBy('nu_setor', 'asc')
            ->whereHas('sectorN1', function ($query) {
                $query->whereNotNull('id');
            })
            ->get()
            ->makeHidden([
                'nu_prev_recebidas',
                'nu_prev_atendidas',
                'nu_prev_ns_ponderado',
                'nu_prev_tp_falado',
                'nu_prev_tp_logado',
                'nu_prev_pausas',
                'nu_prev_carga_h',
                'nu_prev_tp_corte',
                'nu_prev_dn',
                'nu_real_recebidas',
                'nu_real_atendidas',
                'nu_real_abandonadas',
                'nu_real_tp_falado',
                'nu_real_tp_espera',
                'nu_real_tp_logado',
                'nu_real_pausas',
                'nu_real_1_ch_ns',
                'nu_real_2_ch_ns',
                'nu_real_tp_falado_r',
                'nu_real_pausa_tp_lanche',
                'nu_real_pausa_tp_refeicao',
                'nu_real_pausa_tp_descanso',
                'nu_real_pausa_tp_banheiro',
                'nu_real_pausa_tp_feedback',
                'nu_real_pausa_tp_feedback_sup',
                'nu_real_pausa_tp_reuniao',
                'nu_real_pausa_tp_input_vendas',
                'nu_real_pausa_tp_treinamento',
                'nu_real_pausa_tp_callback',
                'nu_real_pausa_tp_comunicacao',
                'nu_real_pausa_tp_backoffice',
                'nu_real_pausa_tp_defeito',
                'nu_real_pausa_tp_logof',
                'nu_real_pausa_tp_exame_periodico',
                'nu_prev_recebidas_fechado',
                'nu_prev_ns_ponderado_fechado',
            ]);


        return $data;
    }

    public static function getBulletin($dt_data, $nu_setor)
    {
        $data = HourHourDate::with([])
            ->where('dt_data', $dt_data)
            ->where('nu_setor', $nu_setor)
            ->orderBy('nu_setor', 'asc')
            ->first()
            ->makeHidden([
                'nu_prev_recebidas',
                'nu_prev_atendidas',
                'nu_prev_ns_ponderado',
                'nu_prev_tp_falado',
                'nu_prev_tp_logado',
                'nu_prev_pausas',
                'nu_prev_carga_h',
                'nu_prev_tp_corte',
                'nu_prev_dn',
                'nu_real_recebidas',
                'nu_real_atendidas',
                'nu_real_abandonadas',
                'nu_real_tp_falado',
                'nu_real_tp_espera',
                'nu_real_tp_logado',
                'nu_real_pausas',
                'nu_real_1_ch_ns',
                'nu_real_2_ch_ns',
                'nu_real_tp_falado_r',
                'nu_real_pausa_tp_lanche',
                'nu_real_pausa_tp_refeicao',
                'nu_real_pausa_tp_descanso',
                'nu_real_pausa_tp_banheiro',
                'nu_real_pausa_tp_feedback',
                'nu_real_pausa_tp_feedback_sup',
                'nu_real_pausa_tp_reuniao',
                'nu_real_pausa_tp_input_vendas',
                'nu_real_pausa_tp_treinamento',
                'nu_real_pausa_tp_callback',
                'nu_real_pausa_tp_comunicacao',
                'nu_real_pausa_tp_backoffice',
                'nu_real_pausa_tp_defeito',
                'nu_real_pausa_tp_logof',
                'nu_real_pausa_tp_exame_periodico',
                'nu_prev_recebidas_fechado',
                'nu_prev_ns_ponderado_fechado',
            ]);

        $day = [
            [
                'dt_data' => $data->dt_data,
                'no_intervalo' => 'Total',
                'no_sigla' => $data->no_sigla,
                'nu_setor' => $data->nu_setor,
                'service_level' => $data->service_level,
                'tma' => $data->tma,
                'traffic' => $data->traffic,
                'volume' => $data->volume,
                'login' => $data->login,
                'breaks' => $data->breaks,
            ]
        ];

        $data->intraday = array_merge($day, $data->getIntraday());

        $data->chart = $data->getChartAttribute();

        return $data;
    }

    public static function getManagers($request  = null)
    {
        $date = $request['date'] ?? Carbon::now()->startOfDay()->format('Y-m-d');

        $sectors = HourHourDate::where(1, 1)
            ->where('dt_data', $date)
            ->distinct()
            ->pluck('nu_setor')
            ->toArray();

        $data = SectorN1Manager::with('manager_username')
            ->whereHas('manager_username', function ($query) {
                $query->whereNotNull('name');
                $query->where('hierarchical_level', '>', 1);
            })
            ->whereIn('sector_n1_id', $sectors)
            ->distinct()
            ->get('manager_username')->toArray();

        $data = collect($data)->sortBy([['manager_username.hierarchical_level.id', 'desc'], ['manager_username.name', 'asc']])->values();

        return $data;
    }

    public static function setBulletinDate($date)
    {
        $date = $date->toDateString();

        $data = DB::connection('mis_primary')
            ->table('DB_TELEFONIA.dbo.TB_BOLETIM_BASE_DIA')
            ->where('DT_DATA', $date)
            ->select([
                "DT_DATA as dt_data",
                DB::raw("LEFT([TP_INTERVALO],8) AS no_intervalo"),
                "no_sigla",
                "CD_SETOR AS nu_setor",
                "nu_prev_recebidas",
                "nu_prev_atendidas",
                "nu_prev_ns_ponderado",
                "nu_prev_tp_falado",
                "nu_prev_tp_logado",
                "nu_prev_pausas",
                "nu_prev_carga_h",
                "nu_prev_tp_corte",
                "nu_prev_dn",
                "nu_real_recebidas",
                "nu_real_atendidas",
                "nu_real_abandonadas",
                "nu_real_tp_falado",
                "nu_real_tp_espera",
                "nu_real_tp_logado",
                "nu_real_pausas",
                "nu_real_1_ch_ns",
                "nu_real_2_ch_ns",
                "nu_real_tp_falado_r",
                "nu_real_pausa_tp_lanche",
                "nu_real_pausa_tp_refeicao",
                "nu_real_pausa_tp_descanso",
                "nu_real_pausa_tp_banheiro",
                "nu_real_pausa_tp_feedback",
                "nu_real_pausa_tp_feedback_sup",
                "nu_real_pausa_tp_reuniao",
                "nu_real_pausa_tp_input_vendas",
                "nu_real_pausa_tp_treinamento",
                "nu_real_pausa_tp_callback",
                "nu_real_pausa_tp_comunicacao",
                "nu_real_pausa_tp_backoffice",
                "nu_real_pausa_tp_defeito",
                "nu_real_pausa_tp_logof",
                "nu_real_pausa_tp_exame_periodico",
                "nu_prev_recebidas_fechado",
                "nu_prev_ns_ponderado_fechado",
            ])
            ->get()->toArray();

        if ($data) {
            // deleta dados no periodo
            HourHourDate::where('dt_data', '=', $date)->delete();
        }
        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {
                try {
                    $data = $chunk->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    HourHourDate::insert($data);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            });
    }

    public static function setBulletinDateHour($date)
    {

        $date = $date->toDateString();

        $data = DB::connection('mis_primary')
            ->table('DB_TELEFONIA.dbo.TB_BOLETIM_BASE_DIA_HORA')
            ->where('DT_DATA', $date)
            ->select([
                DB::raw("CAST(DT_DATA AS varchar(10)) + ' ' +  LEFT([TP_INTERVALO],8) AS dt_data_hora"),
                "DT_DATA as dt_data",
                DB::raw("LEFT([TP_INTERVALO],8) AS no_intervalo"),
                "no_sigla",
                "CD_SETOR AS nu_setor",
                "nu_prev_recebidas",
                "nu_prev_atendidas",
                "nu_prev_ns_ponderado",
                "nu_prev_tp_falado",
                "nu_prev_tp_logado",
                "nu_prev_pausas",
                "nu_prev_tp_corte",
                "nu_prev_dn",
                "nu_real_recebidas",
                "nu_real_atendidas",
                "nu_real_abandonadas",
                "nu_real_tp_falado",
                "nu_real_tp_espera",
                "nu_real_tp_logado",
                "nu_real_pausas",
                "nu_real_1_ch_ns",
                "nu_real_2_ch_ns",
                "nu_real_tp_falado_r",
                "nu_real_pausa_tp_lanche",
                "nu_real_pausa_tp_refeicao",
                "nu_real_pausa_tp_descanso",
                "nu_real_pausa_tp_banheiro",
                "nu_real_pausa_tp_feedback",
                "nu_real_pausa_tp_feedback_sup",
                "nu_real_pausa_tp_reuniao",
                "nu_real_pausa_tp_input_vendas",
                "nu_real_pausa_tp_treinamento",
                "nu_real_pausa_tp_callback",
                "nu_real_pausa_tp_comunicacao",
                "nu_real_pausa_tp_backoffice",
                "nu_real_pausa_tp_defeito",
                "nu_real_pausa_tp_logof",
                "nu_real_pausa_tp_exame_periodico"
            ])
            ->get()->toArray();

        if ($data) {
            // deleta dados no periodo
            HourHourDateHour::where('dt_data', '=', $date)->delete();
        }
        // Insere dados fracionados no banco 
        LazyCollection::make($data)
            ->chunk(999)
            ->each(function ($chunk) {
                try {
                    $data = $chunk->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    HourHourDateHour::insert($data);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            });
    }
}
