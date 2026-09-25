<?php

namespace App\Http\Middleware;

use App\Models\Core\Log\LogAccess;
use App\Services\Core\Auth\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Core\ApiResponse\ApiResponseInterface;
use App\Services\Core\User\MetadataService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

        /** 
         * @disregard 
         */
        $isAdmin = $user->isAdmin();

        // Identifica e remove parâmetros dinâmicos da rota atual
        $reflection = new ReflectionClass($request->route()->getCompiled());
        $staticPrefixProperty = $reflection->getProperty('staticPrefix');
        $staticPrefixProperty->setAccessible(true);
        $currentRoute = $staticPrefixProperty->getValue($request->route()->getCompiled());

        $isAutorized = $isAdmin ?? false;

        // Se não for admin, verifica se a rota foi liberada
        if ($user && !$isAdmin) {
            $routes = AuthService::allowableRoutes($user);
            $isAutorized = in_array($currentRoute, $routes);
        }

        // Registra log de acessos
        self::logAccess($user->username, $request, $isAutorized, $currentRoute);

        // Bloqueia o acesso se não for autorizado
        if (!$isAutorized) {

       
            $message = ['203' => ['message' => 'Acesso não autorizado']];

            return response()->json([
                'status' => 'Error',
                'message' => 'Unauthenticated',
                'route' => $currentRoute,
                'errors' =>  $message,
            ], ApiResponseInterface::CODE_UNAUTHORIZED);
        }

        return $next($request);
    }

    public function logAccess($username, $request, $isAutorized, $currentRoute)
    {
        $currentRoute = str_replace('/api', '',  $currentRoute);
        $routeParameters = $request->route()->parameters();

        $ignore = [
            '/auth/user',
            '/auth/checking-session',
            '/auth/logout',
        ];

        if (!in_array($currentRoute, $ignore)) {

            LogAccess::create([
                'month_ref' => Carbon::now()->startOfMonth(),
                'date_ref' => Carbon::now(),
                'route_parameters' => $routeParameters,
                'username' => $username,
                'route' =>  $currentRoute,
                'method' =>  $request->method(),
                'meta' => MetadataService::getMetadata($request, $username),
                'authorized' =>  $isAutorized,
            ]);
        }
    }
}
