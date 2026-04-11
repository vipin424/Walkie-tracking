@extends('super_admin.layout')
@section('title','Plans')

@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0">Subscription Plans</h5>
  <a href="{{ route('super.plans.create') }}" class="btn btn-primary btn-sm">+ New Plan</a>
</div>

<div class="row g-3">
  @forelse($plans as $plan)
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h5 class="fw-bold">{{ $plan->name }}</h5>
          <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="fs-4 fw-bold text-primary mb-3">₹{{ number_format($plan->price,0) }}<small class="fs-6 text-muted">/mo</small></div>
        <ul class="list-unstyled text-muted small">
          <li><i class="bi bi-check-circle text-success me-1"></i>{{ $plan->max_orders }} Orders/month</li>
          <li><i class="bi bi-check-circle text-success me-1"></i>{{ $plan->max_invoices }} Invoices/month</li>
          <li><i class="bi bi-check-circle text-success me-1"></i>{{ $plan->max_users }} Users</li>
        </ul>
        <div class="text-muted small">{{ $plan->companies_count }} companies on this plan</div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('super.plans.edit', $plan) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
        <form method="POST" action="{{ route('super.plans.destroy', $plan) }}" onsubmit="return confirm('Delete plan?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12 text-muted">No plans yet.</div>
  @endforelse
</div>
@endsection
