<?php

namespace App\Console\Commands\Modules\TacticalCenter\Reports;

use App\Models\Modules\TacticalCenter\Report\Report;
use App\Services\Modules\TacticalCenter\Bulletin\BulletinHourHourService;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ReportCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $reports =  Report::where('type', 1)->where('active', 1)->whereNotNull('report')->get(['id', 'title', 'report']);

        $filePath = ReportInterface::STORAGE_FILE_PATH;
        $sourceDisk = ReportInterface::STORAGE_FILE_DISK;
        $temp = [];

        foreach ($reports as $report) {

            $sourcePath = $filePath . '/' . $report->report;

            $originalExtension = explode('.', $report->report)[1];
            $destinationPath = 'CUBOS-MIGRACAO-CLOUD/' . str_pad($report->id, 4, '0', STR_PAD_LEFT) . ' - ' . normalizeString($report->title) . '.' . $originalExtension;

            $temp[] = $destinationPath;

            if (!Storage::disk($sourceDisk)->exists($sourcePath)) {
                return "Arquivo não encontrado: $sourcePath";
            }

            Storage::disk($sourceDisk)->move($sourcePath, $destinationPath);
        }


        return Command::SUCCESS;
    }
}
