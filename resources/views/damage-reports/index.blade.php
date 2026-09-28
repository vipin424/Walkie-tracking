@extends('layouts.app')
@section('title','Damage Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Damage Reports</h4>
      <p class="text-muted mb-0">Physical damage &amp; replacement charge invoices</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('damage-reports.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Damage Report
    </a>
  </div>

  {{-- Filters --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold text-muted small">Status</label>
          <select id="statusFilter" class="form-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="sent">Sent</option>
            <option value="paid">Paid</option>
          </select>
        </div>
        <div class="col-md-4">
          <button id="clearFilters" class="btn btn-outline-secondary w-100">
            <i class="bi bi-x-circle me-2"></i>Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>All Damage Reports
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="damageReportsTable" class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Report Code</th>
              <th class="px-4 py-3 text-muted fw-semibold">Client</th>
              <th class="px-4 py-3 text-muted fw-semibold">Damage Date</th>
              <th class="px-4 py-3 text-muted fw-semibold">Amount</th>
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
  .table-hover tbody tr:hover { background-color: rgba(13, 110, 253, 0.03); }
  .badge { font-weight: 500; letter-spacing: 0.3px; }
  .modal-content { border-radius: 15px; }
  .modal-header { border-top-left-radius: 15px; border-top-right-radius: 15px; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
let table;

$(document).ready(function () {
  table = $('#damageReportsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('damage-reports.index') }}',
      data: function (d) { d.status = $('#statusFilter').val(); }
    },
    columns: [
      { data: 'report_code',  name: 'report_code' },
      { data: 'client_info',  name: 'client_name', orderable: false },
      { data: 'damage_date',  name: 'damage_date' },
      { data: 'amount',       name: 'replacement_amount' },
      { data: 'status_badge', name: 'status', orderable: false },
      { data: 'actions', orderable: false, searchable: false, className: 'text-center' },
    ],
    order: [[0, 'desc']],
    pageLength: 10,
    language: {
      processing: '<div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>'
    }
  });

  $('#statusFilter').change(function () { table.draw(); });
  $('#clearFilters').click(function () { $('#statusFilter').val(''); table.draw(); });
});
</script>
@endsection
