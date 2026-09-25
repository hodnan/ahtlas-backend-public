<?php

namespace App\Console\Commands\Modules\Management;

use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ControlCenterTrackingReplyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdc:reply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replica os acompanhamentos ativos para próximo mês';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthRef = Carbon::now()->subMonth()->startOfMonth();
        $nextMonth = Carbon::now()->startOfMonth();
        $period = CarbonPeriod::create($nextMonth, $nextMonth->copy()->endOfMonth());
        $trackings = ControlCenterTrackingModel::where('active', 1)
            ->where('month_ref', $monthRef->format('Y-m-d'))
            ->get()->toArray();

        $daily_goals = [];

        foreach ($period as $date) {
            $temp = [
                'date_ref' =>  $date->format('Y-m-d'),
                'month_ref' =>  $nextMonth->format('Y-m-d'),
            ];
            $daily_goals[] = $temp;
        }

        foreach ($trackings as $tracking) {
                     
            try {
                $tracking['month_ref'] = $nextMonth;
                $goal = $tracking['goal'];

                $tracking['daily_goals'] = array_map(function ($item) use ($goal) {
                    $item['goal'] = $goal;
                    return $item;
                },  $daily_goals);

                $tracking['stage'] = 0;

                ControlCenterTrackingModel::create($tracking);
                Cache::forget('CDC_TRACKINGS_'. $monthRef)  ;
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
        return Command::SUCCESS;
    }
}
