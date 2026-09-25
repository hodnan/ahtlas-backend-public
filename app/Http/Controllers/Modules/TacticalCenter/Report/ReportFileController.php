<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Report;

use App\Models\Modules\TacticalCenter\Report\ReportFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\TacticalCenter\Report\ReportFileRequest;
use App\Models\Modules\TacticalCenter\Report\Report;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ReportFileController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportFileRequest $request)
    {
        try {
            $uuid = $request->report_uuid;
            $monthRef = Carbon::parse($request->month_ref)->format('Y-m-d');
            $year =  Carbon::parse($request->month_ref)->format('Y');
            $month =  Carbon::parse($request->month_ref)->format('m');

            // pasta do arquivo
            $filePath = ReportInterface::STORAGE_FILE_PATH . "/$uuid/$year";

            // Obter a extensão original do arquivo
            $extension = $request->file('file')->getClientOriginalExtension();

            // Nome do arquivo com extensão
            $fileName =  "$month.$extension";

            // caoominho completo para o arquivo
            $file = "$filePath/$fileName";

            if ($request->hasFile('file')) {

                // Verificar se o relatório atual tem um arquivo e deletar o arquivo existente
                Storage::disk(ReportInterface::STORAGE_FILE_DISK)->delete($file);

                // Salvar o arquivo com o nome normalizado
                $request->file('file')->storeAs($filePath, $fileName, ReportInterface::STORAGE_FILE_DISK);

                $check = [
                    'report_uuid' => $uuid,
                    'month_ref' => $monthRef,
                ];

                $data = [
                    'year_ref' => $year,
                    'month_ref' => $monthRef,
                    'report_uuid' => $uuid,
                    'file' => $file,
                    'updated_at' => Carbon::now(),
                ];
                

                ReportFile::updateOrCreate($check, $data);
            }
            return ApiResponser::success('Arquivo salvo com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao salvar o arquivo', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid, string $year)
    {
        try {
            $reportFiles = ReportFile::where('report_uuid', $uuid)
                ->where('year_ref', $year)
                ->orderBy('month_ref', 'desc')
                ->get();

            return ApiResponser::success(null, null, [
                'report_files' => $reportFiles
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista de arquivos ', ['generic' => [['Erro ao carregar lista de arquivos']]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function download(ReportFile $report)
    {
        try {
            $filePath = $report->file;

            if (Storage::disk(ReportInterface::STORAGE_FILE_DISK)->exists($filePath)) {
                $originalExtension = explode('.', $report->file)[1];
    
                $downloadName = normalizeString($report->report->label) . '.' . $originalExtension;
    
                /** @disregard */
                return Storage::disk(ReportInterface::STORAGE_FILE_DISK)->download($filePath, $downloadName);
            } else {
                return ApiResponser::error('Arquivo não localizado ', [['Falha ao baixar arquivo']]);
            }
        } catch (\Throwable $th) {
            return ApiResponser::error('Erro ao baixar relatório ', [['Falha ao baixar arquivo']]);
        }
        
    }
}
