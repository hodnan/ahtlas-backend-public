<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\AuthRequest;
use App\Models\Core\MenuN2;
use App\Models\Core\Module;
use App\Models\Core\User;
use App\Models\Core\UserAvatar;
use App\Services\Core\ApiResponse\ApiResponseInterface;
use App\Services\Core\Auth\AuthInterface;
use App\Services\Core\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $navigation = AuthService::navigation($user);
            $server = AuthService::getServer($request);

            return ApiResponser::success(
                null,
                [
                    'user' => $user,
                    'navigation' => $navigation,
                    'server' =>  $server,
                    '2' =>  php_uname('n')

                ]
            );
        } catch (\Throwable $e) {
            return ApiResponser::error('Não existe usuário logado', [['Não existe usuário logado']]);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthRequest $request)
    {
        return AuthService::login($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Request $request)
    {
        try {
            if (Auth::user()) {
                return ApiResponser::success(
                    'Já existe sessão ativa', [ 'is_active' => true]
                );
            } else {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            AuthService::logout($request);
            return ApiResponser::success(null, null);
        } catch (\Throwable $th) {
            return ApiResponser::success(null, null);
        }
    }
}
