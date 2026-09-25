<?php

namespace App\Console\Commands\Modules\TacticalCenter\Reports;

use App\Services\Modules\TacticalCenter\Bulletin\BulletinHourHourService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class BulletinHourHourCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:bulletin-hh';

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

        $dataInicio = Carbon::now()->subDay(5)->startOfDay();
        // $dataInicio = Carbon::now()->subMonth(1)->startOfMonth();
  
        $dataAtual = Carbon::now();

        // Faz busca na base dia a dia
        while ($dataInicio->lte($dataAtual)) {

            BulletinHourHourService::setBulletinDate($dataInicio);
            BulletinHourHourService::setBulletinDateHour($dataInicio);

            $dataInicio->addDay();
        }

      
        return Command::SUCCESS;
    }
}
