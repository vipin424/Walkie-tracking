<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('company.settings', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email',
            'phone'           => 'nullable|string',
            'address'         => 'nullable|string',
            'primary_color'   => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Company settings updated.');
    }

    public function plan()
    {
        $company = auth()->user()->company->load('plan');
        return view('company.plan', compact('company'));
    }
}
