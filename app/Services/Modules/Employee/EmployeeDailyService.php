<?php

namespace App\Services\Modules\Employee;

use App\Models\Modules\Employee\EmployeeDaily;
use App\Services\Modules\Employee\EmployeeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;

class EmployeeDailyService implements EmployeeInterface
{
    public static function setEmployee()
    {
      
        $dataInicio = Carbon::now()->subDay(3)->startOfDay();
        $dataAtual = Carbon::now()->startOfDay();

        while ($dataInicio->lte($dataAtual)) {
            $isLastDay = $dataInicio->isSameDay($dataAtual);
            $dateSearch = $isLastDay ? $dataInicio->copy()->subDay()->format('Y-m-d') : $dataInicio->format('Y-m-d');

            $dateString = $dataInicio->copy()->format('Y-m-d');
            $monthString = $dataInicio->copy()->startOfMonth()->format('Y-m-d');

            $users = DB::connection('mis_primary')
                ->table('DB_RH.dbo.TB_QUADRO_FT_DIARIO as QFD')
                ->select([
                    DB::raw("'$monthString' as month_ref"),
                    DB::raw("'$dateString' as date_ref"),
                    DB::raw("'usr' + CAST(QFD.CD_MATRICULA AS VARCHAR) as username"),
                    'QDC.NO_NOME as name',
                    'QDC.DT_ADMISSAO as admission',
                    DB::raw("CASE WHEN QDC.DT_RESCISAO <> '1900-01-01' and QDC.DT_RESCISAO <= '$dateString' THEN QDC.DT_RESCISAO END as dismissal"),
                    'QDCA.NO_CARGO as position',
                    'QDCA.NO_CARGO_RESUMIDO as position_summary',
                    'QDS.NO_UF as uf',
                    'QDS.CD_SETOR as sector_n1_id',
                    'QDSS.CD_SUBSETOR as sector_n2_id',
                    'QDJ.HR_ENTRADA as start_time',
                    'QDJ.HR_JORNADA as working_hours',
                    DB::raw("'usr' + CAST(QFD.CD_MAT_SUPERVISOR AS VARCHAR) as manager_n1_id"),
                    DB::raw("'usr' + CAST(QFD.CD_MAT_COORDENADOR AS VARCHAR) as manager_n2_id"),
                    DB::raw("'usr' + CAST(QFD.CD_MAT_GERENTE_LOCAL AS VARCHAR) as manager_n3_id"),
                    DB::raw("'usr' + CAST(QFD.CD_MAT_GERENTE_RELAC AS VARCHAR) as manager_n4_id"),
                    DB::raw("'usr' + CAST(QFD.CD_MAT_DIRETOR AS VARCHAR) as manager_n5_id"),
                    DB::raw("CASE WHEN QDT.NU_TREINA_CURSO <> 0 THEN QDT.NU_TREINA_CURSO END as training_id"),
                    DB::raw("CASE WHEN QDT.DT_TREINA_INICIO <> '1900-01-01' THEN QDT.DT_TREINA_INICIO END as training_start"),
                    DB::raw("CASE WHEN QDT.DT_TREINA_ENTREGA <> '1900-01-01' THEN QDT.DT_TREINA_ENTREGA END as training_end"),       
                    DB::raw("CASE WHEN QDFA.DT_AFAST_INICIO <> '1900-01-01' THEN QDFA.DT_AFAST_INICIO END as away_start"),
                    DB::raw("CASE WHEN QDFA.DT_AFAST_RETORNO <> '1900-01-01' THEN QDFA.DT_AFAST_RETORNO END as away_end"),
                    'QDST.NO_STATUS_GIP as status',
                    'QDST.NO_STATUS_OPERACIONAL as status_op',
                ])
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as QDC', 'QFD.CD_MATRICULA', '=', 'QDC.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as SUP', 'QFD.CD_MAT_SUPERVISOR', '=', 'SUP.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as COO', 'QFD.CD_MAT_COORDENADOR', '=', 'COO.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as GN2', 'QFD.CD_MAT_GERENTE_LOCAL', '=', 'GN2.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as GN1', 'QFD.CD_MAT_GERENTE_RELAC', '=', 'GN1.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_COLABORADOR as DIR', 'QFD.CD_MAT_DIRETOR', '=', 'DIR.CD_MATRICULA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_CARGO as QDCA', 'QFD.SK_CARGO', '=', 'QDCA.SK_CARGO')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_SETOR as QDS', 'QFD.SK_SETOR', '=', 'QDS.SK_SETOR')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_SUBSETOR as QDSS', 'QFD.SK_SUBSETOR', '=', 'QDSS.SK_SUBSETOR')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_STATUS as QDST', 'QFD.SK_STATUS', '=', 'QDST.SK_STATUS')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_JORNADA as QDJ', 'QFD.SK_JORNADA', '=', 'QDJ.SK_JORNADA')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_TREINAMENTO as QDT', 'QFD.SK_TREINAMENTO', '=', 'QDT.SK_TREINAMENTO')
                ->leftJoin('DB_RH.dbo.TB_QUADRO_DM_FERIAS_AFAST as QDFA', 'QFD.SK_FERIAS_AFAST', '=', 'QDFA.SK_FERIAS_AFAST')
                ->where('QFD.CD_DATA', DB::raw("DATEDIFF(DAY, 0,  '$dateSearch')"))
                ->get()->toArray();

            // if ($users) {
            //     EmployeeDaily::where('date_ref', $dateString)->delete();
            // }


            LazyCollection::make($users)
                ->chunk(999)
                ->each(function ($chunk) {
                    // Preparar os dados para inserção
                    $updates = $chunk->map(function ($user) {
                        $user  = (array) $user;
                        $user['hierarchical_level'] = 0;
                        $user['hierarchical_level'] = $user['username'] == $user['manager_n1_id'] ? 1 : $user['hierarchical_level'];
                        $user['hierarchical_level'] = $user['username'] == $user['manager_n2_id'] ? 2 : $user['hierarchical_level'];
                        $user['hierarchical_level'] = $user['username'] == $user['manager_n3_id'] ? 3 : $user['hierarchical_level'];
                        $user['hierarchical_level'] = $user['username'] == $user['manager_n4_id'] ? 4 : $user['hierarchical_level'];
                        $user['hierarchical_level'] = $user['username'] == $user['manager_n5_id'] ? 5 : $user['hierarchical_level'];

                        $dateIsVeteran = $value['dt_resc'] ?? Carbon::now();

                        return [
                            'month_ref' => $user['month_ref'],
                            'date_ref' => $user['date_ref'],
                            'username' => $user['username'],
                            'name' => ucfirstException($user['name']),
                            'uf' => $user['uf'],
                            'position' => ucfirstException($user['position']),
                            'position_summary' => ucfirstException($user['position_summary']),
                            'sector_n1_id' => $user['sector_n1_id'],
                            'sector_n2_id' => $user['sector_n2_id'],
                            'manager_n1_id' => $user['manager_n1_id'],
                            'manager_n2_id' => $user['manager_n2_id'],
                            'manager_n3_id' => $user['manager_n3_id'],
                            'manager_n4_id' => $user['manager_n4_id'],
                            'manager_n5_id' => $user['manager_n5_id'],
                            'hierarchical_level' => $user['hierarchical_level'],
                            'type' => 'usr',
                            'staff' => !(preg_match('/Agente|Monitor|Jovem/i', $user['position_summary']) === 1),
                            'admission' => $user['admission'],
                            'dismissal' => $user['dismissal'],
                            'start_time' => $user['start_time'],
                            'working_hours' => $user['working_hours'],
                            'is_veteran' => Carbon::parse($user['admission'])->diffInDays($dateIsVeteran) > 90,
                            'training_id' => $user['training_id'],
                            'training_start' => $user['training_start'],
                            'training_end' => $user['training_end'],
                            'away_start' => $user['away_start'],
                            'away_end' => $user['away_end'],
                            'active' => $user['dismissal'] && str_contains($user['status'], 'RESC') ? 0 : 1,
                            'status' => ucfirstException($user['status']),
                            'status_op' => ucfirstException($user['status_op']),
                        ];
                    })
                    ->toArray();

                    // Usar transações para inserir/atualizar em lote
                    foreach ($updates as $user) {
                        try {

                            $key = [
                                'date_ref' => $user['date_ref'],
                                'username' => $user['username'],
                            ];

                            // EmployeeDaily::create($user);
                            EmployeeDaily::updateOrcreate( $key, $user);
                        } catch (\Throwable $e) {
                            Log::error($e->getMessage());
                        }
                    }
                });

            $dataInicio->addDay();
        }
    }
}
