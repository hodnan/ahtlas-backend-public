<?php

namespace App\Console\Commands\Core;

use App\Models\Addons\Gip;
use App\Models\Core\SectorN1;
use App\Models\Modules\Employee\SectorN2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SectorN1Command extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectorn1:update';

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
                $obj['name'] = str_replace('–', '-', ucfirstException($value['name']));
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
