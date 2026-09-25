<?php

namespace App\Console\Commands\Modules\Employee;

use App\Models\Modules\Employee\SectorN1;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSectorN1Command extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:sectorn1';

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
              "CD_SETOR as id",
              "NO_SETOR as name",
              'NO_SITE_UF as uf',
            ])
            ->groupBy(['CD_SETOR', 'NO_SETOR', 'NO_SITE_UF'])
            ->get()->toArray();

            foreach ($dados as $value) {

               try {
                $value = (array)  $value;
                $obj = SectorN1::find($value['id']) ?? new SectorN1(  $value);
                $value['name'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value['name'] ?? '');
                $obj['name'] = ucfirstException($value['name']);
                $obj->save();
               } catch (\Throwable $th) {
                Log::error($th->getMessage());
               }
                
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return Command::SUCCESS;
    }
}
