<?php

namespace App\Console\Commands\Modules\Employee;

use App\Models\Modules\Employee\SectorN1N2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSectorN1N2Commando extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:sectorn1n2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // try {
        $subQuery = DB::connection('mis_primary')
            ->table('DB_RH.dbo.VW_QUADRO_DIA_ATUAL')
            ->select([
                DB::raw('[CD_SETOR] as sector_n1_id'),
                DB::raw('[CD_SUBSETOR] as sector_n2_id'),
                DB::raw('CASE
                    WHEN [DT_RESCISAO] = \'1900-01-01\'
                    OR [NO_STATUS_DAP] NOT LIKE \'%RES%\' 
                    THEN 1 
                    ELSE 0 
                 END as active')
            ]);

        $dados = DB::connection('mis_primary')
            ->table(DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery)  // Merge bindings para passar os parâmetros da subquery
            ->select([
                'sector_n1_id',
                'sector_n2_id',
                DB::raw('CASE WHEN SUM(active) > 0 THEN 1 ELSE 0 END as active')
            ])
            ->groupBy('sector_n1_id', 'sector_n2_id')
            ->get()
            ->toArray();

        foreach ($dados as $value) {
            try {
                $value = (array) $value;
                $value['id'] = crc32($value['sector_n1_id'] . '-' . $value['sector_n2_id']);
                $value['name'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value['name'] ?? '');
                $obj = SectorN1N2::find($value['id']) ?? new SectorN1N2($value);
                $obj->save();
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
        
        return Command::SUCCESS;
    }
}
