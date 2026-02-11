@extends('layouts.app')

@section('title', 'Items')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Items</h4>
      <p class="text-muted mb-0">Manage and track all Items</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('items.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Item  
    </a>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table id="items-table" class="table table-hover align-middle">
          <thead class="bg-light">
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Description</th>
              <th>Unit Price</th>
              <th>Tax %</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

@push('scripts')
<script>
$(document).ready(function() {
  const table = $('#items-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('items.data') }}',
    columns: [
      { data: 'name', name: 'name' },
      { data: 'type', name: 'type' },
      { data: 'description', name: 'description', orderable: false },
      { data: 'unit_price', name: 'unit_price' },
      { data: 'tax_percent', name: 'tax_percent' },
      { data: 'is_active', name: 'is_active' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
    ],
    order: [[0, 'desc']],
    pageLength: 25,
    language: {
      emptyTable: 'No items found',
      zeroRecords: 'No matching items found'
    }
  });

  // Delete handler
  $('#items-table').on('click', '.delete-btn', function() {
    if (confirm('Are you sure you want to delete this item?')) {
      const id = $(this).data('id');
      $.ajax({
        url: `/items/${id}`,
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: () => table.ajax.reload()
      });
    }
  });
});
</script>
@endpush
@endsection
