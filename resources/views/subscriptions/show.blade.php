@extends('layouts.app')
@section('title', 'Subscription Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">{{ $subscription->subscription_code }}</h4>
            <p class="text-muted mb-0">Subscription Details & Invoice History</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('subscriptions.generate-invoice', $subscription) }}" class="btn btn-success btn-md shadow-sm">
                <i class="bi bi-file-earmark-plus me-2"></i>Generate Invoice
            </a>
            <button type="button" class="btn btn-primary btn-md shadow-sm" onclick="openAgreementModal()">
                <i class="bi bi-file-earmark-lock me-2"></i>
                {{ $subscription->agreement ? 'Regenerate Agreement' : 'Generate Agreement' }}
            </button>
            <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-warning btn-md shadow-sm">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Client Details -->
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
                        <p class="fw-semibold mb-0">{{ $subscription->client_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Phone</label>
                        <p class="fw-semibold mb-0">{{ $subscription->client_phone }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Email</label>
                        <p class="fw-semibold mb-0">{{ $subscription->client_email ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar-check me-2 text-warning"></i>Billing Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small">Billing Day</label>
                        <p class="fw-semibold mb-0">{{ $subscription->billing_day_of_month }} of every month</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Start Date</label>
                        <p class="fw-semibold mb-0">{{ $subscription->billing_start_date->format('d M Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Monthly Amount</label>
                        <p class="fw-semibold mb-0 fs-5 text-warning">₹{{ number_format($subscription->monthly_amount, 2) }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'paused' ? 'warning' : 'danger') }} px-3 py-2">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-box-seam me-2 text-warning"></i>Subscription Items
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold">Item Name</th>
                            <th class="px-4 py-3 text-muted fw-semibold">Type</th>
                            <th class="px-4 py-3 text-muted fw-semibold">Description</th>
                            <th class="px-4 py-3 text-muted fw-semibold text-center">Quantity</th>
                            <th class="px-4 py-3 text-muted fw-semibold text-end">Rate/Month</th>
                            <th class="px-4 py-3 text-muted fw-semibold text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscription->items_json as $item)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $item['name'] }}</td>
                            <td class="px-4 py-3 text-muted">{{ $item['type'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $item['description'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $item['quantity'] }}</td>
                            <td class="px-4 py-3 text-end">₹{{ number_format($item['rate'], 2) }}</td>
                            <td class="px-4 py-3 text-end fw-semibold">₹{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-end fw-bold">Total Monthly Amount:</td>
                            <td class="px-4 py-3 text-end fw-bold fs-5 text-warning">₹{{ number_format($subscription->monthly_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($subscription->notes)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Terms & Conditions
            </h5>
        </div>
        <div class="card-body p-4">
            <p class="mb-0 text-muted">{{ $subscription->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Agreement Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-file-earmark-lock me-2 text-warning"></i>Monthly Rental Agreement
            </h5>
            @if($subscription->agreement)
                <span class="badge px-3 py-2 {{ $subscription->agreement->status === 'signed' ? 'bg-success' : 'bg-warning text-dark' }} fs-6">
                    {{ $subscription->agreement->status === 'signed' ? '✅ Signed' : '⏳ Pending Signature' }}
                </span>
            @else
                <span class="badge bg-secondary px-3 py-2 fs-6">Not Generated</span>
            @endif
        </div>
        <div class="card-body p-4">
            @if($subscription->agreement)
                @php $agr = $subscription->agreement; @endphp
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="text-muted small">Agreement Code</div>
                        <div class="fw-semibold">{{ $agr->agreement_code }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Agreement Period</div>
                        <div class="fw-semibold">
                            {{ $agr->agreement_start_date->format('d M Y') }}
                            <i class="bi bi-arrow-right mx-1"></i>
                            {{ $agr->agreement_end_date->format('d M Y') }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Generated / Sent</div>
                        <div class="fw-semibold">{{ $agr->created_at->format('d M Y') }}</div>
                    </div>
                    @if($agr->status === 'signed')
                    <div class="col-md-3">
                        <div class="text-muted small">Signed On</div>
                        <div class="fw-semibold text-success">{{ $agr->signed_at->format('d M Y, h:i A') }}</div>
                    </div>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-2">
                    {{-- Send via Email --}}
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="openSendAgreementEmailModal()">
                        <i class="bi bi-envelope me-1"></i> Send via Email
                    </button>

                    {{-- Send via WhatsApp --}}
                    <a href="{{ route('subscriptions.agreement.send-whatsapp', $subscription) }}"
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i> Send via WhatsApp
                    </a>

                    {{-- View / Download signed PDF --}}
                    @if($agr->signed_pdf)
                    <a href="{{ asset('storage/' . $agr->signed_pdf) }}" target="_blank"
                       class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-file-pdf me-1"></i> View Signed PDF
                    </a>
                    @endif

                    {{-- Copy Link --}}
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="copyLink('{{ $agr->signed_url }}')">
                        <i class="bi bi-link-45deg me-1"></i> Copy Sign Link
                    </button>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-file-earmark-lock fs-1 d-block mb-3 opacity-25"></i>
                    <p class="mb-3">No agreement generated yet for this subscription.</p>
                    <button type="button" class="btn btn-primary" onclick="openAgreementModal()">
                        <i class="bi bi-plus-circle me-2"></i>Generate Agreement Now
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Generated Invoices -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-file-earmark-text me-2 text-warning"></i>Generated Invoices
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold">Invoice Code</th>
                            <th class="px-4 py-3 text-muted fw-semibold">Billing Period</th>
                            <th class="px-4 py-3 text-muted fw-semibold">Amount</th>
                            <th class="px-4 py-3 text-muted fw-semibold">Status</th>
                            <th class="px-4 py-3 text-muted fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscription->invoices()->orderByDesc('id')->get() as $invoice)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $invoice->invoice_code }}</td>
                            <td class="px-4 py-3">
                                <div class="fw-medium">{{ $invoice->billing_period_from->format('d M Y') }} → {{ $invoice->billing_period_to->format('d M Y') }}</div>
                                <small class="text-muted">Generated: {{ $invoice->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="px-4 py-3 fw-semibold">₹{{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'sent' ? 'info' : 'warning') }} px-3 py-2">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    @if($invoice->pdf_path)
                                    <a href="{{ asset($invoice->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="openSendModal({{ $invoice->id }}, '{{ $invoice->invoice_code }}', '{{ $invoice->subscription->client_email }}')" 
                                            title="Send Invoice">
                                        <i class="bi bi-send"></i>
                                    </button>
                                    @if($invoice->status != 'paid')
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="openReminderModal({{ $invoice->id }}, '{{ $invoice->invoice_code }}', '{{ $invoice->subscription->client_email }}', {{ $invoice->amount }})" 
                                            title="Send Payment Reminder">
                                        <i class="bi bi-bell-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="markAsPaid({{ $invoice->id }}, '{{ $invoice->invoice_code }}', {{ $invoice->amount }})" 
                                            title="Mark as Paid">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteInvoice({{ $invoice->id }}, '{{ $invoice->invoice_code }}')" 
                                            title="Delete Invoice">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No invoices generated yet</p>
                                <small>Click "Generate Invoice" button above to create first invoice</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Generate Agreement Modal -->
<div class="modal fade" id="generateAgreementModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-file-earmark-lock me-2"></i>
          {{ $subscription->agreement ? 'Regenerate Agreement' : 'Generate Agreement' }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('subscriptions.agreement.generate', $subscription) }}">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-info border-0 mb-4" style="background: #e3f2fd; border-left: 4px solid #1565c0 !important;">
            <i class="bi bi-info-circle me-2"></i>
            <strong>{{ $subscription->subscription_code }}</strong> &mdash; {{ $subscription->client_name }}<br>
            <small class="text-muted">Monthly Amount: ₹{{ number_format($subscription->monthly_amount, 2) }}</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="bi bi-calendar-event me-2"></i>Agreement Start Date <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" name="agreement_start_date"
                   value="{{ $subscription->agreement ? $subscription->agreement->agreement_start_date->format('Y-m-d') : $subscription->billing_start_date->format('Y-m-d') }}"
                   required>
            <small class="text-muted">Default: Subscription billing start date</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="bi bi-calendar-check me-2"></i>Agreement End Date <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" name="agreement_end_date"
                   value="{{ $subscription->agreement ? $subscription->agreement->agreement_end_date->format('Y-m-d') : $subscription->billing_start_date->addYear()->format('Y-m-d') }}"
                   required>
            <small class="text-muted">Default: 1 year from start date (change as needed)</small>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="bi bi-shield-lock me-2"></i>Security Deposit (Optional)
            </label>
            <div class="input-group">
              <span class="input-group-text">₹</span>
              <input type="number" step="0.01" class="form-control" name="security_deposit"
                     placeholder="0.00" value="{{ $subscription->agreement ? $subscription->agreement->security_deposit : '' }}">
            </div>
            <small class="text-muted">Refundable security deposit amount</small>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-file-earmark-lock me-1"></i>
            {{ $subscription->agreement ? 'Regenerate' : 'Generate' }} Agreement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Send Agreement Email Modal -->
<div class="modal fade" id="sendAgreementEmailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5b4dff 0%, #004d40 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-envelope me-2"></i>Send Agreement Link via Email
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('subscriptions.agreement.send-email', $subscription) }}">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-success border-0 mb-4" style="background: #e8f5e9; border-left: 4px solid #004d40 !important;">
            <strong>{{ $subscription->subscription_code }}</strong> — Signing link will be sent to client.
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="bi bi-envelope me-2"></i>To Email <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control" name="to_email"
                   value="{{ $subscription->client_email }}"
                   placeholder="client@example.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="bi bi-envelope-plus me-2"></i>CC Emails (Optional)
            </label>
            <input type="text" class="form-control" name="cc_emails"
                   placeholder="email1@example.com, email2@example.com">
            <small class="text-muted">Separate multiple emails with commas</small>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-send-fill me-1"></i>Send Agreement Link
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Send Invoice Modal -->
<div class="modal fade" id="sendInvoiceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5b4dff 0%, #004d40 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-send me-2"></i>Send Invoice
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="sendInvoiceForm">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-info border-0 mb-4">
            <strong id="modalInvoiceCode"></strong>
          </div>
          
          <div class="mb-4">
            <label class="form-label fw-semibold mb-3">
              <i class="bi bi-send me-2"></i>Select Channel
            </label>
            <div class="d-grid gap-2">
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="methodEmail" value="email" checked>
                <label class="form-check-label w-100" for="methodEmail" style="cursor: pointer;">
                  <i class="bi bi-envelope text-primary me-2"></i>
                  <strong>Email</strong>
                  <small class="d-block text-muted">Send professional email with PDF link</small>
                </label>
              </div>
              
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="methodWhatsApp" value="whatsapp">
                <label class="form-check-label w-100" for="methodWhatsApp" style="cursor: pointer;">
                  <i class="bi bi-whatsapp text-success me-2"></i>
                  <strong>WhatsApp</strong>
                  <small class="d-block text-muted">Opens WhatsApp Web with message</small>
                </label>
              </div>
            </div>
          </div>
          
          <div id="emailFields">
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope me-2"></i>To Email <span class="text-danger">*</span>
              </label>
              <input type="email" class="form-control" id="toEmail" name="to_email" required>
              <small class="text-muted">Primary recipient email address</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope-plus me-2"></i>CC Emails (Optional)
              </label>
              <input type="text" class="form-control" id="ccEmails" name="cc_emails" placeholder="email1@example.com, email2@example.com">
              <small class="text-muted">Separate multiple emails with commas</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-chat-text me-2"></i>Custom Message (Optional)
              </label>
              <textarea class="form-control" id="customMessage" name="message" rows="3" 
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

<!-- Payment Reminder Modal -->
<div class="modal fade" id="paymentReminderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-bell-fill me-2"></i>Send Payment Reminder
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="reminderForm">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-warning border-0 mb-4">
            <strong id="reminderInvoiceCode"></strong><br>
            <small>Amount Due: <strong id="reminderAmount"></strong></small>
          </div>
          
          <div class="mb-4">
            <label class="form-label fw-semibold mb-3">
              <i class="bi bi-send me-2"></i>Select Channel
            </label>
            <div class="d-grid gap-2">
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="reminderMethodEmail" value="email" checked>
                <label class="form-check-label w-100" for="reminderMethodEmail" style="cursor: pointer;">
                  <i class="bi bi-envelope text-primary me-2"></i>
                  <strong>Email</strong>
                  <small class="d-block text-muted">Send payment reminder via email</small>
                </label>
              </div>
              
              <div class="form-check p-3 border rounded" style="cursor: pointer;">
                <input class="form-check-input" type="radio" name="method" id="reminderMethodWhatsApp" value="whatsapp">
                <label class="form-check-label w-100" for="reminderMethodWhatsApp" style="cursor: pointer;">
                  <i class="bi bi-whatsapp text-success me-2"></i>
                  <strong>WhatsApp</strong>
                  <small class="d-block text-muted">Opens WhatsApp Web with reminder message</small>
                </label>
              </div>
            </div>
          </div>
          
          <div id="reminderEmailFields">
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope me-2"></i>To Email <span class="text-danger">*</span>
              </label>
              <input type="email" class="form-control" id="reminderToEmail" name="to_email" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-envelope-plus me-2"></i>CC Emails (Optional)
              </label>
              <input type="text" class="form-control" id="reminderCcEmails" name="cc_emails" placeholder="email1@example.com, email2@example.com">
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-semibold">
                <i class="bi bi-chat-text me-2"></i>Custom Reminder Message (Optional)
              </label>
              <textarea class="form-control" id="reminderMessage" name="message" rows="3" 
                        placeholder="Add a custom reminder message...">This is a friendly reminder that your invoice is still pending payment. Please process the payment at your earliest convenience.</textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i>Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-bell-fill me-2"></i>Send Reminder
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentInvoiceId = null;

function openSendModal(invoiceId, invoiceCode, clientEmail) {
  currentInvoiceId = invoiceId;
  document.getElementById('modalInvoiceCode').textContent = 'Invoice: ' + invoiceCode;
  document.getElementById('toEmail').value = clientEmail || '';
  document.getElementById('ccEmails').value = '';
  document.getElementById('customMessage').value = '';
  document.getElementById('methodEmail').checked = true;
  document.getElementById('emailFields').style.display = 'block';
  
  new bootstrap.Modal(document.getElementById('sendInvoiceModal')).show();
}

function openReminderModal(invoiceId, invoiceCode, clientEmail, amount) {
  currentInvoiceId = invoiceId;
  document.getElementById('reminderInvoiceCode').textContent = 'Invoice: ' + invoiceCode;
  document.getElementById('reminderAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  document.getElementById('reminderToEmail').value = clientEmail || '';
  document.getElementById('reminderCcEmails').value = '';
  document.getElementById('reminderMethodEmail').checked = true;
  document.getElementById('reminderEmailFields').style.display = 'block';
  
  new bootstrap.Modal(document.getElementById('paymentReminderModal')).show();
}

function markAsPaid(invoiceId, invoiceCode, amount) {
  if (confirm(`Are you sure you want to mark invoice ${invoiceCode} as PAID?\n\nAmount: ₹${parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n\nThis will create a payment transaction record.`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/monthly-invoice/${invoiceId}/mark-paid`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
  }
}

function deleteInvoice(invoiceId, invoiceCode) {
  if (confirm(`Are you sure you want to delete invoice ${invoiceCode}?\n\nThis action cannot be undone.`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/monthly-invoice/${invoiceId}/delete`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
  }
}

document.querySelectorAll('input[name="method"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const emailFields = document.getElementById('emailFields');
    if (this.value === 'email') {
      emailFields.style.display = 'block';
      document.getElementById('toEmail').required = true;
    } else {
      emailFields.style.display = 'none';
      document.getElementById('toEmail').required = false;
    }
  });
});

// Reminder modal method toggle
document.querySelectorAll('input[name="method"]').forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.id === 'reminderMethodEmail' || this.id === 'reminderMethodWhatsApp') {
      const reminderEmailFields = document.getElementById('reminderEmailFields');
      if (this.value === 'email') {
        reminderEmailFields.style.display = 'block';
        document.getElementById('reminderToEmail').required = true;
      } else {
        reminderEmailFields.style.display = 'none';
        document.getElementById('reminderToEmail').required = false;
      }
    }
  });
});

function openAgreementModal() {
  new bootstrap.Modal(document.getElementById('generateAgreementModal')).show();
}

function openSendAgreementEmailModal() {
  new bootstrap.Modal(document.getElementById('sendAgreementEmailModal')).show();
}

function copyLink(link) {
  // navigator.clipboard only works on HTTPS — use execCommand fallback for HTTP
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(link).then(function() {
      showCopyToast();
    }).catch(function() {
      fallbackCopy(link);
    });
  } else {
    fallbackCopy(link);
  }
}

function fallbackCopy(link) {
  const el = document.createElement('textarea');
  el.value = link;
  el.style.position = 'fixed';
  el.style.left = '-9999px';
  el.style.top = '-9999px';
  document.body.appendChild(el);
  el.focus();
  el.select();
  try {
    const success = document.execCommand('copy');
    if (success) {
      showCopyToast();
    } else {
      prompt('Copy this signing link manually:', link);
    }
  } catch(e) {
    prompt('Copy this signing link manually:', link);
  }
  document.body.removeChild(el);
}

function showCopyToast() {
  // Remove existing toast if any
  const existing = document.getElementById('copyToast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'copyToast';
  toast.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Signing link copied to clipboard!';
  toast.style.cssText = `
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    background: #004d40; color: #fff;
    padding: 12px 20px; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    transition: opacity 0.4s;
  `;
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; }, 2500);
  setTimeout(() => { toast.remove(); }, 3000);
}


document.getElementById('sendInvoiceForm').addEventListener('submit', function(e) {
  e.preventDefault();
  this.action = `/monthly-invoice/${currentInvoiceId}/send`;
  this.submit();
});

document.getElementById('reminderForm').addEventListener('submit', function(e) {
  e.preventDefault();
  this.action = `/monthly-invoice/${currentInvoiceId}/send-reminder`;
  this.submit();
});
</script>

<style>
  .form-check:hover {
    background-color: #f8f9fa;
  }
  
  .modal-content {
    border-radius: 15px;
  }
  
  .modal-header {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
  }
</style>
@endsection
