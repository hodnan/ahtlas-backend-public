<?php

namespace App\Http\Middleware;

use App\Services\Core\Auth\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Core\ApiResponse\ApiResponseInterface;
use App\Traits\ApiResponser;
use ReflectionClass;

class RoutePermission
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $user = Auth::user();
        
        $currentRoute = $request->getPathInfo();
       
        /** 
         * @disregard 
         */
        if ($user && !$user->isAdmin() && str_starts_with($currentRoute, '/api')) {

            $routes = AuthService::allowableRoutes($user);

            // Identifica e remove parâmetros dinâmicos da rota atual
            $reflection = new ReflectionClass($request->route()->getCompiled());
            $staticPrefixProperty = $reflection->getProperty('staticPrefix');
            $staticPrefixProperty->setAccessible(true);
            $currentRoute = $staticPrefixProperty->getValue($request->route()->getCompiled());
                                
            if (!in_array($currentRoute, $routes)) {
                $message = ['203' => ['message' => 'Acesso não autorizado']];

                return response()->json([
                    'status' => 'Error',
                    'message' => 'Unauthenticated',
                    'route' => $currentRoute,
                    'errors' =>  $message,
                ], ApiResponseInterface::CODE_UNAUTHORIZED);
            }
        }


        return $next($request);
    }
}
