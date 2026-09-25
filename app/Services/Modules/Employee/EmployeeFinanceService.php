<?php

namespace App\Services\Modules\Employee;

use App\Models\Addons\Indicator\FinanceIndicator;
use App\Models\Modules\Employee\EmployeeDaily;
use App\Models\Modules\Employee\EmployeeFinanceHeadCount;
use App\Services\Modules\Employee\EmployeeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;

class EmployeeFinanceService implements EmployeeInterface
{
    public static function getPositionLpu()
    {
        $data = EmployeeFinanceHeadCount::where('active', 1)
            ->orderBy('position_lpu')
            ->groupBy('position_lpu')
            ->get(['position_lpu']);

        $data->map(function ($item) {

            $indicator = FinanceIndicator::where('NO_DESCRICAO', $item->position_lpu)->first();

            $item->CD_INDICADOR = $indicator->ID;
            $item->NO_INDICADOR = $indicator->NO_INDICADOR;
        });

        return $data;
    }

    public static function getHcByPositionLpuAndDate($positionLpu, $date, $indicatorId, $indicatorName)
    {
        $positionLpu =  str_replace("'", "''", $positionLpu);
        $indicatorName =  str_replace("'", "''", $indicatorName);

        try {
            $hc = EmployeeDaily::leftJoin('employee_finance_head_counts', function ($join) {
                $join->on('employee_finance_head_counts.position', '=', 'employee_dailies.position')
                    ->on('employee_finance_head_counts.sector_n1_id', '=', 'employee_dailies.sector_n1_id')
                    ->on('employee_finance_head_counts.working_hours', '=', 'employee_dailies.working_hours');
            })
                ->where('employee_dailies.active', 1)
                ->whereIn('employee_dailies.status_op', ['Ativo No Piso'])
                ->whereNull('employee_dailies.dismissal')
                ->where('employee_dailies.date_ref', $date)
                ->where('employee_finance_head_counts.position_lpu', $positionLpu)
                ->groupBy(['employee_dailies.sector_n1_id', 'employee_finance_head_counts.position_lpu', 'employee_dailies.date_ref'])
                ->get([
                    DB::RAW("EXTRACT(YEAR FROM employee_dailies.date_ref) as \"NU_ANO\""),
                    DB::RAW("EXTRACT(MONTH FROM employee_dailies.date_ref) as \"NU_MES\""),
                    DB::RAW("EXTRACT(DAY FROM employee_dailies.date_ref) as \"NU_DIA\""),
                    'employee_dailies.sector_n1_id as CD_SETOR',
                    DB::RAW("'$indicatorId' as \"CD_INDICADOR\""),
                    DB::RAW("'$indicatorName' as \"NO_INDICADOR\""), // Escapa corretamente
                    DB::RAW("'759292' as \"NU_INSERIDO_POR\""),
                    DB::RAW("CURRENT_DATE - '1900-01-01' as \"CD_DT_REGISTRO\""),
                    DB::RAW('COUNT(*) as "NU_RESULTADO"')
                ]);
        } catch (\Throwable $th) {
            return false;
        }

        if ($hc) {
            foreach ($hc as $data) {
                if ($data) {

                    self::storeHCDay($data);
                }
            }
        }
    }

    public static function storeHCDay($data)
    {
        try {
            DB::connection('mis_primary')
                ->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_DIA_759292_FT')
                ->where('NU_ANO', $data->NU_ANO)
                ->where('NU_MES', $data->NU_MES)
                ->where('NU_DIA', $data->NU_DIA)
                ->where('CD_SETOR', $data->CD_SETOR)
                ->where('CD_INDICADOR', $data->CD_INDICADOR)
                ->delete();
        } catch (\Throwable $th) {
            Log::info('storeHCDay - delete ', $data);
        }

        try {
            DB::connection('mis_primary')
                ->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_DIA_759292_FT')
                ->insert([
                    'nu_ano' => $data->NU_ANO,
                    'nu_mes' => $data->NU_MES,
                    'nu_dia' => $data->NU_DIA,
                    'cd_setor' => $data->CD_SETOR,
                    'cd_indicador' => $data->CD_INDICADOR,
                    'no_indicador' => $data->NO_INDICADOR,
                    'nu_resultado' => $data->NU_RESULTADO,
                    'nu_inserido_por' => $data->NU_INSERIDO_POR,
                    'cd_dt_registro' => $data->CD_DT_REGISTRO,
                ]);
        } catch (\Throwable $th) {
            Log::info('storeHCDay - insert ', $data);
        }
    }

    public static function storeHCMonth(Carbon $data)
    {
        $year =  $data->copy()->format('Y');
        $month =  $data->copy()->format('m');

        $maxDay = DB::connection('mis_primary')->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_DIA_759292_FT')
            ->select(DB::RAW("max(NU_DIA) as max_day"))
            ->where('NU_ANO', $year)
            ->where('NU_MES', $month)
            ->first();

        try {
            DB::connection('mis_primary')
                ->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_MES_759292_FT')
                ->where('NU_ANO', $year)
                ->where('NU_MES', $month)
                ->delete();
        } catch (\Throwable $th) {
            Log::info('storeHCMonth - delete ', [$data]);
        }

        try {

            DB::connection('mis_primary')
                ->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_MES_759292_FT')
                ->insertUsing(
                    ['NU_ANO', 'NU_MES', 'CD_SETOR', 'CD_INDICADOR', 'NO_INDICADOR', 'NU_RESULTADO', 'NU_INSERIDO_POR', 'CD_DT_REGISTRO'],
                    DB::connection('mis_primary')->table('DB_KPI.dbo.TB_KPI_FINANCEIRO_DIA_759292_FT')
                        ->select('NU_ANO', 'NU_MES', 'CD_SETOR', 'CD_INDICADOR', 'NO_INDICADOR', 'NU_RESULTADO', 'NU_INSERIDO_POR', 'CD_DT_REGISTRO')
                        ->where('NU_ANO', $year)
                        ->where('NU_MES', $month)
                        ->where('NU_DIA',  $maxDay->max_day)
                );
        } catch (\Throwable $th) {
            Log::info('storeHCMonth - store ', [$data]);
        }
    }
}
