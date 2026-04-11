<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\SubscriptionInvoice;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies'  => Company::count(),
            'active_companies' => Company::where('status', 'active')->count(),
            'total_revenue'    => SubscriptionInvoice::where('status', 'paid')->sum('amount'),
            'total_plans'      => Plan::count(),
        ];

        $companies = Company::with('plan')->latest()->take(10)->get();

        return view('super_admin.dashboard', compact('stats', 'companies'));
    }
}
