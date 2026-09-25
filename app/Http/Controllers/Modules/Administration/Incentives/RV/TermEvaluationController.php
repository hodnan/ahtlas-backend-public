<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermRequest;
use App\Services\Modules\Administration\TermEvaluationService;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class TermEvaluationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $terms =  TermEvaluationService::termsList($request->all());
            $sectors =  $request->isMethod('get') ? TermService::getSectors() : null;

            return ApiResponser::success(null, null, [
                'terms' => $terms,
                'sectors' => $sectors,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TermRequest $request)
    {
        try {
            TermService::termStore($request);
            return ApiResponser::success('Termo criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao criar pedido']]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $term = TermService::getTermById((int)$id);
            return ApiResponser::success(null, null, [
                'term' => $term,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao abrir termo', [['Erro ao abrir termo']]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function download(Request $request)
    {

        $monthRef = Carbon::parse($request->month_ref)->startOfMonth()->format('Y-m');

        $terms =  TermEvaluationService::termsList($request->all())->toArray();

        $terms =  array_map(function ($item) {

            $temp['id'] = $item['id'];
            $temp['term_id'] = $item['term_id'];
            $temp['term_name'] = $item['term']['term_name'];
            $temp['term_status'] = $item['term']['status']['label'];
            $temp['term_approved_at'] = $item['term']['approved_at'];
            $temp['term_sector_n1_id'] = $item['term']['sector_n1_id'];
            $temp['term_sector_n2_id'] = $item['term']['sector_n2_id'];
            $temp['username'] = $item['username'];
            $temp['name'] = isset($item['user']['name']) ? $item['user']['name'] : null;
            $temp['sector_n1_id'] = isset($item['user']['sector_n1_id']) ? $item['user']['sector_n1_id'] : null;
            $temp['sector_n2_id'] = isset($item['user']['sector_n2_id']) ? $item['user']['sector_n2_id'] : null;
            $temp['accept'] = $item['accept']['id'];
            $temp['uuid'] = $item['uuid'];
            $temp['created_at'] = $item['created_at'];
            $temp['updated_at'] = $item['updated_at'];

            return $temp;
        }, $terms);

        $csvContent = makeCsv($terms);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=" . $monthRef . "_terms_evaluetions.csv",
        ];

        return response()->make($csvContent, 200, $headers);
    }
}
