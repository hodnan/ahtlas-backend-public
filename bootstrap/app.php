<?php

use App\Http\Middleware\ConvertUnauthorizedToNoContent;
use App\Http\Middleware\RoutePermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    /* pode ser necessario para notificações push 
        ver mais em https://laravel.com/docs/11.x/sanctum#spa-configuration 'Authorizing Private Broadcast Channels'
    */
    // ->withBroadcasting(
    //     __DIR__.'/../routes/channels.php',
    //     ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    // )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->append(ConvertUnauthorizedToNoContent::class);
        $middleware->alias([
            'route.permission' => RoutePermission::class,
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
