@extends('layouts.app')
@section('title','Company Settings')

@section('content')
<div class="row">
  <div class="col-md-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white"><strong>Company Profile & Branding</strong></div>
      <div class="card-body">
        <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data">
          @csrf

          <div class="mb-3">
            <label class="form-label">Company Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea>
          </div>

          <hr>
          <h6 class="text-muted">Theme Colors</h6>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Primary Color</label>
              <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $company->primary_color) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Secondary Color</label>
              <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', $company->secondary_color) }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Logo</label>
            @if($company->logo)
              <div class="mb-2"><img src="{{ asset('storage/'.$company->logo) }}" height="50" class="rounded"></div>
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
          </div>

          <button class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white"><strong>Current Plan</strong></div>
      <div class="card-body">
        @if($company->plan)
          <h4 class="fw-bold text-primary">{{ $company->plan->name }}</h4>
          <div class="fs-5 mb-3">₹{{ number_format($company->plan->price,0) }}<small class="text-muted">/month</small></div>
          <ul class="list-unstyled">
            <li><i class="bi bi-check-circle text-success me-2"></i>{{ $company->plan->max_orders }} Orders/month</li>
            <li><i class="bi bi-check-circle text-success me-2"></i>{{ $company->plan->max_invoices }} Invoices/month</li>
            <li><i class="bi bi-check-circle text-success me-2"></i>{{ $company->plan->max_users }} Users</li>
          </ul>
          <div class="text-muted small">
            Expires: {{ $company->subscription_expires_at?->format('d M Y') ?? 'No expiry' }}
          </div>
        @else
          <p class="text-muted">No plan assigned. Contact support to upgrade.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
