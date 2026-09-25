<?php

namespace App\Console\Commands\Addons;

use App\Models\Addons\Calendar;
use App\Models\Addons\Telegram;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:create';

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
        $first = Carbon::now()->startOfYear();
        $last = Carbon::now()->addYear()->endOfYear();

        $current = $first->copy(); // Criar uma cópia para evitar modificar o original

        while ($current->lessThanOrEqualTo($last)) {

            $yearRef = $current->copy()->startOfYear()->format('Y-m-d'); // Carbon usa ISO-8601, então segunda-feira é 1 e domingo é 7
            $monthRef = $current->copy()->startOfMonth()->format('Y-m-d'); // Carbon usa ISO-8601, então segunda-feira é 1 e domingo é 7
            $weekday = $current->copy()->dayOfWeekIso; // Carbon usa ISO-8601, então segunda-feira é 1 e domingo é 7

            $data = [
                'year_ref' => $yearRef,
                'month_ref' => $monthRef,
                'year' => $current->year,
                'month' => $current->month,
                'day' => $current->day,
                'date' => $current->toDateString(),
                'weekday' => $weekday,
                'business_day' => !in_array($weekday, [7, 1]), // Ajustado para o ISO-8601
                'active' => 0,
                'passed' => 0,
            ];

            try {
                Calendar::updateOrCreate($data, ['date' => $data['date']]);
            } catch (\Throwable $th) {
                Log::error('Calendar - Create', [$th->getMessage()]);
            }

            $current->addDay(); // Adiciona um dia ao current
        }

        return Command::SUCCESS;
    }
}
