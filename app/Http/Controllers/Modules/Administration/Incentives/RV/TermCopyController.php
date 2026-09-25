<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermRequest;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class TermCopyController extends Controller
{
    use ApiResponser;

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $term = TermService::getTermById((int)$id);
            return ApiResponser::success(null, null, [
                'term_copy' => $term,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao abrir termo', [['Erro ao abrir termo']]);
        }
    }


}
