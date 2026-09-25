<?php

namespace App\Traits;

use App\Models\Core\Info;
use App\Services\Core\ApiResponse\ApiResponseInterface;
use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    private static function getCallingClassNamespace(): string
    {
        $backtrace = debug_backtrace();
        $caller = $backtrace[2]; // [2] refere-se à classe que chamou o método na trait
        return $caller['class'];
    }

    public static function success(?string $message , $data = null, $dataModule = null,  int $code = ApiResponseInterface::CODE_OK): JsonResponse
    {
        $route = request()->path();
        
        $info = Info::where('route', $route)->first ();

        return response()->json([
            'status' => 'Success',
            'namespace' => self::getCallingClassNamespace(),
            'message' => $message,
            'data' => $data,
            'dataModule' => $dataModule,
            'info' => $info->infos ?? false
        ], $code);
    }

    public static function error(?string $message, array $data = [],  int $code = ApiResponseInterface::CODE_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'namespace' => self::getCallingClassNamespace(),
            'message' => $message,
            'errors' => $data,
        ], $code);
    }
}
