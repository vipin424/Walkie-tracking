<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('companies')->get();
        return view('super_admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super_admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'max_orders'    => 'required|integer|min:1',
            'max_invoices'  => 'required|integer|min:1',
            'max_users'     => 'required|integer|min:1',
            'features'      => 'nullable|array',
        ]);

        Plan::create($data);

        return redirect()->route('super.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('super_admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'max_orders'    => 'required|integer|min:1',
            'max_invoices'  => 'required|integer|min:1',
            'max_users'     => 'required|integer|min:1',
            'is_active'     => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('super.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('super.plans.index')->with('success', 'Plan deleted.');
    }
}
