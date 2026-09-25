<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Services\Modules\Administration\TermInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TermAdherenceController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->all();

        $username = Auth::user()->username;

        $monthRef = isset($data['month_ref']) ? Carbon::parse($request->month_ref)->format("Y-m-d") : Carbon::now()->startOfMonth()->format("Y-m-d");

        try {
            $terms = TermSignature::with(['term', 'user'])
                ->whereHas('user', function ($query) use ($username) {
                    $query->where('username', $username);
                    $query->orWhere('manager_n1_id', $username);
                    $query->orWhere('manager_n2_id', $username);
                    $query->orWhere('manager_n3_id', $username);
                    $query->orWhere('manager_n4_id', $username);
                    $query->orWhere('manager_n5_id', $username);
                })
                ->where(function ($query) {
                    $query->whereHas('term', function ($query) {
                        $query->where('status', TermInterface::STATUS_APPROVED);
                    });
                    $query->orWhere(function ($query) {
                        $query->whereNotNull('uuid');
                    });
                })
                ->where('month_ref', $monthRef)
                ->orderBy('term_id')
                ->orderBy('username')
                ->get(['id', 'term_id', 'month_ref', 'username', 'accept', 'updated_at']);

            return ApiResponser::success(null, null, [
                'terms' => $terms,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    public function download(Request $request)
    {

        $data = $request->all();

        $username = Auth::user()->username;

        $monthRef = isset($data['month_ref']) ? Carbon::parse($request->month_ref)->format("Y-m-d") : Carbon::now()->startOfMonth()->format("Y-m-d");

        $terms = TermSignature::with(['term', 'user'])
            ->whereHas('user', function ($query) use ($username) {
                $query->where('username', $username);
                $query->orWhere('manager_n1_id', $username);
                $query->orWhere('manager_n2_id', $username);
                $query->orWhere('manager_n3_id', $username);
                $query->orWhere('manager_n4_id', $username);
                $query->orWhere('manager_n5_id', $username);
            })
            ->where(function ($query) {
                $query->whereHas('term', function ($query) {
                    $query->where('status', TermInterface::STATUS_APPROVED);
                });
                $query->orWhere(function ($query) {
                    $query->whereNotNull('uuid');
                });
            })
            ->where('month_ref', $monthRef)
            ->orderBy('term_id')
            ->orderBy('username')
            ->get(['id', 'term_id', 'month_ref', 'username', 'accept', 'created_at', 'updated_at'])
            ->toArray();

        $terms =  array_map(function ($item) {

            $temp['term_id'] = $item['term_id'];
            $temp['term_name'] = $item['term']['term_name'];
            $temp['term_status'] =  $item['term']['status']['label'];
            $temp['term_approved_at'] = $item['term']['approved_at'];
            $temp['term_sector_n1_id'] = $item['term']['sector_n1_id'];
            $temp['term_sector_n2_id'] = $item['term']['sector_n2_id'];
            $temp['employee_username'] = $item['username'];
            $temp['employee_name'] = $item['user'] ? $item['user']['name'] : null;
            $temp['employee_sector_n1_id'] = $item['user'] ? $item['user']['sector_n1_id'] : null;
            $temp['employee_sector_n2_id'] = $item['user'] ? $item['user']['sector_n2_id'] : null;
            $temp['employee_accept'] = $item['accept']['label'];
            $temp['created_at'] =   $item['created_at'];
            $temp['signed_at'] = $item['accept']['id'] ? $item['updated_at'] : null;

            return $temp;
        }, $terms);

        $csvContent = makeCsv($terms);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=" . $monthRef . "_terms_adherence.csv",
        ];

        return response()->make($csvContent, 200, $headers);
    }
}
