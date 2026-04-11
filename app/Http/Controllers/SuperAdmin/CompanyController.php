<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with('plan')->latest()->paginate(20);
        return view('super_admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('super_admin.companies.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email',
            'phone'                   => 'nullable|string',
            'address'                 => 'nullable|string',
            'primary_color'           => 'nullable|string|max:7',
            'secondary_color'         => 'nullable|string|max:7',
            'plan_id'                 => 'nullable|exists:plans,id',
            'subscription_expires_at' => 'nullable|date',
            'logo'                    => 'nullable|image|max:2048',
            'admin_name'              => 'required|string',
            'admin_email'             => 'required|email|unique:users,email',
            'admin_password'          => 'required|min:8',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
        }

        $company = Company::create([
            'name'                    => $data['name'],
            'slug'                    => Str::slug($data['name']) . '-' . Str::random(4),
            'email'                   => $data['email'],
            'phone'                   => $data['phone'] ?? null,
            'address'                 => $data['address'] ?? null,
            'primary_color'           => $data['primary_color'] ?? '#0d6efd',
            'secondary_color'         => $data['secondary_color'] ?? '#6c757d',
            'plan_id'                 => $data['plan_id'] ?? null,
            'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            'logo'                    => $logoPath,
            'status'                  => 'active',
        ]);

        User::create([
            'name'       => $data['admin_name'],
            'email'      => $data['admin_email'],
            'password'   => Hash::make($data['admin_password']),
            'company_id' => $company->id,
            'role'       => 'company_admin',
        ]);

        return redirect()->route('super.companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('super_admin.companies.edit', compact('company', 'plans'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'nullable|email',
            'phone'                   => 'nullable|string',
            'address'                 => 'nullable|string',
            'primary_color'           => 'nullable|string|max:7',
            'secondary_color'         => 'nullable|string|max:7',
            'plan_id'                 => 'nullable|exists:plans,id',
            'subscription_expires_at' => 'nullable|date',
            'status'                  => 'required|in:active,inactive,suspended',
            'logo'                    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $company->update($data);

        return redirect()->route('super.companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('super.companies.index')->with('success', 'Company deleted.');
    }

    public function toggleStatus(Company $company)
    {
        $company->update(['status' => $company->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Company status updated.');
    }
}
