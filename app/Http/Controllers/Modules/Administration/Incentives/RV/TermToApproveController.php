<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermApproveRequest;
use App\Jobs\Modules\Administration\Incentives\RV\TermEmployeeToSignJob;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Models\Modules\Employee\SectorN1Manager;
use App\Services\Core\User\MetadataService;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Employee\EmployeeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TermToApproveController extends Controller
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

            $sectors = SectorN1Manager::where('manager_username', $username)->pluck('sector_n1_id')->toArray();

            $terms =  Term::with(['sectorN1', 'sectorN2'])
                ->whereIn('sector_n1_id',  $sectors)
                ->where('month_ref', $monthRef)
                ->orderBy('id')
                ->get();

            return ApiResponser::success(null, null, [
                'terms' => $terms,
                'sectors' => $sectors,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermApproveRequest $request, Term $term)
    {
        try {
            $term->status =  $request->status;
            $term->approved_meta = MetadataService::getMetadata($request);
            $term->approved_by = Auth::user()->username;
            $term->approved_at = Carbon::now();
            $term->save();

            // Log::info('xxx',[$term->id, $term->status])
            if ($term->status['id'] == TermInterface::STATUS_APPROVED) {
                TermEmployeeToSignJob::dispatch($term->id)
                    ->onQueue(TermInterface::QUEUE);
            }

            return ApiResponser::success('Sua Assinatura foi registrada com sucesso', null, null);
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
