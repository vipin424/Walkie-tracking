@extends('layouts.app')
@section('title','Monthly Subscriptions')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Monthly Subscriptions</h4>
      <p class="text-muted mb-0">Manage recurring monthly billing for clients</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('subscriptions.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Subscription
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold text-muted small">Status</label>
          <select id="statusFilter" class="form-select form-select-md">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="cancelled">Cancelled</option>
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
        <i class="bi bi-calendar-check me-2 text-warning"></i>All Subscriptions
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="subscriptions-table" class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Subscription Code</th>
              <th class="px-4 py-3 text-muted fw-semibold">Client</th>
              <th class="px-4 py-3 text-muted fw-semibold">Billing Info</th>
              <th class="px-4 py-3 text-muted fw-semibold">Monthly Amount</th>
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
  table = $('#subscriptions-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('subscriptions.index') }}',
      data: function(d) {
        d.status = $('#statusFilter').val();
      }
    },
    columns: [
      { data: 'subscription_code', name: 'subscription_code' },
      { data: 'client', name: 'client_name' },
      { data: 'billing_info', name: 'billing_day_of_month', orderable: false },
      { data: 'amount', name: 'monthly_amount' },
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

function generateInvoice(id) {
  if(confirm('Generate invoice for current billing period?')) {
    window.location.href = `/subscriptions/${id}/generate-invoice`;
  }
}
</script>
@endsection
