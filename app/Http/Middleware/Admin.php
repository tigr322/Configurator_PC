<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class Admin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->admin == 1) {
            return $next($request);
        }

        abort(403, 'Доступ запрещён');
    }
}