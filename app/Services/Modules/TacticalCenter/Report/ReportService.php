<?php

namespace App\Services\Modules\TacticalCenter\Report;

use App\Models\Modules\Employee\Employee;
use App\Models\Modules\TacticalCenter\Report\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService implements ReportInterface
{
    public static function storeFile(Request $request, int $id)
    {
        $report = Report::find($id);

        try {
            if ($request->hasFile('file')) {

                // Verificar se o relatório atual tem um arquivo e deletar o arquivo existente
                if ($report->report) {
                    $currentFilePath = ReportInterface::STORAGE_FILE_PATH . '/' . $report->report;
                    Storage::disk(ReportInterface::STORAGE_FILE_DISK)->delete($currentFilePath);
                }

                // Normalizar o nome
                $normalizedTitle = $report->uuid;

                // Obter a extensão original do arquivo
                $extension = $request->file('file')->getClientOriginalExtension();

                // Criar o nome do arquivo com a extensão
                $fileName = $normalizedTitle . '.' . $extension;

                // Salvar o arquivo com o nome normalizado
                $request->file('file')->storeAs(ReportInterface::STORAGE_FILE_PATH, $fileName, ReportInterface::STORAGE_FILE_DISK);

                $report->report = $fileName;
                $report->save();

                return true;
            }
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function downloadFile(int $id): StreamedResponse
    {
        $report = Report::findOrFail($id);

        if ($report->report) {
            $filePath = ReportInterface::STORAGE_FILE_PATH . '/' . $report->report;

            if (Storage::disk(ReportInterface::STORAGE_FILE_DISK)->exists($filePath)) {
                $originalExtension = explode('.', $report->report)[1];

                $downloadName = normalizeString($report->label) . '.' . $originalExtension;

                /** @disregard */
                return Storage::disk(ReportInterface::STORAGE_FILE_DISK)->download($filePath, $downloadName);
            } else {
                  /** @disregard */
                return response()->json(['error' => 'File not found.'], 404);
            }
        } else {
              /** @disregard */
            return response()->json(['error' => 'No file associated with this report.'], 404);
        }
    }

    public static function getReports($request = null)
    {
        $username = Auth::user()->username;
        $userType = Auth::user()->type;

        // pega o grupo default se parametro não existir no filtro
        $group = !isset($request['filter']) && !isset($request['group']) ? ReportInterface::GOUPS_DEFAULT[$userType] : $request['group'];

        $reports = Report::with(['owners.user'])
            ->leftJoin('report_favorites', function ($join) use ($username) {
                $join->on('report_favorites.report_id', '=', 'reports.id')
                    ->where('report_favorites.created_by', $username);
            })
            ->select([
                'reports.id',
                'reports.uuid',
                'reports.title',
                'reports.type',
                'reports.group',
                'reports.report',
                'reports.active',
                DB::RAW("CASE WHEN report_favorites.is_favorite = true then true else false end as is_favorite"),
            ])
            ->where(function ($query) use ($request) {
                if (isset($request['active'])) {
                    $query->where('active', $request['active']);
                }
                if (isset($request['type'])) {
                    $query->where('type', $request['type']);
                }
            })
            ->where(function($query) use ($group){
                if($group) $query->where('reports.group',  $group );
            })
            ->where(function ($query) use ($userType) {
                if ($userType == 'ext') $query->where('reports.group', ReportInterface::GROUP_PARTNER_A);
                if ($userType == 'prb') $query->where('reports.group', ReportInterface::GROUP_PARTNER_B);
            })
            ->orderByRaw('CASE WHEN report_favorites.is_favorite = true THEN 1 ELSE 0 END DESC')
            ->orderBy('title')
            ->orderBy('type')
            ->get();

        return  $reports;
    }

    public static function getOwners()
    {
        $owners = Employee::with(['avatar'])->where('active', 1)
            ->whereIn('manager_n3_id', config('ahtlas.reports.owner_managers_n3'))
            ->where('staff', 1)
            ->orWhere(function ($query) {
                $query->whereIn('username', config('ahtlas.reports.owner_users'));
            })
            ->get(['username', 'name', 'position_summary', 'hierarchical_level']);

        return  $owners;
    }

    public static function isOwner()
    {
        /** @disregard */
        $username = auth()->user()->username;

        $isOwner = self::getOwners()->contains('username', $username);

        return $isOwner;
    }
}
