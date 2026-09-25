<?php

namespace App\Console\Commands\Modules\TacticalCenter\HeadCount;

use App\Models\Modules\TacticalCenter\HeadCount\HeadCount;
use App\Services\Modules\TacticalCenter\HeadCount\HeadCountService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class HeadCountCalcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:calc';

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
        $subMonth = Carbon::now()->subMonth()->startOfMonth();

        $headCounts = HeadCount::where('month_ref','>=', $subMonth )->get();

        foreach ($headCounts as $headCount) {
            HeadCountService::setRealData($headCount);
            if(env('APP_ENV') == 'production') {
                HeadCountService::copyMisPrimary($headCount);
            }
        }
      
        return Command::SUCCESS;
    }
}
