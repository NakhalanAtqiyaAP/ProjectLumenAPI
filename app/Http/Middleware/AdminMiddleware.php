<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect('/login'); // Redirect jika tidak terautentikasi
        }

        $response = $next($request);

        if ($request->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.'); // Tolak jika bukan admin
        } 

        return $response;
    }
}
