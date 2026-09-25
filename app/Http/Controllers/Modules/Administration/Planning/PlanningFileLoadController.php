<?php

namespace App\Http\Controllers\Modules\Administration\Planning;

use App\Models\Modules\Administration\Planning\PlanningFileLoad;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Planning\PlanningFileLoadRequest;
use App\Services\Modules\Administration\PlanningFileInterface;
use App\Services\Modules\Administration\PlanningFileService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PlanningFileLoadController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $monthRef = isset($request['month_ref']) ? Carbon::parse($request['month_ref'])->firstOfMonth()->toDateString() : Carbon::now()->format('Y-m-01');

            $statuses = PlanningFileInterface::STATUSES;
            $types = PlanningFileInterface::TYPES;
            $charges = PlanningFileLoad::with(['createdBy'])
                ->where('month_ref', $monthRef)
                ->orderBy('id', 'desc')
                ->get();

            return ApiResponser::success(null, null, [
                'statuses' => $statuses,
                'types' => $types,
                'charges' => $charges,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(PlanningFileLoad $fileLoad, Request $request)
    {
        try {

            if ($request['status'] == PlanningFileInterface::STATUS_ERROR) {

                $errors = $fileLoad->errors;
                $errors['charge_error']['message'] = 'Erro ao carregar no destino';
                $fileLoad->errors =  $errors;
            }

            $fileLoad->status = $request['status'];
            $fileLoad->save();

            return ApiResponser::success('Dados atualizados', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao salvar o dados', [[['Erro ao salvar o dados']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanningFileLoadRequest $request)
    {
        try {
            PlanningFileService::store($request);
            return ApiResponser::success('Arquivo salvo com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao salvar o arquivo', [[['Erro ao salvar o arquivo']]]);
        }
    }

    public function download(PlanningFileLoad $fileLoad)
    {
        try {
            $filePath = $fileLoad->file;

            if (Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->exists($filePath)) {
                /** @disregard */
                return Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->download($filePath);
            } else {
                return ApiResponser::error('Arquivo não localizado ', [['Falha ao baixar arquivo']]);
            }
        } catch (\Throwable $th) {
            return ApiResponser::error('Erro ao baixar relatório ', [['Falha ao baixar arquivo']]);
        }
    }

    public function downloadModel(int $id)
    {
        try {

            $filePath = PlanningFileInterface::STORAGE_FILE_PATH . "\/Model\/" . PlanningFileInterface::TYPES[$id]['model'];

            if (Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->exists($filePath)) {
                /** @disregard */
                return Storage::disk(PlanningFileInterface::STORAGE_FILE_DISK)->download($filePath);
            } else {
                return ApiResponser::error('Arquivo não localizado ', [['Falha ao baixar arquivo']]);
            }
        } catch (\Throwable $th) {
            return ApiResponser::error('Erro ao baixar relatório ', [['Falha ao baixar arquivo']]);
        }
    }
}
