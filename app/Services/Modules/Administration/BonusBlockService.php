<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Incentives\Bonus\BonusBlock;
use App\Models\Modules\Administration\Incentives\Bonus\BonusBlockItem;
use App\Models\Modules\Administration\Incentives\Bonus\BonusBlockItemTarget;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Models\Modules\Administration\Intelligence\Indicator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class BonusBlockService
{

    public static function show(string $blockId, string $monthRef)
    {
        try {
            $bonusBlock = BonusBlock::with([
                'owner',
                'fiscalYear',
                'createdBy',
                'updatedBy',
                'blockItens.indicator',
                'blockItens.grade' => function ($query) use ($monthRef) {
                    $query->where('month_ref', $monthRef);
                }
            ])
                ->where('public_id', $blockId)
                ->first();

            $bonusBlock->itensWeight = BonusBlockService::getFullweight($bonusBlock->public_id);
            $bonusBlock->grade = BonusBlockService::getBlockGrade($bonusBlock->public_id, $monthRef);

            $bonusBlock->blockItens->each(function($item) use($blockId){

                $item['chart'] = BonusBlockService::getChartDataItem($blockId, $item->indicator->id);

                return $item;
            });

            return $bonusBlock;
        } catch (\Throwable $th) {
            return new \Exception($th->getMessage());
        };
    }
    public static function store(Request $request)
    {
        try {

            $fiscalYear = BonusFiscalYear::create($request->all());

            if ($fiscalYear->active['id'] == 1) {
                BonusFiscalYear::where('id', '<>', $fiscalYear->id)
                    ->update(['active' => 0]);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function update($fiscalYear, Request $request)
    {
        try {
            $fiscalYear->active = $request->active;
            $fiscalYear->save();

            if ($fiscalYear->active['id'] == 1) {
                BonusFiscalYear::where('id', '<>', $fiscalYear->id)
                    ->update(['active' => 0]);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function getFullweight($blockId, $id = null)
    {
        return BonusBlockItem::where('block_id', $blockId)
            ->where(function ($uqery) use ($id) {
                if ($id) {
                    $uqery->where('id', '<>', $id);
                }
            })
            ->sum('weight');

        return $fullWeight;
    }

    public static function storeItemTarget($blockItem)
    {
        try {
            $fiscalYear = BonusFiscalYear::where('public_id', $blockItem->fiscal_year_id)->first();

            $start = Carbon::parse($fiscalYear->month_start);
            $end = Carbon::parse($fiscalYear->month_end);


            $period = CarbonPeriod::create($start, '1 month', $end);

            $item = [
                'fiscal_year_id' => $blockItem->fiscal_year_id,
                'item_id' => $blockItem->id,
                'indicator_id' => $blockItem->indicator_id,
                'block_id' => $blockItem->block_id,
                'weight' => $blockItem->weight,
            ];


            foreach ($period as $date) {

                $item['month_ref'] = $date->copy()->format('Y-m-d');
                $item['month_detour'] = self::getDetour(null, null, $blockItem);
                $item['accumulated_detour'] = self::getDetour(null, null, $blockItem);

                BonusBlockItemTarget::create($item);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function updateItemTarget($blockItem, $targets)
    {
        foreach ($targets as $item) {
            try {
                $target = BonusBlockItemTarget::find($item['id']);
                $month_result = isset($item['month_result']) ? $item['month_result'] : null;
                $month_target = isset($item['month_target']) ? $item['month_target'] : null;
                $accumulated_target = isset($item['accumulated_target']) ? $item['accumulated_target'] : null;
                $accumulated_result = isset($item['accumulated_result']) ? $item['accumulated_result'] : null;

                $target->month_target = $month_target;
                $target->month_result = $month_result;
                $target->accumulated_target = $accumulated_target;
                $target->accumulated_result = $accumulated_result;

                $target->save();
               
            } catch (\Throwable $th) {
                new \Exception($th->getMessage());
            }
        }

        try {
            self::getAccumulated($targets, $blockItem);
         
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function getDetour(float | null $result, float | null $target, $blockItem)
    {
        try {
            $indicatorId = $blockItem->indicator_id;

            $dif = round($result - $target, 2);
            $percentage = $target == 0 ? round($result, 2) : null;
            $percentage = $result && $target ? round(((float) $dif / (float) $target) * 100, 2) : $percentage;

            $gr = self::getGR($result, $target, $indicatorId);
            $range = self::getRange($gr, $blockItem->range);

            $grade = self::getItemGrade($gr, $blockItem->range);

            return [
                'detour' => !is_null($result) && !is_null($target) ? $dif : '-',
                'percentage' => !is_null($percentage) ? $percentage . "%" : '-',
                'gr' => !is_null($gr) ? round($gr, 2) : '-',
                'grade' => !is_null($grade) ? round($grade, 2) : '-',
                'icon' => $range['icon'],
                'color' => $range['color'],
                'rgb' => $range['rgb'],
                'border' => $range['border'],
            ];
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public  static function getGR($result, $target, $indicatorId)
    {
        $indicator = Indicator::find($indicatorId);

        $gr = null;

        if (is_null($result) || is_null($target)) {

            return null;
        }

        if ($target == 0) {
            return 100;
        }

        if ($indicator->direction['id'] === 1) {
            $gr = ($result / $target) * 100;
        }

        if ($indicator->direction['id'] === 0) {
            $dif = $target - $result;

            $gr = ($dif / $target) * 100 + 100;
        }

        return $gr;
    }

    public static function getItemGrade($gr, $ranges)
    {
        try {
            // Se o GR for menor que o menor GR da tabela, retorna a menor nota
            if ($gr <= $ranges[0]['gr']) {
                return $ranges[0]['grade'];
            }

            // Se o GR for maior que o maior GR da tabela, retorna a maior nota
            if ($gr >= end($ranges)['gr']) {
                return end($ranges)['grade'];
            }

            // Percorre as faixas e encontra onde o GR está localizado
            for ($i = 0; $i < count($ranges) - 1; $i++) {
                $grMin = $ranges[$i]['gr'];
                $grMax = $ranges[$i + 1]['gr'];
                $gradeMin = $ranges[$i]['grade'];
                $gradeMax = $ranges[$i + 1]['grade'];

                if ($gr >= $grMin && $gr <= $grMax) {
                    // Apply linear interpolation
                    return $gradeMin + (($gr - $grMin) * ($gradeMax - $gradeMin)) / ($grMax - $grMin);
                }
            }

            return null; // Caso inesperado

        } catch (\Throwable $th) {

            new \Exception($th->getMessage());
        }
    }

    public static function getBlockGrade($blockId, $month)
    {
        $grade = BonusBlockItemTarget::where('block_id', $blockId)
            ->where('month_ref', $month)
            ->sum('grade_weight');
        return round($grade, 2);
    }

    public static function getRange($gr, $blockRanges)
    {

        $ranges = collect(BonusInterface::BLOCK_RANGES);

        $grMin = $blockRanges[0]['gr'];

        if ($gr < $grMin && !is_null($gr)) {
            return $ranges[1];
        }

        if (is_null($gr)) {
            return $ranges[0];
        }

        $range = collect($blockRanges)->reverse()->filter(function ($item) use ($gr) {

            $grTarget = $item['gr'];
            $comparison = $gr >= $grTarget;

            return $comparison ? $item : null;
        })->first();

        $range['icon'] = $ranges[$range['id']]['icon'];
        $range['color'] = $ranges[$range['id']]['color'];
        $range['rgb'] = $ranges[$range['id']]['rgb'];
        $range['border'] = $ranges[$range['id']]['border'];

        return $range;
    }

    public static function getAccumulated($targets, $blockItem)
    {
        $accumulationType = $blockItem->accumulation_type['id'];

        $accumulatedTarget = 0;
        $accumulatedResult = 0;

        $accumulatedTargetSum = 0;
        $accumulatedResultSum = 0;

        $n = 1;

        foreach ($targets as $target) {

            $target = BonusBlockItemTarget::find($target['id']);
           
            $accumulatedTargetSum += $target['month_target'];
            $accumulatedResultSum += $target['month_result'];

            if ($accumulationType == BonusInterface::ACCUMULATION_TYPE_MANUAL) {
                $accumulatedTarget = $target['accumulated_target'];
                $accumulatedResult = $target['accumulated_result'];
            }

            if ($accumulationType == BonusInterface::ACCUMULATION_TYPE_SUM) {
               
                $accumulatedTarget = $accumulatedTargetSum;
                $accumulatedResult = $accumulatedResultSum;
            }

            if ($accumulationType == BonusInterface::ACCUMULATION_TYPE_EQUAL) {
                $accumulatedTarget = $target['month_target'];
                $accumulatedResult = $target['month_result'];
            }

            if ($accumulationType == BonusInterface::ACCUMULATION_TYPE_AVERAGE) {
                $accumulatedTarget = $accumulatedTargetSum / $n;
                $accumulatedResult = $accumulatedResultSum / $n;
            }

            if ($blockItem->accumulation_type != 0) {
                $target->accumulated_target = round($accumulatedTarget, 2);
                $target->accumulated_result =  round($accumulatedResult, 2);
            }

            if ($target['month_target'] === null || $target['month_result'] === null) {
                $target->accumulated_target = null;
                $target->accumulated_result = null;
                $target->month_detour = self::getDetour(null, null, $blockItem);
                $target->accumulated_detour = self::getDetour(null, null, $blockItem);
                $target->grade = null;
                $target->save();
            } else {
                $target->month_detour = self::getDetour($target['month_result'], $target['month_target'], $blockItem);
                $target->accumulated_detour = self::getDetour($accumulatedResult,  $accumulatedTarget, $blockItem);
                $target->grade = $target->accumulated_detour['grade'] ?? null;
                $target->grade_weight = $target->grade * ($blockItem->weight / 100);

                $target->save();
            }

            $n++;
        }
    }

    public static function getActiveMonth($fiscalYearId)
    {
        $fiscalYear = BonusFiscalYear::where('public_id', $fiscalYearId)->first();

        if(!$fiscalYear) return null;

        $start = Carbon::parse($fiscalYear->month_start);
        $end = Carbon::parse($fiscalYear->month_end);

        $activeMonth = Carbon::now()->startOfMonth();

        if ($activeMonth < $start) {
            $activeMonth = $start;
        }
        if ($activeMonth > $end) {
            $activeMonth = $end;
        }

        return $activeMonth->format('Y-m-d');
    }

    public static function getMonths($fiscalYearId)
    {

        if(!$fiscalYearId) return [];
        $fiscalYear = BonusFiscalYear::where('public_id', $fiscalYearId)->first();

        $start = Carbon::parse($fiscalYear->month_start);
        $end = Carbon::parse($fiscalYear->month_end);

        $period = CarbonPeriod::create($start, '1 month', $end);

        $months = [];

        foreach ($period as $date) {
            $months[] = [
                'label' => $date->format('Y-m'),
                'value' => $date->format('Y-m-d'),
            ];
        }

        return $months;
    }

    public static function getChartDataItem($blockId, $indicatorId)
    {
        $results =  BonusBlockItemTarget::where('block_id', $blockId)
            ->where('indicator_id', $indicatorId)
            ->orderBy('month_ref')
            ->get([
                'month_ref',
                'month_target',
                'month_result',
                'month_detour',
                'accumulated_target',
                'accumulated_result',
                'accumulated_detour',
            ]);

        $chart = [];
        $chart['year'] = Carbon::parse($results[0]['month_ref'])->format('Y');

        foreach ($results as $result) {
            $month = Carbon::parse($result['month_ref']);
            $chart['label'][] = $month->locale('pt_BR')->translatedFormat('M');
            $chart['month_target'][] = $result['month_target'];
            $chart['month_result'][] = $result['month_result'];
            $chart['month_rgb'][] = $result['month_detour']['rgb'];
            $chart['accumulated_target'][] = $result['accumulated_target'];
            $chart['accumulated_result'][] = $result['accumulated_result'];
            $chart['accumulated_rgb'][] = $result['accumulated_detour']['rgb'];
        }
        return $chart;
    }
}
