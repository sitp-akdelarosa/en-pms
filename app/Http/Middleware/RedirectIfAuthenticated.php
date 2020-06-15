<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (Auth::user()->user_category == 'OFFICE') {
                return redirect('/dashboard');
            } 

            if (Auth::user()->user_category == 'PRODUCTION') {
                return redirect('/prod/dashboard');
            }
        }

        return $next($request);
    }
}
