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
        $user_access = \DB::table('users as u')
                            ->join('admin_user_types as ut','u.user_type','=','ut.id')
                            ->select('ut.description','ut.category')
                            ->where('u.user_id',$request->user_id)
                            ->first();

        if (Auth::guard($guard)->check()) {
            if ($user_access->category == 'OFFICE' && $user_access->category == 'ALL') {
                return redirect('/dashboard');
            }

            if ($user_access->category == 'PRODUCTION' && $user_access->category == 'ALL') {
                return redirect('/prod/dashboard');
            }
        }

        return $next($request);
    }
}
