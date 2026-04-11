<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCompanySubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->company_id) {
            abort(403, 'No company assigned to your account.');
        }

        $company = $user->company;

        if (!$company || $company->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your company account is inactive or suspended.']);
        }

        if (!$company->isSubscriptionActive()) {
            return redirect()->route('subscription.expired');
        }

        view()->share('currentCompany', $company);

        return $next($request);
    }
}
