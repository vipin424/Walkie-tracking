@extends('super_admin.layout')
@section('title','Create Company')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:700px">
  <div class="card-header bg-white"><strong>New Company</strong></div>
  <div class="card-body">
    <form method="POST" action="{{ route('super.companies.store') }}" enctype="multipart/form-data">
      @csrf

      <h6 class="text-muted mb-3">Company Details</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Company Name *</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email *</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Logo</label>
          <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        <div class="col-12">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Primary Color</label>
          <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color','#0d6efd') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Secondary Color</label>
          <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color','#6c757d') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Plan</label>
          <select name="plan_id" class="form-select">
            <option value="">— No Plan —</option>
            @foreach($plans as $plan)
              <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->name }} (₹{{ $plan->price }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Subscription Expires At</label>
          <input type="date" name="subscription_expires_at" class="form-control" value="{{ old('subscription_expires_at') }}">
        </div>
      </div>

      <hr>
      <h6 class="text-muted mb-3">Company Admin Account</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Admin Name *</label>
          <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Admin Email *</label>
          <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Admin Password *</label>
          <input type="password" name="admin_password" class="form-control" required minlength="8">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary">Create Company</button>
        <a href="{{ route('super.companies.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
