<?php

namespace App\Http\Controllers\Modules\Administration;

use App\Http\Controllers\Controller;
use App\Models\Core\Info;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class Core extends Controller
{
    use ApiResponser;
    
    public function infoDisplay()
    {
        try {
            $data = Info::all();
            return ApiResponser::success(null,$data );
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [['Erro ao listar dados']]);
        }
    }
}
