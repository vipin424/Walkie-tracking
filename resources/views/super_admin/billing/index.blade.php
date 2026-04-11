@extends('super_admin.layout')
@section('title','Billing')

@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0">Subscription Invoices</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">+ New Invoice</button>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Invoice #</th><th>Company</th><th>Plan</th><th>Amount</th><th>Period</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($invoices as $inv)
        <tr>
          <td>{{ $inv->invoice_number }}</td>
          <td>{{ $inv->company->name }}</td>
          <td>{{ $inv->plan->name }}</td>
          <td>₹{{ number_format($inv->amount,0) }}</td>
          <td>{{ $inv->period_from->format('d M Y') }} – {{ $inv->period_to->format('d M Y') }}</td>
          <td><span class="badge bg-{{ $inv->status === 'paid' ? 'success' : ($inv->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($inv->status) }}</span></td>
          <td>
            @if($inv->status !== 'paid')
            <form method="POST" action="{{ route('super.billing.mark-paid', $inv) }}">
              @csrf
              <button class="btn btn-sm btn-success">Mark Paid</button>
            </form>
            @else
            <span class="text-muted small">{{ $inv->paid_at?->format('d M Y') }}</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No invoices yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $invoices->links() }}</div>

{{-- Create Invoice Modal --}}
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">New Subscription Invoice</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="{{ route('super.billing.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Company</label>
            <select name="company_id" class="form-select" required>
              @foreach(\App\Models\Company::all() as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Plan</label>
            <select name="plan_id" class="form-select" required>
              @foreach(\App\Models\Plan::where('is_active',true)->get() as $p)
                <option value="{{ $p->id }}">{{ $p->name }} — ₹{{ $p->price }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Amount (₹)</label>
            <input type="number" name="amount" class="form-control" required min="0">
          </div>
          <div class="row g-2">
            <div class="col">
              <label class="form-label">Period From</label>
              <input type="date" name="period_from" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Period To</label>
              <input type="date" name="period_to" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Create Invoice</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
