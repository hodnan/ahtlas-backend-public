<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermSignatureRequest;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Services\Core\User\MetadataService;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use App\Services\Modules\Administration\TermSignatureService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TermToSignatureController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();

        $username = Auth::user()->username;
        $monthRef = $data['month_ref'] ? Carbon::parse($request->month_ref)->format("Y-m-d") : Carbon::now()->startOfMonth()->format("Y-m-d");

        try {
            $terms = TermSignature::with(['term'])
                ->where('username', $username)
                ->where('month_ref', $monthRef)
                ->where(function($query){
                    $query->whereHas('term', function ($query) {
                        $query->where('status', TermInterface::STATUS_APPROVED);
                    });
                    $query->orWhere(function ($query) {
                        $query->whereNotNull('uuid');
                    });
                })   
                ->orderBy('id')
                ->get();

            return ApiResponser::success(null, null, [
                'terms' => $terms,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $signature)
    {
        try {

            // Log::info(TermSignature::with(['term'])->find($signature));

            $signature = TermSignature::with(['term'])
            // ->join('rv_terms_evaluations', function ($join) {
            //     $join->on('rv_terms_evaluations.username', '=', 'rv_terms_signatures.username')
            //     ->on('rv_terms_evaluations.term_id', '=', 'rv_terms_signatures.term_id')  
            //         ->where('rv_terms_evaluations.indicator_id', -1000);
            // })
            ->select([
                'rv_terms_signatures.id',
                'rv_terms_signatures.uuid',
                'rv_terms_signatures.term_id',
                'rv_terms_signatures.term_file',
                'rv_terms_signatures.month_ref',
                'rv_terms_signatures.username',
                'rv_terms_signatures.accept',
                'rv_terms_signatures.updated_at',
                'rv_terms_signatures.created_at',
                // 'rv_terms_evaluations.evaluation_details',
            ]) ->find($signature)
           ;
           
            return ApiResponser::success(null, null, [
                'signature' => $signature ,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao abrir termo', [['Erro ao abrir termo']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermSignatureRequest $request, TermSignature $signature)
    {
        try {
           
            $signature->accept =  $request->accept;
            $signature->uuid = uuid_create();
            $signature->accept_meta = MetadataService::getMetadata($request);
            $signature->save();
           
            TermSignatureService::storeTermFile($signature->id);

            return ApiResponser::success('Sua Assintatura foi registrada com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
