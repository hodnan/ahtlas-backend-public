<?php

namespace App\Console\Commands\Modules\ForMe\Trade;

use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TradeTimeCancelCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tradetime:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela pedidos com mais de 45 dias';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(100)->startOfDay()->format('Y-m-d H:i:s');

        // Cancela pedidos com mais de 45 dias sem atualização
        TradeTime::whereIn('status', [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED])
            ->where('updated_at', '<', $date)
            ->update([
                'status' => TradetimeInterface::STATUS_CANCELED,
                'notes' => DB::raw("CONCAT('Cancelamento automático - Mais de 45 dias sem renovação. \n', notes)")
            ]);

        // Cancela pedidos de colaboradores desligados ou não localizados no GIP
        TradeTime::whereIn('status', [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED])
            ->with(['user'])->whereHas('user', function ($query) {
                $query->where('status', 0);
            })->update([
                'status' => TradetimeInterface::STATUS_CANCELED,
                'notes' => DB::raw("CONCAT('Cancelamento automático - Colaborador não localizado \n', notes)")
            ]);

        return Command::SUCCESS;
    }
}
