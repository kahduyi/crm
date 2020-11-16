<?php

namespace App\Http\Middleware\Employee;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
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
        if (auth('employee')->check()) {
            return redirect()->route('organize.auth.home.index');
        }

        return $next($request);
    }
}
