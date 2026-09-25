<?php

namespace App\Http\Controllers\Addons\ServicePosition;

use App\Http\Controllers\Controller;
use App\Models\Addons\ServicePosition\ServicePositionOccupation;
use App\Services\Core\User\MetadataService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class ServicePositionOccupationController extends Controller
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
    public function store(Request $request)
    {
        try {
            $dateRef = Carbon::now();
            $monthRef = Carbon::now()->startOfMonth();
            $username =  str_replace("ext", "usr", strtolower($request->username));
            $meta = MetadataService::getMetadata($request, $username);

            $data = [
                'month_ref' => $monthRef,
                'date_ref' => $dateRef,
                'username' => $username,
                'report_id' => $request->reportId,
                'hostname' => $request->hostname,               
                'log' => $request->log,               
                'meta' => $meta,               
            ];

            ServicePositionOccupation::create($data);

            return ApiResponser::success( null, null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao registrar', [['Erro ao registrar']]  );
        }


    }

   
}
