<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Core\ApiResponse\ApiResponseInterface;


class ConvertUnauthorizedToNoContent
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $response = $next($request);
        
        if ($response->getStatusCode() == 401) {
            $message = ['203' => ['message' => 'Acesso não autorizado']];
            
            return response()->json([
                'status' => 'Error',
                'message' => 'Unauthenticated',
                'errors' =>  $message ,
            ], ApiResponseInterface::CODE_UNAUTHORIZED);
        }

      

        return $response;
    }   
}
