<?php

namespace App\Services\Modules\ForMe\Trade;

use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use App\Models\Core\User;
use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Models\Modules\ForMe\Trade\TradeTimeRange;
use Illuminate\Support\Facades\DB;


class TradeTimeService implements TradetimeInterface
{
    public static function getTrades($request)
    {
        $data = TradeTime::with(['sectorN1', 'user'])
            ->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->whereIn('status', $request->status);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        return   $data;
    }

    public static function getTimes($sectorN1Id)
    {
        try {
            $sectorTimes = User::where('sector_n1_id', $sectorN1Id)
                ->orderBy('start_time')
                ->groupBY('start_time')
                ->get('start_time');

            $times = TradeTimeRange::where(function ($query) use ($sectorTimes) {
                if (count($sectorTimes) > 0) {
                    foreach ($sectorTimes as $key => $value) {
                        $query->orWhereRaw("'$value->start_time' BETWEEN time_start AND time_end");
                    }
                }
            })
                ->get();
       
        } catch (\Throwable $th) {
            return [];
        }


        return $times;
    }
}
