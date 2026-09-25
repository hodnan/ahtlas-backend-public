<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Report;

use App\Models\Modules\TacticalCenter\Report\ReportFavorite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class ReportFavoriteController extends Controller
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
        $key = [
            'created_by' => Auth::user()->username,
            'report_id' => $request->report_id,
        ];

        $data = [
            'report_id' => $request->report_id,
        ];

        $favotite = ReportFavorite::updateOrCreate($key, $data);

        $favotite->is_favorite = !$favotite->is_favorite;

        $favotite->save();
    }
}
