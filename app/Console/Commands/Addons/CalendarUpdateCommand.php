<?php

namespace App\Console\Commands\Addons;

use App\Models\Addons\Calendar;
use App\Models\Addons\Telegram;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o calendário';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $toDay = Carbon::now()->format('Y-m-d');

            // marca como passado os dias menores do que hoje
            Calendar::where('passed', 0)
                ->where('date', '<', $toDay)
                ->update([
                    'passed' => 1
                ]);

            //marca como ativo a data de hoje
            Calendar::where('date',  $toDay)
                ->update([
                    'active' => 1
                ]);
                
        } catch (\Throwable $th) {
            Log::error('Calendar - update', [$th->getMessage()]);
        }


        return Command::SUCCESS;
    }
}
