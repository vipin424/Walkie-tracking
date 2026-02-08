@extends('layouts.app')
@section('title','Orders')
@section('content')
<div class="container-fluid px-4 py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Orders</h4>
      <p class="text-muted mb-0">Manage and track all Orders</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('orders.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Order
    </a>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <form method="get">
        <div class="row g-3 align-items-end">
          <div class="col-md-2">
            <label class="form-label fw-semibold text-muted small">Status</label>
            <select name="status" class="form-select form-select-md">
              <option value="">All Status</option>
              @foreach(['confirmed','completed','sent'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold text-muted small">Payment</label>
            <select name="payment_status" class="form-select form-select-md">
              <option value="">All Payments</option>
              <option value="pending" @selected(request('payment_status')==='pending')>Pending</option>
              <option value="partial" @selected(request('payment_status')==='partial')>Partial</option>
              <option value="paid" @selected(request('payment_status')==='paid')>Paid</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold text-muted small">Search</label>
            <input class="form-control form-control-md" name="search" placeholder="Search by code or client..." value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-warning btn-md w-100">
              <i class="bi bi-funnel me-2"></i>Filter
            </button>
          </div>
          @if(request('status') || request('search') || request('payment_status'))
          <div class="col-md-2">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-md w-100">
              <i class="bi bi-x-circle me-2"></i>Clear
            </a>
          </div>
          @endif
        </div>
      </form>
    </div>
  </div>

  <!-- orders Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-file-earmark-text me-2 text-warning"></i>All Orders
        </h5>
        <span class="badge bg-warning bg-opacity-10 text-warning fs-6">{{ $orders->total() }} Total</span>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Order Code</th>
              <th class="px-4 py-3 text-muted fw-semibold">Event Period</th>
              <th class="px-4 py-3 text-muted fw-semibold">Duration</th>
              <th class="px-4 py-3 text-muted fw-semibold">Client</th>
              <th class="px-4 py-3 text-muted fw-semibold">Status</th>
              <th class="px-4 py-3 text-muted fw-semibold">Settlement</th>
              <th class="px-4 py-3 text-muted fw-semibold">Total</th>
              <th class="px-4 py-3 text-muted fw-semibold">Pending</th>
              <th class="px-4 py-3 text-muted fw-semibold">Payment</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $q)
              <tr>
                <td class="px-4 py-3">
                  <a href="{{ route('orders.show',$q) }}" class="text-decoration-none fw-semibold text-primary">
                    <i class="bi bi-file-text me-2"></i>{{ $q->order_code }}
                  </a>
                </td>
                
                <td class="px-4 py-3">
                    <div class="fw-medium">
                        {{ \Carbon\Carbon::parse($q->event_from)->format('d M') }}
                        →
                        {{ \Carbon\Carbon::parse($q->event_to)->format('d M Y') }}
                    </div>
                  @if($q->event_state === 'running')
                      <span class="badge bg-success">Live</span>
                  @elseif($q->event_state === 'upcoming')
                      <span class="badge bg-danger">{{ $q->days_label }}</span>
                  @else
                      <span class="badge bg-secondary">Completed</span>
                  @endif
                </td>

                <td class="px-4 py-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $q->event_days }} Day{{ $q->event_days > 1 ? 's' : '' }}
                    </span>
                </td>

                <td class="px-4 py-3">
                  <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                      <i class="bi bi-person-fill text-warning"></i>
                    </div>
                    <div>
                      <span class="fw-medium d-block">{{ $q->client_name }}</span>
                      <small class="text-muted">{{ $q->client_phone }}</small>
                    </div>
                  </div>
                </td>
                
                <td class="px-4 py-3">
                  @php
                    $statusColors = [
                      'confirmed' => 'primary',
                      'completed' => 'success',
                      'sent' => 'danger'
                    ];
                    $color = $statusColors[$q->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $color }} px-3 py-2" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.15) !important; color: var(--bs-{{ $color }}) !important;">
                    {{ ucfirst($q->status) }}
                  </span>
                </td>
                
                <td class="px-4 py-3">
                  @php
                    $settlementColors = [
                      'pending' => 'warning',
                      'settled' => 'success',
                      'failed' => 'danger'
                    ];
                    $color = $settlementColors[$q->settlement_status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $color }} px-3 py-2" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.15) !important; color: var(--bs-{{ $color }}) !important;">
                    {{ ucfirst($q->settlement_status) }}
                  </span>
                </td>
                
                <td class="px-4 py-3">
                  <span class="fw-semibold text-dark">₹{{ number_format($q->total_amount, 2) }}</span>
                </td>
                
                <td class="px-4 py-3">
                  <span class="fw-semibold {{ $q->final_payable > 0 ? 'text-danger' : 'text-success' }}">
                    ₹{{ number_format($q->final_payable, 2) }}
                  </span>
                </td>
                
                <td class="px-4 py-3">
                  @php
                    $paymentColors = [
                      'pending' => 'warning',
                      'partial' => 'info',
                      'paid' => 'success'
                    ];
                    $color = $paymentColors[$q->payment_status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $color }} px-3 py-2" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.15) !important; color: var(--bs-{{ $color }}) !important;">
                    {{ ucfirst($q->payment_status) }}
                  </span>
                </td>
                
                <td class="px-4 py-3">
                  <div class="btn-group" role="group">
                    <a href="{{ route('orders.show',$q) }}" class="btn btn-sm btn-outline-primary" title="View">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('orders.edit',$q) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    
                    @if($q->payment_status !== 'paid' && $q->final_payable > 0)
                      <button class="btn btn-sm btn-outline-info" 
                              onclick="openReminderModal({{ $q->id }}, '{{ $q->order_code }}', '{{ $q->client_name }}', {{ $q->final_payable }})" 
                              title="Send Reminder">
                        <i class="bi bi-bell-fill"></i>
                      </button>
                      
                      <button class="btn btn-sm btn-outline-success" 
                              onclick="openPaymentModal({{ $q->id }}, '{{ $q->order_code }}', {{ $q->final_payable }})" 
                              title="Record Payment">
                        <i class="bi bi-cash-coin"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <p class="mb-0">No orders found</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    @if($orders->hasPages())
      <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
</div>

<!-- Payment Reminder Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5b4dff 0%, #004d40 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-bell-fill me-2"></i>Send Payment Reminder
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 mb-4">
          <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2" style="font-size: 1.2rem;"></i>
            <div>
              <strong id="reminderOrderCode"></strong>
              <div class="small">Client: <span id="reminderClientName"></span></div>
              <div class="small fw-bold text-danger">Pending: ₹<span id="reminderPendingAmount"></span></div>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label class="form-label fw-semibold mb-3">
            <i class="bi bi-send me-2"></i>Select Channel
          </label>
          <div class="d-grid gap-2">
            <div class="form-check p-3 border rounded" style="cursor: pointer;">
              <input class="form-check-input" type="radio" name="reminderChannel" id="channelWhatsApp" value="whatsapp" checked>
              <label class="form-check-label w-100" for="channelWhatsApp" style="cursor: pointer;">
                <i class="bi bi-whatsapp text-success me-2"></i>
                <strong>WhatsApp</strong>
                <small class="d-block text-muted">Opens WhatsApp Web with message</small>
              </label>
            </div>
            
            <div class="form-check p-3 border rounded" style="cursor: pointer;">
              <input class="form-check-input" type="radio" name="reminderChannel" id="channelEmail" value="email">
              <label class="form-check-label w-100" for="channelEmail" style="cursor: pointer;">
                <i class="bi bi-envelope text-primary me-2"></i>
                <strong>Email</strong>
                <small class="d-block text-muted">Sends professional email reminder</small>
              </label>
            </div>
            
            <div class="form-check p-3 border rounded" style="cursor: pointer;">
              <input class="form-check-input" type="radio" name="reminderChannel" id="channelBoth" value="both">
              <label class="form-check-label w-100" for="channelBoth" style="cursor: pointer;">
                <i class="bi bi-send-fill text-warning me-2"></i>
                <strong>Both (WhatsApp + Email)</strong>
                <small class="d-block text-muted">Maximum reach</small>
              </label>
            </div>
          </div>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">
            <i class="bi bi-chat-text me-2"></i>Custom Message (Optional)
          </label>
          <textarea class="form-control" id="reminderMessage" rows="4" 
                    placeholder="Leave empty to use professional default template"></textarea>
          <small class="text-muted">
            <i class="bi bi-lightbulb me-1"></i>
            Tip: Default message includes order details and payment info
          </small>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-2"></i>Cancel
        </button>
        <button type="button" class="btn btn-info" onclick="sendReminder()">
          <i class="bi bi-send-fill me-2"></i>Send Reminder
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background: linear-gradient(135deg, #086051ff 0%, #004d408 100%); color: white;">
        <h5 class="modal-title">
          <i class="bi bi-cash-coin me-2"></i>Record Payment
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-success border-0 mb-4">
          <strong id="paymentOrderCode"></strong>
          <div class="small fw-bold text-danger mt-1">Pending: ₹<span id="paymentPendingAmount"></span></div>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">
            Payment Amount <span class="text-danger">*</span>
          </label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-light">₹</span>
            <input type="number" class="form-control" id="paymentAmount" step="0.01" min="0.01" required>
          </div>
          <button type="button" class="btn btn-sm btn-link text-success p-0 mt-2" onclick="fillFullAmount()">
            <i class="bi bi-check-circle me-1"></i>Pay full pending amount
          </button>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">
            Payment Method <span class="text-danger">*</span>
          </label>
          <select class="form-select form-select-lg" id="paymentMethod" required>
            <option value="">Choose payment method...</option>
            <option value="gpay">💳 Google Pay (GPay)</option>
            <option value="phonepe">📱 PhonePe</option>
            <option value="paytm">💰 Paytm</option>
            <option value="upi">🔗 Other UPI</option>
            <option value="bank_transfer">🏦 Bank Transfer / NEFT / RTGS</option>
            <option value="cash">💵 Cash</option>
            <option value="other">📝 Other</option>
          </select>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Transaction ID</label>
            <input type="text" class="form-control" id="transactionId" placeholder="Optional">
          </div>
          
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Payment Date</label>
            <input type="datetime-local" class="form-control" id="paidAt" value="{{ now()->format('Y-m-d\TH:i') }}">
          </div>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">Notes</label>
          <textarea class="form-control" id="paymentNotes" rows="2" placeholder="Any additional notes..."></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-2"></i>Cancel
        </button>
        <button type="button" class="btn btn-success" onclick="recordPayment()">
          <i class="bi bi-check-circle me-2"></i>Record Payment
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
  }
  
  .btn-group .btn {
    border-radius: 0 !important;
  }
  
  .btn-group .btn:first-child {
    border-top-left-radius: 0.375rem !important;
    border-bottom-left-radius: 0.375rem !important;
  }
  
  .btn-group .btn:last-child {
    border-top-right-radius: 0.375rem !important;
    border-bottom-right-radius: 0.375rem !important;
  }
  
  .badge {
    font-weight: 500;
    letter-spacing: 0.3px;
  }
  
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

<script>
let currentOrderId = null;
let currentPendingAmount = 0;

// Get CSRF token helper function
function getCSRFToken() {
  let token = document.querySelector('meta[name="csrf-token"]');
  if (!token) {
    // Try to find it in a form
    token = document.querySelector('input[name="_token"]');
  }
  return token ? token.getAttribute('content') || token.value : '';
}

function openReminderModal(orderId, orderCode, clientName, pendingAmount) {
  currentOrderId = orderId;
  document.getElementById('reminderOrderCode').textContent = 'Order: ' + orderCode;
  document.getElementById('reminderClientName').textContent = clientName;
  document.getElementById('reminderPendingAmount').textContent = parseFloat(pendingAmount).toFixed(2);
  document.getElementById('reminderMessage').value = '';
  
  new bootstrap.Modal(document.getElementById('reminderModal')).show();
}

function openPaymentModal(orderId, orderCode, pendingAmount) {
  currentOrderId = orderId;
  currentPendingAmount = parseFloat(pendingAmount);
  
  document.getElementById('paymentOrderCode').textContent = 'Order: ' + orderCode;
  document.getElementById('paymentPendingAmount').textContent = currentPendingAmount.toFixed(2);
  document.getElementById('paymentAmount').value = '';
  document.getElementById('paymentMethod').value = '';
  document.getElementById('transactionId').value = '';
  document.getElementById('paymentNotes').value = '';
  
  new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function fillFullAmount() {
  document.getElementById('paymentAmount').value = currentPendingAmount.toFixed(2);
}

function sendReminder() {
  const channel = document.querySelector('input[name="reminderChannel"]:checked').value;
  const message = document.getElementById('reminderMessage').value;
  
  const btn = event.target;
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
  btn.disabled = true;
  
  const csrfToken = getCSRFToken();
  
  if (!csrfToken) {
    showAlert('danger', 'CSRF token not found. Please refresh the page.');
    btn.innerHTML = originalHtml;
    btn.disabled = false;
    return;
  }
  
  fetch(`/orders/${currentOrderId}/send-reminder`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ channel, message })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      bootstrap.Modal.getInstance(document.getElementById('reminderModal')).hide();
      showAlert('success', data.message);
      
      // Open WhatsApp Web if WhatsApp selected
      if (data.whatsapp_link && (channel === 'whatsapp' || channel === 'both')) {
        setTimeout(() => {
          window.open(data.whatsapp_link, '_blank');
          showAlert('info', '📱 WhatsApp opened! Send the message to client.');
        }, 500);
      }
    } else {
      showAlert('danger', data.message || 'Failed to send reminder');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('danger', 'An error occurred. Please try again.');
  })
  .finally(() => {
    btn.innerHTML = originalHtml;
    btn.disabled = false;
  });
}

function recordPayment() {
  const amount = document.getElementById('paymentAmount').value;
  const method = document.getElementById('paymentMethod').value;
  const transactionId = document.getElementById('transactionId').value;
  const paidAt = document.getElementById('paidAt').value;
  const notes = document.getElementById('paymentNotes').value;
  
  if (!amount || !method) {
    showAlert('warning', 'Please fill in amount and payment method');
    return;
  }
  
  if (parseFloat(amount) <= 0) {
    showAlert('warning', 'Amount must be greater than 0');
    return;
  }
  
  if (parseFloat(amount) > currentPendingAmount) {
    showAlert('warning', 'Amount cannot exceed pending amount');
    return;
  }
  
  const btn = event.target;
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Recording...';
  btn.disabled = true;
  
  const csrfToken = getCSRFToken();
  
  if (!csrfToken) {
    showAlert('danger', 'CSRF token not found. Please refresh the page.');
    btn.innerHTML = originalHtml;
    btn.disabled = false;
    return;
  }
  
  fetch(`/orders/${currentOrderId}/record-payment`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      amount,
      payment_method: method,
      transaction_id: transactionId,
      paid_at: paidAt,
      notes
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
      showAlert('success', '✅ ' + data.message);
      
      setTimeout(() => location.reload(), 1500);
    } else {
      showAlert('danger', data.message || 'Failed to record payment');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('danger', 'An error occurred. Please try again.');
  })
  .finally(() => {
    btn.innerHTML = originalHtml;
    btn.disabled = false;
  });
}

function showAlert(type, message) {
  const iconMap = {
    'success': '✅',
    'danger': '❌',
    'warning': '⚠️',
    'info': 'ℹ️'
  };
  
  const alertHtml = `
    <div class="alert alert-${type} alert-dismissible fade show position-fixed shadow-lg" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 350px; border-radius: 10px;" 
         role="alert">
      <strong>${iconMap[type] || ''} ${message}</strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  
  document.body.insertAdjacentHTML('beforeend', alertHtml);
  
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      const bsAlert = bootstrap.Alert.getInstance(alert);
      if (bsAlert) bsAlert.close();
    });
  }, 5000);
}
</script>
@endsection
