<?php

namespace App\Console\Commands\Modules\TacticalCenter\Reports;

use App\Services\Modules\TacticalCenter\Bulletin\BulletinBackofficeService;
use App\Services\Modules\TacticalCenter\Bulletin\BulletinHourHourService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class BulletinBackofficeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:bulletin-backoffice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BulletinBackofficeService::setStatuses();
        BulletinBackofficeService::setEnvironment();
        BulletinBackofficeService::setMailing();
        BulletinBackofficeService::setQueue();

        $dataInicio = Carbon::now()->subDay(1)->startOfDay();
        
  
        $dataAtual = Carbon::now();

        // Faz busca na base dia a dia
        while ($dataInicio->lte($dataAtual)) {

            BulletinBackofficeService::setBulletin($dataInicio);
            BulletinBackofficeService::setSimultaneous($dataInicio);
          
            $dataInicio->addDay();
        }
      
        return Command::SUCCESS;
    }
}
