<?php

namespace App\Console\Commands\Modules\Management;


use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultSector;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ControlCenterTrackingStageCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cdc:stage';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Calcula o stage considerando os resultados';

  /**
   * Execute the console command.
   */
  public function handle()
  {

    ControlCenterTrackingStage::where('created_at', '<', Carbon::now()->startOfDay())->where('status', ControlCenterInterface::STAGE_STATUS_STANDBY)
      ->update(
        [
          'status' => ControlCenterInterface::STAGE_STATUS_CANCELED,
          'notes' => 'Cancelado automático por não validação',
        ]
      );

    $activeDay = Carbon::now()->format('d');
    $monthActive = Carbon::now()->startOfMonth();
    $monthPrev = $activeDay > 5 ? $monthActive : Carbon::now()->subMonth(1)->startOfMonth();

    ControlCenterTrackingDailyResultSector::where('month_ref', '>=', $monthPrev)->update(['stage' => null]);

    $trackings = ControlCenterTrackingModel::with(['indicator'])
      ->where('month_ref', '>=', '2024-11-01')
      ->where('month_ref', '>=', $monthPrev)
      ->where('active', 1)
      ->get([
        'id',
        'month_ref',
        'sector_n1_id',
        'indicator_id',
        'q1',
        'q2',
        'q3',
        'q4',
      ]);

    foreach ($trackings as $tracking) {
      try {
        // Atualiza os responsáveis 
        $tracking->owners = ControlCenterService::getOwners($tracking->indicator_id, $tracking->sector_n1_id);

        // Pega o ultimo resultado do kpi
        $result = ControlCenterService::getTrackingLastResult($tracking);

        // Salva dados de novos stages
        ControlCenterService::setTrackingStage($tracking, $result);

      } catch (\Throwable $th) {
        Log::error('CCP Stage', [$th->getMessage()]);
      }
    }

    $stages = ControlCenterTrackingStage::where('status', ControlCenterInterface::STAGE_STATUS_VALIDATED )
    ->where('month_ref', '>=', $monthPrev)
    ->get(['date_ref', 'sector_n1_id', 'indicator_id', 'stage']);

    foreach ($stages as $stage) {

      $result = ControlCenterTrackingDailyResultSector::where('date_ref', $stage->date_ref)
        ->where('sector_n1_id',  $stage->sector_n1_id)
        ->where('indicator_id',  $stage->indicator_id)
        ->first();

      if ($result) {
        $result->stage = $stage->stage['id'];
        $result->save();
      }
    }

    Cache::forget('CDC_TRACKINGS_' . $monthPrev->format('Y-m-d'));
    Cache::forget('CDC_TRACKINGS_' . $monthActive->format('Y-m-d'));
    Cache::forget('CDC_STAGES_' . $monthPrev->format('Y-m-d'));
    Cache::forget('CDC_STAGES_' . $monthActive->format('Y-m-d'));
    return Command::SUCCESS;
  }
}
