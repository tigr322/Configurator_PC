<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends HttpKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('prices:update')->everyFiveMinutes();
    }
    protected $middleware = [
       
        \Illuminate\Http\Middleware\HandleCors::class,
        
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
       
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\HandleInertiaRequests::class,

    ];

    protected $middlewareGroups = [
        'web' => [
           
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
           
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'customHeaders' => \App\Http\Middleware\CustomHeadersMiddleware::class,
        'admin' => \App\Http\Middleware\Admin::class, // вот сюда ты добавляешь admin
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
