@extends('layouts.app')
@section('title', 'Monthly Quotations')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Monthly Rate Quotations</h4>
      <p class="text-muted mb-0">Rate contracts for monthly walkie rental</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('monthly-quotations.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Quotation
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
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
          </select>
        </div>
        <div class="col-md-3">
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
        <i class="bi bi-file-earmark-text me-2 text-warning"></i>All Monthly Quotations
      </h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="mq-table" class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-3 py-3 text-muted fw-semibold">Code</th>
              <th class="px-3 py-3 text-muted fw-semibold">Client / Company</th>
              <th class="px-3 py-3 text-muted fw-semibold">Total Amount</th>
              <th class="px-3 py-3 text-muted fw-semibold">Status</th>
              <th class="px-3 py-3 text-muted fw-semibold">Date</th>
              <th class="px-3 py-3 text-muted fw-semibold text-center">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
  .table-hover tbody tr:hover { background-color: rgba(13,110,253,0.03); }
  .btn-group .btn { border-radius: 0 !important; }
  .btn-group .btn:first-child { border-radius: 0.375rem 0 0 0.375rem !important; }
  .btn-group .btn:last-child  { border-radius: 0 0.375rem 0.375rem 0 !important; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
  const table = $('#mq-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('monthly-quotations.index') }}',
      data: function (d) { d.status = $('#statusFilter').val(); }
    },
    columns: [
      { data: 'code_link',    name: 'code' },
      { data: 'client',       name: 'client_name', orderable: false },
      { data: 'total',        name: 'total_amount' },
      { data: 'status_badge', name: 'status' },
      { data: 'created',      name: 'created_at' },
      { data: 'actions',      orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    pageLength: 15,
    language: {
      processing: '<div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>'
    }
  });

  $('#statusFilter').change(() => table.draw());
  $('#clearFilters').click(function () { $('#statusFilter').val(''); table.draw(); });
});
</script>
@endsection
