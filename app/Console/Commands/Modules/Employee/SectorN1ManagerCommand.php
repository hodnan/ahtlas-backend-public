<?php

namespace App\Console\Commands\Modules\Employee;

use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Employee\SectorN1Manager;
use App\Services\Modules\Employee\EmployeeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SectorN1ManagerCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:sectorn1-manager';

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
        $dados = EmployeeService::getManagerBySector();

        SectorN1Manager::truncate();
    
        foreach ($dados as $value) {
    
          try {
            if($value){

              $key = [
                'manager_username' => $value['manager_username'],
                'sector_n1_id' => $value['sector_n1_id'],
            ];

              SectorN1Manager::updateOrcreate($key , $value);
            }
          } catch (\Throwable $th) {
            Log::error('SectorN1 Manager', [$th->getMessage()]);
          }
        }

        return Command::SUCCESS;
    }
}
