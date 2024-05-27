<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
