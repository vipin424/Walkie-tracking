@extends('layouts.app')
@section('title','Quotations')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Quotation</h4>
      <p class="text-muted mb-0">Manage and track all quotation</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('quotations.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Quotation
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold text-muted small">Status</label>
          <select id="statusFilter" class="form-select form-select-md">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="accepted">Accepted</option>
            <option value="rejected">Rejected</option>
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
        <i class="bi bi-file-earmark-text me-2 text-warning"></i>All Quotations
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="quotationsTable" class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Quotation Code</th>
              <th class="px-4 py-3 text-muted fw-semibold">Event Period</th>
              <th class="px-4 py-3 text-muted fw-semibold">Duration</th>
              <th class="px-4 py-3 text-muted fw-semibold">Client</th>
              <th class="px-4 py-3 text-muted fw-semibold">Total Amount</th>
              <th class="px-4 py-3 text-muted fw-semibold">Status</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Actions</th>
            </tr>
          </thead>
        </table>
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
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
let table;

$(document).ready(function() {
  table = $('#quotationsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('quotations.index') }}',
      data: function(d) {
        d.status = $('#statusFilter').val();
      }
    },
    columns: [
      { data: 'code', name: 'code' },
      { data: 'event_period', name: 'event_from', orderable: false },
      { data: 'duration', name: 'total_days', orderable: false },
      { data: 'client', name: 'client_name' },
      { data: 'total', name: 'total_amount' },
      { data: 'status', name: 'status' },
      { data: 'actions', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    pageLength: 10,
    language: {
      processing: '<div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>'
    }
  });

  $('#statusFilter').change(function() {
    table.draw();
  });

  $('#clearFilters').click(function() {
    $('#statusFilter').val('');
    table.draw();
  });
});

function deleteQuotation(id) {
  if (!confirm('Are you sure you want to delete this quotation?')) return;
  
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  
  fetch(`/quotations/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showAlert('success', data.message);
      table.draw();
    } else {
      showAlert('danger', 'Failed to delete quotation');
    }
  })
  .catch(() => showAlert('danger', 'An error occurred'));
}

function showAlert(type, message) {
  const iconMap = { 'success': '✅', 'danger': '❌' };
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
    document.querySelectorAll('.alert').forEach(alert => {
      const bsAlert = bootstrap.Alert.getInstance(alert);
      if (bsAlert) bsAlert.close();
    });
  }, 3000);
}
</script>
@endsection
