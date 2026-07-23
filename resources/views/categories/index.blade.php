@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="container-fluid p-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold mb-0">Categories</h4>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Add Category
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table id="categories-table" class="table table-hover align-middle w-100">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Slug</th>
              <th>Featured</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function() {
    $('#categories-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('categories.data') }}',
      columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'slug', name: 'slug' },
        { data: 'is_featured', name: 'is_featured' },
        { data: 'is_active', name: 'is_active' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
      ],
      order: [[0, 'desc']]
    });
  });
</script>
@endpush
