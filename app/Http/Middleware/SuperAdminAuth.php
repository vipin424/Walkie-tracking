<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('super_admin')->check()) {
            return redirect()->route('super.login');
        }

        return $next($request);
    }
}
