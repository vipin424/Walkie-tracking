@extends('super_admin.layout')
@section('title','Edit Company')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:700px">
  <div class="card-header bg-white"><strong>Edit: {{ $company->name }}</strong></div>
  <div class="card-body">
    <form method="POST" action="{{ route('super.companies.update', $company) }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Company Name *</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            @foreach(['active','inactive','suspended'] as $s)
              <option value="{{ $s }}" @selected($company->status === $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Primary Color</label>
          <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $company->primary_color) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Secondary Color</label>
          <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $company->secondary_color) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Plan</label>
          <select name="plan_id" class="form-select">
            <option value="">— No Plan —</option>
            @foreach($plans as $plan)
              <option value="{{ $plan->id }}" @selected($company->plan_id == $plan->id)>{{ $plan->name }} (₹{{ $plan->price }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Subscription Expires At</label>
          <input type="date" name="subscription_expires_at" class="form-control" value="{{ old('subscription_expires_at', $company->subscription_expires_at?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Logo</label>
          @if($company->logo)
            <div class="mb-1"><img src="{{ asset('storage/'.$company->logo) }}" height="40"></div>
          @endif
          <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('super.companies.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
