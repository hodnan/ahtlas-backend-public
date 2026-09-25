<?php

namespace App\Console\Commands\Modules\Management;


use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;

use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultSector;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Log;

class ControlCenterTrackingDailyResultSectorCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cdc:result-sector';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Calcula os resultados';

  /**
   * Execute the console command.
   */
  public function handle()
  {

    $monthPrev = Carbon::now()->subMonth(1)->startOfMonth();

    $trackings = ControlCenterTrackingModel::with(['indicator'])->where('month_ref', '>=', $monthPrev)
      ->get([
        'month_ref',
        'sector_n1_id',
        'indicator_id',
        'bypass',
        'daily_goals',
      ]);

    foreach ($trackings as $tracking) {
      $trackingSectors = ControlCenterService::getGroupSectorIds($tracking->sector_n1_id);
      ControlCenterService::setResultSector($tracking, $trackingSectors);
    }

    $monthPrev = Carbon::now()->subMonth(1)->startOfMonth();
    $monthActive = Carbon::now()->startOfMonth();

    ControlCenterTrackingDailyResultSector::where('month_ref', '>=', $monthPrev)->update(['stage' => null]);

    Cache::forget('CDC_TRACKINGS_' . $monthPrev->format('Y-m-d'));
    Cache::forget('CDC_TRACKINGS_' . $monthActive->format('Y-m-d'));

    return Command::SUCCESS;
  }
}
