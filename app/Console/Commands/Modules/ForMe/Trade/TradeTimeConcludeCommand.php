<?php

namespace App\Console\Commands\Modules\ForMe\Trade;

use App\Models\Modules\ForMe\Trade\TradeTime;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


class TradeTimeConcludeCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tradetime:conclude';

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
        // Verifica quais colaboradores tiveram seus horarios trocadas para dentro da faixa no pedido 
        $tradeTimeIds = TradeTime::leftJoin('employees', 'employees.username', '=', 'trade_times.username')
        ->whereIn('trade_times.status', [TradetimeInterface::STATUS_PENDING, TradetimeInterface::STATUS_RENOVATED])
        ->whereRaw('employees.start_time BETWEEN trade_times.time_start AND trade_times.time_end')
        ->whereRaw('trade_times.time_old <> employees.start_time')
        ->pluck('trade_times.id');

        // Faz update dos pedidos para concluído 
        TradeTime::whereIn('id', $tradeTimeIds)
        ->update([
            'status' => TradetimeInterface::STATUS_APPROVED,
            'notes' => DB::raw("CONCAT('Conclusão automática - Horário atual do colaborador está dentro do intervalo solicitado \n', notes)")
        ]);

        return Command::SUCCESS;
    }
}
