<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class DeletedUser
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
        if (auth()->user()->del_flag == 1) {
            $user = auth()->user();
            auth()->logout();

            $deleted_at = Carbon::parse($user->deleted_at)->format('jS F Y h:i:s A');

            return redirect()->route('login')
                ->withError('Your account was deleted at ' . $deleted_at);
        }

        return $next($request);
    }
}
