<?php

namespace App\Http\Middleware\Employee;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;

class VerifyCodeEmployee
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
            return redirect()->route('organization.index');
        }
        $employee = Employee::where('mobile', $request->mobile)->first();
        if (! $employee) {
            return redirect()->route('organization.auth.show.login');
        } elseif ($employee->verified_at) {
            // He has already registered and must log-in to enter the site.
            return redirect()->route('organization.auth.show.login');
        }
        return $next($request);
    }
}
