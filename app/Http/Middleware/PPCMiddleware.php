<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PPCMiddleware
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
        $user_access = \DB::table('users as u')
                            ->join('admin_user_types as ut','u.user_type','=','ut.id')
                            ->select('ut.description','ut.category')
                            ->where('u.id',Auth::user()->id)
                            ->first();

        if (Auth::user() && ($user_access->category == 'OFFICE' || $user_access->category == 'ALL')) {
            return $next($request);
        }
        return redirect('/');
    }
}
