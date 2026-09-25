<?php

namespace App\Console\Commands\Addons;

use App\Models\Addons\KpiResult;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class KpiResultCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:result-guilda';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Salva os resultados dos kpis em addons ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataInicio = Carbon::now()->subMonth(1)->startOfMonth();
        $dataAtual = Carbon::now();

        KpiResult::truncate();

        // // para reprocessamento 
        // $dataInicio = Carbon::parse('2024-12-01');
        // $dataAtual = '2024-12-31';

        // Faz busca na base dia a dia
        while ($dataInicio->lte($dataAtual)) {

            $dataString = $dataInicio->toDateString();

            $data = DB::connection('mis_primary')
                ->table('DB_CORPORATIVO.dbo.TB_CORP_KPI_RESULT_DIA_FT')
                ->where('CD_DT_REF', DB::raw("DATEDIFF(day, 0, '$dataString')"))
                ->where('CD_MATRICULA', '>', 599999)                
                ->select([
                    DB::raw("DATEADD(day, 0, CD_DT_REF) as date_ref"),
                    DB::raw("'usr' + cast(CD_MATRICULA as varchar) as username"),
                    DB::raw('CD_SETOR as sector_n1_id'),
                    // DB::raw('CASE WHEN CD_INDICADOR = -1 then 10000013 else CD_INDICADOR end as indicator_id'),
                    DB::raw('CD_INDICADOR as indicator_id'),
                    DB::raw('CD_RESULTADO as "result"'),
                    DB::raw('CD_FATOR_0 as factor_0'),
                    DB::raw('CD_FATOR_1 as factor_1'),
                    DB::raw('CD_FATOR_2 as factor_2'),
                    DB::raw('GETDATE() as created_at'),
                    DB::raw('GETDATE() as updated_at')
                ])
                // ->where('CD_SETOR', '2052')
                // ->where('CD_INDICADOR', '2')
                ->get()
                ->toArray();

            // Insere dados fracionados no banco 
            LazyCollection::make($data)
                ->chunk(999)
                ->each(function ($chunk) {
                    try {
                        $data = $chunk->map(function ($user) {
                            return (array) $user;
                        })->toArray();
                        KpiResult::insert($data);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                });
            $dataInicio->addDay();
        }

        return Command::SUCCESS;
    }
}
