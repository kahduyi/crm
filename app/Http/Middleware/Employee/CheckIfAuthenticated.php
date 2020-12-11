<?php

namespace App\Http\Middleware\Employee;

use Closure;
use Illuminate\Http\Request;

class CheckIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth('employee')->check()) {
            return redirect()->route('organization.auth.show.login');
        }
//        dd("auth ta inja".auth('employee')->check());
        return $next($request);
    }
}
