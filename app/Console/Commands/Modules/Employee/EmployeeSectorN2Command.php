<?php

namespace App\Console\Commands\Modules\Employee;


use App\Models\Modules\Employee\SectorN2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSectorN2Command extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:sectorn2';

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
       
        try {
            $dados = DB::connection('mis_primary')
                ->table('DB_RH.dbo.VW_QUADRO_DIA_ATUAL')
                ->select([
                    "CD_SUBSETOR as id",
                    "NO_SUBSETOR as name",
                ])
                ->groupBy(['CD_SUBSETOR', 'NO_SUBSETOR'])
                ->get()->toArray();

            foreach ($dados as $value) {
                try {
                    $value = (array) $value;
                    $value['name'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value['name'] ?? '');              
                    $obj = SectorN2::find($value['id']) ?? new SectorN2($value);
                    $obj['name'] = ucfirstException($obj['name']);     
                    $obj->save();
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                }
            }
        } catch (\Throwable $th) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
