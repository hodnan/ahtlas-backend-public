<?php

namespace App\Console\Commands\Modules\Management;

use App\Models\Modules\Management\MyTeam\AiEmployeePower;
use App\Services\Modules\Management\Myteam\AiPowerService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class AiEmployeePowerCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ai:emmployee';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Atualiza a lista de resultados do potênc-ia';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $months = [
      Carbon::now()->startOfMonth()->format('Y-m-d'),
      Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d'),
    ];

    foreach ($months as $value) {
      $m0 = Carbon::parse($value)->startOfMonth();
      $m1 = $m0->copy()->subMonth();
      $m2 = $m0->copy()->subMonths(2);

      $m0Formatted = $m0->format('Ym');
      $m1Formatted = $m1->format('Ym');
      $m2Formatted = $m2->format('Ym');

      AiEmployeePower::where('month_ref', $m0)->delete();

      $results = DB::connection('mis_primary')
        ->select("
              WITH operador_grupo_real_m1 AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_OPERADOR_MENSAL_ACAO_REAL
                  WHERE
                      ANOMES = $m1Formatted 
              ),
              operador_grupo_real_m2 AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_OPERADOR_MENSAL_ACAO_REAL
                  WHERE
                      ANOMES = $m2Formatted 
              ),
              operador_grupo_previsto AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_OPERADOR_MENSAL_ACAO_PREVISAO
                  WHERE
                      ANOMES = $m0Formatted 
              ),
              supervisor_grupo_real_m1 AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_SUPERVISOR_MENSAL_ACAO_REAL
                  WHERE
                      ANOMES = $m1Formatted 
              ),
              supervisor_grupo_real_m2 AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_SUPERVISOR_MENSAL_ACAO_REAL
                  WHERE
                      ANOMES = $m2Formatted 
              ),
              supervisor_grupo_previsto AS (
                  SELECT
                      CD_MATRICULA,
                      CAST(SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 1, 4) + '-' + SUBSTRING(CAST(ANOMES AS VARCHAR(6)), 5, 2) + '-01' AS DATE) AS month_ref,
                      NU_GRUPO_MES
                  FROM
                      DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_SUPERVISOR_MENSAL_ACAO_PREVISAO
                  WHERE
                      ANOMES = $m0Formatted 
              ),
              complilado AS (
                  SELECT
                      gp.month_ref,
                      'usr' + CAST(gp.CD_MATRICULA AS VARCHAR) AS username,
                      0 AS hierarchical_level,
                      gp.NU_GRUPO_MES AS m0,
                      grm1.NU_GRUPO_MES AS m1,
                      grm2.NU_GRUPO_MES AS m2,
                      CONCAT(gp.NU_GRUPO_MES, grm1.NU_GRUPO_MES, grm2.NU_GRUPO_MES) AS action_id,
                      GETDATE() AS created_at,
                      GETDATE() AS updated_at
                  FROM
                      operador_grupo_previsto AS gp
                  LEFT JOIN
                      operador_grupo_real_m1 AS grm1 ON grm1.CD_MATRICULA = gp.CD_MATRICULA
                  LEFT JOIN
                      operador_grupo_real_m2 AS grm2 ON grm2.CD_MATRICULA = gp.CD_MATRICULA
                  UNION
                  SELECT
                      gp.month_ref,
                      'usr' + CAST(gp.CD_MATRICULA AS VARCHAR) AS username,
                      1 AS hierarchical_level,
                      gp.NU_GRUPO_MES AS m0,
                      grm1.NU_GRUPO_MES AS m1,
                      grm2.NU_GRUPO_MES AS m2,
                      CONCAT(gp.NU_GRUPO_MES, grm1.NU_GRUPO_MES, grm2.NU_GRUPO_MES) AS action_id,
                      GETDATE() AS created_at,
                      GETDATE() AS updated_at
                  FROM
                      supervisor_grupo_previsto AS gp
                  LEFT JOIN
                      supervisor_grupo_real_m1 AS grm1 ON grm1.CD_MATRICULA = gp.CD_MATRICULA
                  LEFT JOIN
                      supervisor_grupo_real_m2 AS grm2 ON grm2.CD_MATRICULA = gp.CD_MATRICULA
              )
              SELECT * FROM complilado
              WHERE m0 IS NOT NULL AND m1 IS NOT NULL AND m2 IS NOT NULL
      ");

      $resultsArray = collect($results)->map(function ($item) {
        return (array) $item;
      })->all();

      LazyCollection::make($resultsArray)
        ->chunk(999)
        ->each(function ($chunk) {
          try {
            AiEmployeePower::insert($chunk->toArray());
          } catch (\Throwable $e) {
            Log::error($e->getMessage());
          }
        });
    }

    AiPowerService::setManagers();
    return Command::SUCCESS;
  }
}
