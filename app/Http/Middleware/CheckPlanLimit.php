<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPlanLimit
{
    public function handle(Request $request, Closure $next, string $feature = 'orders')
    {
        $company = auth()->user()?->company;

        if ($company && $company->plan) {
            $limit = match($feature) {
                'orders'   => $company->plan->max_orders,
                'invoices' => $company->plan->max_invoices,
                'users'    => $company->plan->max_users,
                default    => PHP_INT_MAX,
            };

            $used = match($feature) {
                'orders'   => $company->orders()->whereMonth('created_at', now()->month)->count(),
                'invoices' => $company->orders()->whereMonth('created_at', now()->month)->count(),
                'users'    => $company->users()->count(),
                default    => 0,
            };

            if ($used >= $limit) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Plan limit reached. Please upgrade.'], 403);
                }
                return redirect()->back()->with('plan_limit_reached', "You have reached your monthly {$feature} limit ({$limit}). Please upgrade your plan.");
            }
        }

        return $next($request);
    }
}
