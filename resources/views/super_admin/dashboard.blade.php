@extends('super_admin.layout')
@section('title','Dashboard')

@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center p-3">
      <div class="fs-2 fw-bold text-primary">{{ $stats['total_companies'] }}</div>
      <div class="text-muted">Total Companies</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center p-3">
      <div class="fs-2 fw-bold text-success">{{ $stats['active_companies'] }}</div>
      <div class="text-muted">Active Companies</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center p-3">
      <div class="fs-2 fw-bold text-warning">{{ $stats['total_plans'] }}</div>
      <div class="text-muted">Plans</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center p-3">
      <div class="fs-2 fw-bold text-info">₹{{ number_format($stats['total_revenue'],0) }}</div>
      <div class="text-muted">Total Revenue</div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <strong>Recent Companies</strong>
    <a href="{{ route('super.companies.create') }}" class="btn btn-sm btn-primary">+ New Company</a>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Company</th><th>Plan</th><th>Expires</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($companies as $company)
        <tr>
          <td>{{ $company->name }}</td>
          <td>{{ $company->plan?->name ?? '—' }}</td>
          <td>{{ $company->subscription_expires_at?->format('d M Y') ?? 'No expiry' }}</td>
          <td>
            <span class="badge bg-{{ $company->status === 'active' ? 'success' : 'danger' }}">
              {{ ucfirst($company->status) }}
            </span>
          </td>
          <td>
            <a href="{{ route('super.companies.edit', $company) }}" class="btn btn-xs btn-outline-secondary btn-sm">Edit</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
