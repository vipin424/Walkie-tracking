@extends('layouts.app')
@section('title', 'Dispatches')
@section('content')
<div class="container-fluid px-4 py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Dispatches</h4>
      <p class="text-muted mb-0">Manage and track all dispatch orders</p>
    </div>
    <a class="btn btn-warning btn-md shadow-sm" href="{{ route('dispatches.create') }}">
      <i class="bi bi-plus-circle me-2"></i>New Dispatch
    </a>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <form method="get">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-semibold text-muted small">Status Filter</label>
            <select name="status" class="form-select form-select-md">
              <option value="">All Status</option>
              @foreach(['Active','Partially Returned','Returned'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold text-muted small">Client Name</label>
            <input class="form-control form-control-md" name="client" placeholder="Search by client name..." value="{{ request('client') }}">
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-warning btn-md w-100">
              <i class="bi bi-funnel me-2"></i>Apply Filters
            </button>
          </div>
          @if(request('status') || request('client'))
          <div class="col-md-2">
            <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary btn-md w-100">
              <i class="bi bi-x-circle me-2"></i>Clear
            </a>
          </div>
          @endif
        </div>
      </form>
    </div>
  </div>

  <!-- Dispatches Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-box-seam me-2 text-warning"></i>All Dispatches
        </h5>
        <span class="badge bg-warning bg-opacity-10 text-warning fs-6">{{ $dispatches->total() }} Total</span>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">Dispatch Code</th>
              <th class="px-4 py-3 text-muted fw-semibold">Client</th>
              <th class="px-4 py-3 text-muted fw-semibold">Status</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Total Items</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dispatches as $d)
              <tr>
                <td class="px-4 py-3">
                  <a href="{{ route('dispatches.show',$d) }}" class="text-decoration-none fw-semibold text-primary">
                    <i class="bi bi-file-text me-2"></i>{{ $d->code }}
                  </a>
                </td>
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                      <i class="bi bi-person-fill text-warning"></i>
                    </div>
                    <span class="fw-medium">{{ $d->client->name }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  @php
                    $statusColors = [
                      'Active' => 'info',
                      'Partially Returned' => 'danger',
                      'Returned' => 'success'
                    ];
                    $color = $statusColors[$d->status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $color }} px-3 py-2" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.15) !important; color: var(--bs-{{ $color }}) !important;">
                    {{ $d->status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-secondary bg-opacity-10 text-secondary fs-6 px-3 py-2">
                    {{ $d->total_items }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="btn-group" role="group">
                  @if($d->invoice_path)
                      <a href="{{ asset('storage/'.$d->invoice_path) }}" target="_blank" class="btn btn-sm btn-outline-success">
                          <i class="bi bi-file-earmark-pdf"></i>
                      </a>
                  @endif
                    <a href="{{ route('dispatches.show',$d) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                      <i class="bi bi-eye"></i>
                    </a>
                    <!-- <form method="post" action="{{ route('dispatches.destroy',$d) }}" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this dispatch?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form> -->
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <p class="mb-0">No dispatches found</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  {{-- ✅ Pagination --}}
  @if($dispatches->hasPages())
    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
      {{ $dispatches->links('pagination::bootstrap-5') }}
    </div>
  @endif
  </div>
</div>

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
  }
  
  .btn-group .btn {
    border-radius: 0;
  }
  
  .btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
  }
  
  .btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
  }
  
  .badge {
    font-weight: 500;
    letter-spacing: 0.3px;
  }
  
  .card {
    transition: all 0.3s ease;
  }
  
  .form-select:focus,
  .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
  }
</style>
@endsection