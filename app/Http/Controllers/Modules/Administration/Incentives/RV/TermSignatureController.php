<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;


use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermQueryRequest;
use App\Models\Modules\Administration\Incentives\RV\TermPanelSignature;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermSignatureService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TermSignatureController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {
            $username = preg_replace('/\D/', '', $request->username);
            $employee = Employee::with(['avatar'])->where('username', 'usr' . $username)->first(['username', 'name', 'admission', 'dismissal']) ?? false;

            $terms = TermSignatureService::getTermsByUser($username);

            return ApiResponser::success(null, null, [
                'employee' => $employee,
                'terms' => $terms,

            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TermQueryRequest $request, String $username)
    {
        try {
            $username =  $request->username;
            $filePath = TermInterface::STORAGE_FILE_TEMP . "/" . $username . '.zip';
            TermSignatureService::downloadTermsByUser($username);

            if (Storage::exists($filePath)) {
                return response()->streamDownload(function () use ($filePath) {
                    echo Storage::get($filePath);
                    Storage::delete($filePath); // Deleta o arquivo após o envio
                }, "{$username}.zip");
            } else {
                return ApiResponser::error('Arquivo não localizado ', [['Falha ao baixar arquivo']]);
            }
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro gerar dados para download', [[['Erro gerar dados para download']]]);
        }
    }
}
