<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProductionMiddleware
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

        if (Auth::user() && (Auth::user()->user_category == 'PRODUCTION' || Auth::user()->user_category == 'ALL')) {
            return $next($request);
        }
        return redirect('/');
    }
}
