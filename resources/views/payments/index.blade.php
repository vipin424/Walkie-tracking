@extends('layouts.app')
@section('title', 'Payments')
@section('content')
<style>
  .mt-5 {
    margin-top: 2.5rem !important;
}
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="fw-bold mb-0">💳 Payments</h4>
    <small class="text-muted">Manage and track all payment records</small>
  </div>
</div>

{{-- FILTER CARD --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="get" class="row gy-2 gx-3 align-items-center">
      <div class="col-md-3">
        <label class="form-label fw-semibold text-secondary">Payment Status</label>
        <select name="status" class="form-select shadow-sm">
          <option value="">All Status</option>
          @foreach(['Paid','Unpaid','Advance Received'] as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold text-secondary">Client Name</label>
        <input type="text" name="client" value="{{ request('client') }}" class="form-control shadow-sm" placeholder="Search by client...">
      </div>
      <div class="col-md-2 mt-5">
        <button class="btn btn-outline-warning btn-md w-100">
          <i class="bi bi-funnel me-2"></i> Apply Filter
        </button>
      </div>
    </form>
  </div>
</div>

{{-- PAYMENTS TABLE --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
    <div><i class="bi bi-wallet2 text-primary me-2"></i> All Payments</div>
    <span class="badge bg-light text-dark">{{ $payments->total() }} Total</span>
  </div>

  <div class="card-body p-0">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Dispatch Code</th>
          <th>Client</th>
          <th>Status</th>
          <th class="text-end">Advance</th>
          <th class="text-end">Total Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $p)
        <tr>
          <td>
            <a href="{{ route('dispatches.show',$p->dispatch) }}" class="text-decoration-none fw-semibold text-primary">
              <i class="bi bi-file-earmark-text me-1"></i> {{ $p->dispatch->code }}
            </a>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                <i class="bi bi-person-fill"></i>
              </div>
              <span class="fw-medium">{{ $p->dispatch->client->name }}</span>
            </div>
          </td>
          <td>
            @php
              $statusClass = match($p->payment_status) {
                'Paid' => 'bg-success-subtle text-success',
                'Unpaid' => 'bg-danger-subtle text-danger',
                'Advance Received' => 'bg-warning-subtle text-warning',
                default => 'bg-secondary-subtle text-secondary'
              };
            @endphp
            <span class="badge rounded-pill {{ $statusClass }}">
              {{ $p->payment_status }}
            </span>
          </td>
          <td class="text-end text-muted">{{ number_format($p->advance_amount,2) }}</td>
          <td class="text-end fw-semibold">{{ number_format($p->total_amount,2) }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">No payments found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- PAGINATION --}}
@if($payments->hasPages())
  <div class="mt-4 d-flex justify-content-center">
    {{ $payments->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection
