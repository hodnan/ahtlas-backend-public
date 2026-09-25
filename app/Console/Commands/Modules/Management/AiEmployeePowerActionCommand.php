<?php

namespace App\Console\Commands\Modules\Management;

use App\Models\Addons\AiEmployeePowerAction;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Modules\Management\MyTeam\AiEmployeePowerAction as MyTeamAiEmployeePowerAction;
use Illuminate\Support\Facades\Log;


class AiEmployeePowerActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:emmployee-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza a lista de ações do potênc-ia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = AiEmployeePowerAction::all();

        foreach ($data as $value) {
    
          $item = [
            'id' => $value['NU_Q_MES_0'] . $value['NU_Q_MES_1'] . $value['NU_Q_MES_2'],
            'action' =>  Str::lower($value['NO_ACAO']) ,
          ];
    
          try {
            MyTeamAiEmployeePowerAction::updateOrInsert(['id' => $item['id']], $item);
          
          } catch (\Throwable $e) {
            Log::error($e->getMessage());
          }
          
        }
        return Command::SUCCESS;
    }
}
