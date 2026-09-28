@extends('layouts.app')
@section('title', $damageReport->report_code)

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">{{ $damageReport->report_code }}</h4>
      <p class="text-muted mb-0">Damage / Replacement Invoice Details</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @if($damageReport->pdf_path)
      <a href="{{ asset($damageReport->pdf_path) }}" target="_blank" class="btn btn-outline-primary btn-md shadow-sm">
        <i class="bi bi-file-pdf me-2"></i>View Invoice PDF
      </a>
      @endif
      <button type="button" class="btn btn-success btn-md shadow-sm" onclick="openSendModal()">
        <i class="bi bi-send me-2"></i>Send to Client
      </button>
      @if($damageReport->status !== 'paid')
      <button type="button" class="btn btn-info btn-md shadow-sm" onclick="markPaid()">
        <i class="bi bi-check-circle me-2"></i>Mark as Paid
      </button>
      @endif
      <a href="{{ route('damage-reports.edit', $damageReport) }}" class="btn btn-warning btn-md shadow-sm">
        <i class="bi bi-pencil me-2"></i>Edit
      </a>
    </div>
  </div>

  <div class="row">

    {{-- ═══ CLIENT DETAILS ═══ --}}
    <div class="col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-person-circle me-2 text-warning"></i>Client Details
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label class="text-muted small">Name</label>
            <p class="fw-semibold mb-0">{{ $damageReport->client_name }}</p>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Phone</label>
            <p class="fw-semibold mb-0">{{ $damageReport->client_phone }}</p>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Email</label>
            <p class="fw-semibold mb-0">{{ $damageReport->client_email ?: 'N/A' }}</p>
          </div>
          @if($damageReport->subscription)
          <div class="mb-0">
            <label class="text-muted small">Linked Subscription</label>
            <p class="fw-semibold mb-0">
              <span class="badge bg-light text-dark border px-3 py-2">
                📋 {{ $damageReport->subscription->subscription_code }}
              </span>
            </p>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ═══ INVOICE DETAILS ═══ --}}
    <div class="col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-receipt me-2 text-warning"></i>Invoice Details
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label class="text-muted small">Report Code</label>
            <p class="fw-semibold mb-0">{{ $damageReport->report_code }}</p>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Date of Damage</label>
            <p class="fw-semibold mb-0">{{ $damageReport->damage_date->format('d M Y') }}</p>
          </div>
          <div class="mb-3">
            <label class="text-muted small">Replacement Amount</label>
            <p class="fw-semibold mb-0 fs-5 text-warning">₹{{ number_format($damageReport->replacement_amount, 2) }}</p>
          </div>
          <div class="mb-0">
            <label class="text-muted small">Status</label>
            <p class="mb-0">
              @php
                $statusColors = ['pending' => 'warning', 'sent' => 'info', 'paid' => 'success'];
              @endphp
              <span class="badge bg-{{ $statusColors[$damageReport->status] }} px-3 py-2">
                {{ ucfirst($damageReport->status) }}
              </span>
              @if($damageReport->sent_at)
                <small class="text-muted ms-2">Sent: {{ $damageReport->sent_at->format('d M Y') }}</small>
              @endif
              @if($damageReport->paid_at)
                <small class="text-muted ms-2">Paid: {{ $damageReport->paid_at->format('d M Y') }}</small>
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ═══ EQUIPMENT DETAILS ═══ --}}
  @if($damageReport->walkie_units && count($damageReport->walkie_units) > 0)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-broadcast me-2 text-warning"></i>Equipment Details
      </h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Walkie Model</th>
              <th class="px-4 py-3 text-muted fw-semibold">Serial Number</th>
              <th class="px-4 py-3 text-muted fw-semibold">Unit ID / Asset No.</th>
            </tr>
          </thead>
          <tbody>
            @foreach($damageReport->walkie_units as $unit)
            <tr>
              <td class="px-4 py-3 fw-medium">{{ $unit['model'] ?: '—' }}</td>
              <td class="px-4 py-3">{{ $unit['serial'] ?: '—' }}</td>
              <td class="px-4 py-3">{{ $unit['unit_id'] ?: '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- ═══ DAMAGE DESCRIPTION ═══ --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-tools me-2 text-warning"></i>Damage Description
      </h5>
    </div>
    <div class="card-body p-4">
      <p class="mb-0" style="white-space: pre-line;">{{ $damageReport->damage_description }}</p>
    </div>
  </div>

  {{-- ═══ DAMAGE PHOTOS ═══ --}}
  @if($damageReport->damage_photos && count($damageReport->damage_photos) > 0)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-camera me-2 text-warning"></i>Damage Photographs
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="row g-3">
        @foreach($damageReport->damage_photos as $photo)
        <div class="col-6 col-md-3 col-lg-2">
          <a href="{{ asset('storage/'.$photo) }}" target="_blank">
            <img src="{{ asset('storage/'.$photo) }}" class="img-fluid rounded shadow-sm"
                 style="width:100%; height:130px; object-fit:cover; border:2px solid #dee2e6; transition: transform .2s;"
                 onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
          </a>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- ═══ NOTES ═══ --}}
  @if($damageReport->notes)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Internal Remarks
      </h5>
    </div>
    <div class="card-body p-4">
      <p class="mb-0 text-muted">{{ $damageReport->notes }}</p>
    </div>
  </div>
  @endif

</div>

{{-- ══════════════════════════════════════════════════════
     SEND INVOICE MODAL  (matches subscriptions/show.blade pattern)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="sendInvoiceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5b4dff 0%, #004d40 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-send me-2"></i>Send Damage Invoice
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('damage-reports.send', $damageReport) }}">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-info border-0 mb-4">
            <strong>{{ $damageReport->report_code }}</strong><br>
            <small>Amount: ₹{{ number_format($damageReport->replacement_amount, 2) }}</small>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold mb-3">
              <i class="bi bi-send me-2"></i>Select Channel
            </label>
            <div class="d-grid gap-2">
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="mEmail" value="email" checked>
                <label class="form-check-label w-100" for="mEmail" style="cursor: pointer;">
                  <i class="bi bi-envelope text-primary me-2"></i>
                  <strong>Email</strong>
                  <small class="d-block text-muted">Send professional email with PDF download link</small>
                </label>
              </div>
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="mWhatsApp" value="whatsapp">
                <label class="form-check-label w-100" for="mWhatsApp" style="cursor: pointer;">
                  <i class="bi bi-whatsapp text-success me-2"></i>
                  <strong>WhatsApp</strong>
                  <small class="d-block text-muted">Opens WhatsApp Web with invoice message</small>
                </label>
              </div>
            </div>
          </div>

          <div id="emailFields">
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope me-2"></i>To Email <span class="text-danger">*</span>
              </label>
              <input type="email" class="form-control" id="toEmail" name="to_email"
                     value="{{ $damageReport->client_email }}" required>
              <small class="text-muted">Primary recipient email address</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope-plus me-2"></i>CC Emails (Optional)
              </label>
              <input type="text" class="form-control" name="cc_emails"
                     placeholder="email1@example.com, email2@example.com">
              <small class="text-muted">Separate multiple emails with commas</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-chat-text me-2"></i>Custom Message (Optional)
              </label>
              <textarea class="form-control" name="message" rows="3"
                        placeholder="Add a custom message to the email..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i>Cancel
          </button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-send-fill me-2"></i>Send Invoice
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .form-check:hover { background-color: #f8f9fa; }
  .modal-content { border-radius: 15px; }
  .modal-header { border-top-left-radius: 15px; border-top-right-radius: 15px; }
</style>

<script>
function openSendModal() {
  document.getElementById('mEmail').checked = true;
  document.getElementById('emailFields').style.display = 'block';
  document.getElementById('toEmail').required = true;
  new bootstrap.Modal(document.getElementById('sendInvoiceModal')).show();
}

function markPaid() {
  if (confirm('Mark invoice {{ $damageReport->report_code }} as PAID?\n\nAmount: ₹{{ number_format($damageReport->replacement_amount, 2) }}\n\nThis will create a payment transaction record.')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('damage-reports.mark-paid', $damageReport) }}';
    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
  }
}

document.querySelectorAll('input[name="method"]').forEach(radio => {
  radio.addEventListener('change', function () {
    const show = this.value === 'email';
    document.getElementById('emailFields').style.display = show ? 'block' : 'none';
    document.getElementById('toEmail').required = show;
  });
});
</script>
@endsection
