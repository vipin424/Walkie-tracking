@extends('layouts.app')
@section('title', 'Quotation: ' . $monthlyQuotation->code)
@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <a href="{{ route('monthly-quotations.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-2"></i>Back to Quotations
      </a>
      <h4 class="mb-1 fw-bold">{{ $monthlyQuotation->code }}</h4>
      <p class="text-muted mb-0">Monthly Rate Quotation</p>
    </div>
    <div>
      @php
        $colors = ['draft' => 'secondary', 'sent' => 'success'];
        $color  = $colors[$monthlyQuotation->status] ?? 'secondary';
      @endphp
      <span class="badge bg-{{ $color }} px-4 py-2 fs-6">{{ ucfirst($monthlyQuotation->status) }}</span>
    </div>
  </div>

  {{-- Success / Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
  @endif

  {{-- Action Buttons --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('monthly-quotations.edit', $monthlyQuotation) }}" class="btn btn-warning">
          <i class="bi bi-pencil-square me-2"></i>Edit Quotation
        </a>
        <form action="{{ route('monthly-quotations.generate-pdf', $monthlyQuotation) }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
          </button>
        </form>
        @if($monthlyQuotation->pdf_path)
          <a href="{{ asset($monthlyQuotation->pdf_path) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-eye me-2"></i>View PDF
          </a>
        @endif
        <form action="{{ route('monthly-quotations.destroy', $monthlyQuotation) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this quotation?')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger">
            <i class="bi bi-trash me-2"></i>Delete
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Client Info --}}
    <div class="col-lg-5 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-person-circle me-2 text-warning"></i>Client Information
          </h5>
        </div>
        <div class="card-body p-4">
          @if($monthlyQuotation->client_company)
          <div class="mb-2">
            <small class="text-muted fw-semibold">Company</small>
            <p class="mb-0 fw-semibold fs-6">{{ $monthlyQuotation->client_company }}</p>
          </div>
          @endif
          <div class="mb-2">
            <small class="text-muted fw-semibold">Contact Person</small>
            <p class="mb-0">{{ $monthlyQuotation->client_name }}</p>
          </div>
          @if($monthlyQuotation->client_phone)
          <div class="mb-2">
            <small class="text-muted fw-semibold">Phone</small>
            <p class="mb-0"><i class="bi bi-telephone-fill text-success me-1"></i>{{ $monthlyQuotation->client_phone }}</p>
          </div>
          @endif
          @if($monthlyQuotation->client_email)
          <div class="mb-2">
            <small class="text-muted fw-semibold">Email</small>
            <p class="mb-0"><i class="bi bi-envelope-fill text-primary me-1"></i>{{ $monthlyQuotation->client_email }}</p>
          </div>
          @endif
          @if($monthlyQuotation->client_address)
          <div class="mb-2">
            <small class="text-muted fw-semibold">Location / Address</small>
            <p class="mb-0">{{ $monthlyQuotation->client_address }}</p>
          </div>
          @endif
          @if($monthlyQuotation->client_gstin)
          <div class="mb-0">
            <small class="text-muted fw-semibold">GSTIN</small>
            <p class="mb-0 font-monospace">{{ $monthlyQuotation->client_gstin }}</p>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Quotation Summary --}}
    <div class="col-lg-7 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-receipt me-2 text-warning"></i>Quotation Summary
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted fw-semibold d-block mb-1">Quotation Code</small>
                <span class="fw-bold">{{ $monthlyQuotation->code }}</span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted fw-semibold d-block mb-1">Date</small>
                <span>{{ $monthlyQuotation->created_at->format('d M Y') }}</span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted fw-semibold d-block mb-1">Valid Till</small>
                <span class="text-danger fw-semibold">{{ $monthlyQuotation->valid_till }}</span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted fw-semibold d-block mb-1">Payment Terms</small>
                <span>{{ $monthlyQuotation->payment_terms ?: '—' }}</span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted fw-semibold d-block mb-1">Delivery Timeline</small>
                <span>{{ $monthlyQuotation->delivery_timeline ?: '—' }}</span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-warning bg-opacity-10 rounded border border-warning">
                <small class="text-muted fw-semibold d-block mb-1">Grand Total (Monthly)</small>
                <h5 class="mb-0 fw-bold text-warning">₹{{ number_format($monthlyQuotation->total_amount, 2) }}</h5>
              </div>
            </div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-4">
              <div class="p-2 bg-light rounded text-center">
                <small class="text-muted d-block">Subtotal</small>
                <span class="fw-semibold">₹{{ number_format($monthlyQuotation->subtotal, 2) }}</span>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 bg-light rounded text-center">
                <small class="text-muted d-block">GST ({{ $monthlyQuotation->gst_percent }}%)</small>
                <span class="fw-semibold text-info">₹{{ number_format($monthlyQuotation->gst_amount, 2) }}</span>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 bg-warning bg-opacity-10 rounded text-center border border-warning">
                <small class="text-muted d-block">Total</small>
                <span class="fw-bold text-warning">₹{{ number_format($monthlyQuotation->total_amount, 2) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Items Table --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-list-ul me-2 text-warning"></i>Items / Scope of Supply
      </h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">#</th>
              <th class="px-4 py-3 text-muted fw-semibold">Description</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">UOM</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Quantity</th>
              <th class="px-4 py-3 text-muted fw-semibold text-end">Rate / Month</th>
              <th class="px-4 py-3 text-muted fw-semibold text-end">Amount / Month</th>
            </tr>
          </thead>
          <tbody>
            @foreach($monthlyQuotation->items as $i => $item)
            <tr>
              <td class="px-4 py-3">
                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $i + 1 }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="fw-medium">{{ $item->item_name }}</div>
                @if($item->description)
                  <small class="text-muted">{{ $item->description }}</small>
                @endif
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-info bg-opacity-10 text-info">{{ $item->uom }}</span>
              </td>
              <td class="px-4 py-3 text-center fw-semibold">{{ $item->quantity }}</td>
              <td class="px-4 py-3 text-end">₹{{ number_format($item->rate, 2) }}</td>
              <td class="px-4 py-3 text-end fw-semibold">₹{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="bg-light">
            <tr>
              <td colspan="5" class="px-4 py-3 text-end text-muted fw-semibold">Subtotal:</td>
              <td class="px-4 py-3 text-end fw-semibold">₹{{ number_format($monthlyQuotation->subtotal, 2) }}</td>
            </tr>
            @if($monthlyQuotation->gst_amount > 0)
            <tr>
              <td colspan="5" class="px-4 py-3 text-end text-muted fw-semibold">
                GST ({{ $monthlyQuotation->gst_percent }}%):
              </td>
              <td class="px-4 py-3 text-end fw-semibold text-info">₹{{ number_format($monthlyQuotation->gst_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
              <td colspan="5" class="px-4 py-3 text-end fw-bold">Grand Total (Per Month):</td>
              <td class="px-4 py-3 text-end">
                <span class="fs-5 fw-bold text-warning">₹{{ number_format($monthlyQuotation->total_amount, 2) }}</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Notes --}}
  @if($monthlyQuotation->notes)
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-sticky me-2 text-warning"></i>Notes / Terms &amp; Conditions
      </h5>
    </div>
    <div class="card-body p-4">
      <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $monthlyQuotation->notes }}</p>
    </div>
  </div>
  @endif

</div>

<style>
  .table-hover tbody tr:hover { background-color: rgba(13,110,253,0.03); }
  .card { transition: all 0.2s ease; }
</style>
@endsection
