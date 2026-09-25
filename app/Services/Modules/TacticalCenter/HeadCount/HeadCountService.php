<?php

namespace App\Services\Modules\TacticalCenter\HeadCount;

use App\Models\Addons\Calendar;
use App\Models\Addons\HeadCount as AddonsHeadCount;
use App\Models\Modules\Employee\EmployeeDaily;
use App\Models\Modules\TacticalCenter\HeadCount\HeadCount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeadCountService
{
    public static function getHeadCount($request)
    {
        try {

            $monthRef = $request->month_ref ?? Carbon::now()->startOfMonth();
            $calendars = Calendar::where('month_ref', $monthRef)
                ->orderBy('date', 'asc')
                ->get(['month_ref', 'date', 'day']);

            $data = [];

            foreach ($calendars as $calendar) {

                $temp = $calendar;

                $hc = HeadCount::where('date_ref', $calendar->date)
                    ->select([
                        DB::raw("sum(hc_dim) as hc_dim"),
                    ])
                    ->groupBy(['month_ref', 'date_ref'])
                    ->get()
                    ->first();


                $temp['indicator'] = ['label' => 'Dim - ', 'value' => $hc->hc_dim];
                $data[] =  $temp;
            }


            return $data;
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getRealData($dateRef, array $sectorN1Id)
    {
        $monthStart = Carbon::parse($dateRef)->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::parse($dateRef)->endOfMonth()->format('Y-m-d');
        $nextMonthStart = Carbon::parse($dateRef)->addMonth()->startOfMonth()->format('Y-m-d');
        $nextMonthEnd = Carbon::parse($dateRef)->addMonth()->endOfMonth()->format('Y-m-d');


        $result = EmployeeDaily::select([
            'date_ref',
            'month_ref',
            DB::raw("SUM(CASE WHEN status IN ('Atividade Normal', 'Afast. Doenca Menor 15 Dias') and status_op in ('Ativo No Piso') THEN 1 ELSE 0 END) AS hc_real"),
            DB::raw("SUM(CASE WHEN status LIKE '%Ferias%' THEN 1 ELSE 0 END) AS vacation_real"),
            DB::raw("SUM(CASE WHEN status_op IN ('Ativo No Tr Migração', 'Ativo No Tr Inicial', 'Ativo No Tr Reciclagem', 'Ativo No Tr Retorno Afast') THEN 1 ELSE 0 END) AS training_real"),
            DB::raw("SUM(CASE WHEN status_op = 'Ativo No Tr Inicial' THEN 1 ELSE 0 END) AS training_initial_real"),
            DB::raw("SUM(CASE WHEN status_op = 'Ativo No Tr Migração' THEN 1 ELSE 0 END) AS training_migration_real"),
            DB::raw("SUM(CASE WHEN status_op = 'Ativo No Tr Reciclagem' THEN 1 ELSE 0 END) AS training_recycling_real"),
            DB::raw("SUM(CASE WHEN status_op in ('Ativo No Tr Retorno Afast', 'Ativo No Tr Inicial (Retorno Afast.)') THEN 1 ELSE 0 END) AS training_return_leave_real"),
            DB::raw("SUM(CASE WHEN status_op LIKE '%Afastado %' THEN 1 ELSE 0 END) AS away_real"),
            DB::raw("SUM(CASE WHEN dismissal >= month_ref and status_op like '%Desligado%' THEN 1 ELSE 0 END) AS to_total_real"),
            DB::raw("SUM(CASE WHEN dismissal >= month_ref and status in ('Resc Demissao', 'Resc Demissao Com Justa Causa', 'Resc Demissao Sem Justa Causa') THEN 1 ELSE 0 END) AS to_active_real"),
            DB::raw("SUM(CASE WHEN dismissal >= month_ref and status_op LIKE '%Desligado No Piso%' THEN 1 ELSE 0 END) AS to_operation_real"),
            DB::raw("SUM(CASE WHEN dismissal >= month_ref and status_op LIKE '%Desligado No Tr%' THEN 1 ELSE 0 END) AS to_training_real"),
            DB::raw("SUM(CASE WHEN status IN ('Atividade Normal', 'Afast. Doenca Menor 15 Dias') and status_op in ('Ativo No Piso') THEN 1 ELSE 0 END) + SUM(CASE WHEN status LIKE '%Ferias%' THEN 1 ELSE 0 END) + SUM(CASE WHEN status_op IN ('Ativo No Tr Migração', 'Ativo No Tr Inicial', 'Ativo No Tr Reciclagem', 'Ativo No Tr Retorno Afast') THEN 1 ELSE 0 END) AS total_real"),
            DB::raw("SUM(CASE WHEN date_ref >= training_end and status_op like 'Ativo No Tr %' and training_end between '$monthStart' and '$monthEnd' then 1 else 0 end) AS training_delivery_this_month"),
            DB::raw("SUM(CASE WHEN status_op like 'Ativo No Tr %' and training_end between '$nextMonthStart' and '$nextMonthEnd' then 1 else 0 end) AS training_delivery_next_month")
        ])
            ->where('date_ref', $dateRef)
            ->whereIN('sector_n1_id', $sectorN1Id)
            ->where(function ($query) {
                $query->where('position', 'Jovem Aprendiz Op')
                    ->orWhere('position_summary', 'Agente');
            })
            ->groupBy(['date_ref', 'month_ref'])
            ->first();

        return $result;
    }

    public static function setRealData($headCount)
    {
        try {
            $data = self::getRealData($headCount->date_ref, [$headCount->sector_n1_id]);

            if ($data) {
                $headCount->hc_real = $data->hc_real;
                $headCount->vacation_real = $data->vacation_real;
                $headCount->training_real = $data->training_real;
                $headCount->training_initial_real = $data->training_initial_real;
                $headCount->training_migration_real = $data->training_migration_real;
                $headCount->training_recycling_real = $data->training_recycling_real;
                $headCount->training_return_leave_real = $data->training_return_leave_real;
                $headCount->away_real = $data->away_real;
              
                $headCount->to_total_real = $data->to_total_real;
                $headCount->to_active_real = $data->to_active_real;
                $headCount->to_operation_real = $data->to_operation_real;
                $headCount->to_training_real = $data->to_training_real;

                $headCount->total_active_real = $data->hc_real + $data->vacation_real;
                $headCount->total_real = $data->total_real;

                $headCount->hc_dif = $data->hc_real - $headCount->hc_dim;
                $headCount->total_dif = $headCount->total_real - $headCount->total_dim;
                $headCount->training_dif = $headCount->training_real - $headCount->training_dim;
                $headCount->training_delivery_this_month = $data->training_delivery_this_month;
                $headCount->training_delivery_next_month = $data->training_delivery_next_month;

                $headCount->save();
            }
        } catch (\Throwable $th) {
            return  [$data, $headCount, $th->getMessage()];
        }
    }

    public static function copyMisPrimary($headCount)
    {
        $baseDate = Carbon::parse('1900-01-01');

        $data['CD_DATA'] = $baseDate->diffInDays($headCount->date_ref);
        $data['CD_SETOR'] = $headCount->sector_n1_id;
        $data['CD_MAT_DIRETOR'] = $headCount->manager_n5_id;
        $data['CD_MAT_GERENTE_RELAC'] = $headCount->manager_n4_id;
        $data['CD_MAT_GERENTE_LOCAL'] = $headCount->manager_n3_id;
        $data['NU_HC_DIM'] = $headCount->hc_dim;
        $data['NU_FERIAS_DIM'] = $headCount->vacation_dim;
        $data['NU_TREINAMENTO_DIM'] = $headCount->training_dim;
        $data['NU_TREINAMENTO_INICIAL_DIM'] = $headCount->training_initial_dim;
        $data['NU_TREINAMENTO_MIGRACAO_DIM'] = $headCount->training_migration_dim;
        $data['NU_TOTAL_DIM'] = $headCount->total_dim;
        $data['NU_HC_REAL'] = $headCount->hc_real;
        $data['NU_FERIAS_REAL'] = $headCount->vacation_real;
        $data['NU_TREINAMENTO_REAL'] = $headCount->training_real;
        $data['NU_TREINAMENTO_INICIAL_REAL'] = $headCount->training_initial_real;
        $data['NU_TREINAMENTO_MIGRACAO_REAL'] = $headCount->training_migration_real;
        $data['NU_TREINAMENTO_RECICLAGEM_REAL'] = $headCount->training_recycling_real;
        $data['NU_TREINAMENTO_RETORNO_REAL'] = $headCount->training_return_leave_real;
        $data['NU_AFASTADO_REAL'] = $headCount->away_real;

        $data['NU_TO_TOTAL_REAL'] = $headCount->to_total_real;
        $data['NU_TO_ATIVO_REAL'] = $headCount->to_active_real;
        $data['NU_TO_OPERACAO_REAL'] = $headCount->to_operation_real;
        $data['NU_TO_TREINAMENTO_REAL'] = $headCount->to_training_real;
     
        $data['NU_TOTAL_REAL'] = $headCount->total_real;
        $data['NU_TOTAL_ATIVO_REAL'] = $headCount->total_active_real;
        $data['NU_HD_CLIENT_DIM'] = $headCount->hd_client_dim;
        $data['NU_HC_DIF'] = $headCount->hc_dif;
        $data['NU_TOTAL_DIF'] =  $headCount->total_dif;
        $data['NU_FERIAS_DIF'] = $headCount->training_dif;
        $data['NU_TREINAMENTO_DIF'] = $headCount->vacation_dif;
        $data['NU_TREINAMENTO_PREV_NESTE_MES'] = $headCount->training_delivery_this_month;
        $data['NU_TREINAMENTO_PREV_PROXIMO_MES'] = $headCount->training_delivery_next_month;
        $data['NU_VERSAO'] = $headCount->version;
                    
        $key = [
            'CD_DATA' => $data['CD_DATA'],
            'CD_SETOR' => $data['CD_SETOR'],
        ];

        AddonsHeadCount::updateOrCreate($key, $data);
    }
}
