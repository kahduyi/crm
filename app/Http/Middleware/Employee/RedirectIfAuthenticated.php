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
        dd("dd(\"ma da RedirectIfAuthenticated hastim.\");");
        if (auth('employee')->check()) {
            return redirect()->route('organization.index');
        }

        return $next($request);
    }
}
