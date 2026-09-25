<?php

namespace App\Http\Controllers\Modules\People\Employee;

use App\Http\Controllers\Controller;
use App\Services\Modules\Employee\EmployeeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class EmployeeBirthdateOnMonthController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $employees = EmployeeService::getBirthdateOnMonth();

            return ApiResponser::success(null, null, [

                'birthdate_on_month' => $employees,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar ', [[['Erro ao listar aniversariante']]]);
        }
    }
}
