@extends('layouts.app')
@section('title','Orders')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Orders</h4>
      <p class="text-muted mb-0">Manage and track all Orders</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('orders.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Order
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold text-muted small">Status</label>
          <select id="statusFilter" class="form-select form-select-md">
            <option value="">All Status</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="sent">Sent</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold text-muted small">Payment</label>
          <select id="paymentFilter" class="form-select form-select-md">
            <option value="">All Payments</option>
            <option value="pending">Pending</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
          </select>
        </div>
        <div class="col-md-4">
          <button id="clearFilters" class="btn btn-outline-secondary btn-md w-100">
            <i class="bi bi-x-circle me-2"></i>Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-file-earmark-text me-2 text-warning"></i>All Orders
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="ordersTable" class="table table-hover align-middle mb-0">
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
        </table>
      </div>
    </div>
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
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5b4dff 0%, #004d40 100%); color: white;">
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
let currentOrderId = null;
let currentPendingAmount = 0;
let table;

$(document).ready(function() {
  table = $('#ordersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('orders.index') }}',
      data: function(d) {
        d.status = $('#statusFilter').val();
        d.payment_status = $('#paymentFilter').val();
      }
    },
    columns: [
      { data: 'order_code', name: 'order_code' },
      { data: 'event_period', name: 'event_from', orderable: false },
      { data: 'duration', name: 'total_days', orderable: false },
      { data: 'client', name: 'client_name' },
      { data: 'status', name: 'status' },
      { data: 'settlement', name: 'settlement_status' },
      { data: 'total', name: 'total_amount' },
      { data: 'pending', name: 'final_payable' },
      { data: 'payment', name: 'payment_status' },
      { data: 'actions', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    pageLength: 10,
    language: {
      processing: '<div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>'
    }
  });

  $('#statusFilter, #paymentFilter').change(function() {
    table.draw();
  });

  $('#clearFilters').click(function() {
    $('#statusFilter, #paymentFilter').val('');
    table.draw();
  });
});

function getCSRFToken() {
  let token = document.querySelector('meta[name="csrf-token"]');
  if (!token) {
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
      
      setTimeout(() => table.draw(), 1500);
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
