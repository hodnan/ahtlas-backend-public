<?php

namespace App\Http\Controllers\Modules\ForMe;


use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ForMe\Trade\TradeTimeRequest;
use App\Http\Requests\Modules\ForMe\Trade\TradeTimeUpdateRequest;
use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use App\Services\Modules\ForMe\Trade\TradeTimeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class TradeTimeController extends Controller
{
    use ApiResponser;

    public function display()
    {
         /** 
         * @disregard 
         */
        $user = Auth()->user();
        try {
            $times = TradeTimeService::getTimes($user->sector_n1_id);
            $statuses = TradetimeInterface::STATUSLISTREG;
            $trades = TradeTime::with(['user', 'sectorN1'])->where('username', $user->username)->orderBy('id', 'desc')->get();
            return ApiResponser::success(null, null, ['trades' => $trades, 'times' => $times, 'statuses' =>  $statuses]);

        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ',[[['Erro ao carregar lista ']]]);
        }
    }

    public function displayAdmin(Request $request)
    {
        $status = $request->status ?? [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED];
        $statuses = TradetimeInterface::STATUSLISTFILTER;

        try {
            $trades = TradeTime::with(['user', 'sectorN1'])
                ->orderBy('id', 'desc')
                ->whereIn('status', $status)
                ->get();
            return ApiResponser::success(null, ['statuses' => $statuses], ['trades' => $trades]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [['Erro ao carregar lista ']]);
        }
    }

  
    public function allTradesDownload(Request $request)
    {
        $status = $request->status ?? [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED];

        $trades = TradeTime::where(function ($query) use ($status) {
            if ($status) {
                $query->whereIn('status', $status);
            }
        })
            ->get([
                'id',
                'username',
                'sector_n1_id',
                'time_old',
                'time_label',
                'time_start',
                'time_end',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
                'notes'
            ])
            ->makeHidden(['status_color', 'expires_in', 'status'])
            ->toArray();

        $csvContent = makeCsv($trades);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="trade_times.csv"',
        ];

        return response()->make($csvContent, 200, $headers);
    }

    public function timeStore(TradeTimeRequest $request)
    {
        try {
            /** 
         * @disregard 
         */
            $user = Auth()->user();
            $data = $request->all();
            $time = $request->time;

            $data['username'] = $user->username;
            $data['sector_n1_id'] = $user->sector_n1_id;
            $data['time_old'] = $user->start_time;
            $data['time_label'] = $time['label'];
            $data['time_start'] = $time['time_start'];
            $data['time_end'] = $time['time_end'];
            $data['status'] = TradetimeInterface::STATUS_PENDING;

            TradeTime::create($data);
            return ApiResponser::success('Pedido criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao criar pedido']]);
        }
    }

    public function timeUpdate(TradeTimeUpdateRequest $request)
    {
        try {
            $data = TradeTime::where('id', $request->id)->first();
            $data->notes = $request->notes;
            $data->status = $request->status;
            $data->save();
            return ApiResponser::success('Pedido atualizado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizado pedido', [['Erro ao atualizado pedido']]);
        }
    }
}
