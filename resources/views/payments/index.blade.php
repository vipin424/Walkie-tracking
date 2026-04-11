@extends('layouts.app')
@section('title', 'Payments')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="fw-bold mb-0">💳 Payment Transactions</h4>
    <small class="text-muted">All received payments across orders</small>
  </div>
  <div class="text-end">
    <div class="text-muted small">Total Collected</div>
    <div class="fs-5 fw-bold text-success">₹{{ number_format($totalAmount, 2) }}</div>
  </div>
</div>

{{-- FILTERS --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row gy-2 gx-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold text-secondary">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Order code / Client name...">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold text-secondary">Payment Method</label>
        <select name="method" class="form-select">
          <option value="">All Methods</option>
          @foreach(['cash','gpay','paytm','phonepe','bank_transfer','upi','other'] as $m)
            <option value="{{ $m }}" @selected(request('method') === $m)>{{ ucwords(str_replace('_',' ',$m)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold text-secondary">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold text-secondary">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
      </div>
      @if(request()->hasAny(['search','method','from','to']))
      <div class="col-md-1">
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
      </div>
      @endif
    </form>
  </div>
</div>

{{-- TABLE --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span class="fw-semibold"><i class="bi bi-wallet2 text-primary me-2"></i>Transactions</span>
    <span class="badge bg-light text-dark">{{ $transactions->total() }} records</span>
  </div>
  <div class="card-body p-0">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Order</th>
          <th>Client</th>
          <th>Method</th>
          <th>Transaction ID</th>
          <th>Notes</th>
          <th>Recorded By</th>
          <th class="text-end">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $txn)
        <tr>
          <td class="text-nowrap">
            <div class="fw-medium">{{ $txn->paid_at->format('d M Y') }}</div>
            <small class="text-muted">{{ $txn->paid_at->format('h:i A') }}</small>
          </td>
          <td>
            @if($txn->order)
              <a href="{{ route('orders.show', $txn->order) }}" class="text-decoration-none fw-semibold text-primary">
                {{ $txn->order->order_code }}
              </a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            <div class="fw-medium">{{ $txn->order?->client_name ?? '—' }}</div>
            <small class="text-muted">{{ $txn->order?->client_phone }}</small>
          </td>
          <td>
            @php
              $methodColors = [
                'cash' => 'success', 'gpay' => 'primary', 'paytm' => 'info',
                'phonepe' => 'purple', 'bank_transfer' => 'secondary',
                'upi' => 'warning', 'other' => 'dark',
              ];
              $color = $methodColors[$txn->payment_method] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
              {{ ucwords(str_replace('_', ' ', $txn->payment_method)) }}
            </span>
          </td>
          <td>
            <span class="text-muted small font-monospace">{{ $txn->transaction_id ?? '—' }}</span>
          </td>
          <td>
            <span class="text-muted small">{{ Str::limit($txn->notes, 40) ?? '—' }}</span>
          </td>
          <td>
            <span class="text-muted small">{{ $txn->recorded_by }}</span>
          </td>
          <td class="text-end fw-bold text-success">
            ₹{{ number_format($txn->amount, 2) }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>No transactions found
          </td>
        </tr>
        @endforelse
      </tbody>
      @if($transactions->count())
      <tfoot class="table-light">
        <tr>
          <td colspan="7" class="text-end fw-bold">Page Total</td>
          <td class="text-end fw-bold text-success">₹{{ number_format($transactions->sum('amount'), 2) }}</td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>

@if($transactions->hasPages())
  <div class="mt-4 d-flex justify-content-center">
    {{ $transactions->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection
