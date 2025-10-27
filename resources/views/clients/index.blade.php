@extends('layouts.app')
@section('title', 'Clients')
@section('content')
<style>
  .mt-5 {
    margin-top: 2.5rem !important;
}
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="fw-bold mb-0">👤 Clients</h4>
    <small class="text-muted">Manage and view all your registered clients</small>
  </div>
  <a class="btn btn-warning shadow-sm" href="{{ route('clients.create') }}">
    <i class="bi bi-plus-circle me-1"></i> Add Client
  </a>
</div>

{{-- SEARCH FILTER CARD --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="get" class="row gy-2 gx-3 align-items-center">
      <div class="col-md-4">
        <label class="form-label fw-semibold text-secondary">Search Client</label>
        <input type="text" name="q" class="form-control shadow-sm" placeholder="Enter name or company..."
          value="{{ $q }}">
      </div>
      <div class="col-md-2 mt-5">
        <button class="btn btn-outline-warning btn-md w-100">
          <i class="bi bi-search me-1"></i> Filter
        </button>
      </div>
    </form>
  </div>
</div>

{{-- CLIENTS TABLE --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
    <div><i class="bi bi-people text-warning me-2"></i> All Clients</div>
    <span class="badge bg-light text-dark">{{ $clients->total() }} Total</span>
  </div>

  <div class="card-body p-0">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Client Name</th>
          <th>Contact</th>
          <th>Company</th>
          <th class="text-end" style="width:150px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $c)
        <tr>
          <td class="fw-medium">
            <div class="d-flex align-items-center">
              <div class="avatar-sm bg-warning-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                <i class="bi bi-person-fill"></i>
              </div>
              <span>{{ $c->name }}</span>
            </div>
          </td>
          <td class="text-muted">{{ $c->contact_number }}</td>
          <td>{{ $c->company_name ?? '-' }}</td>
          <td class="text-end">
            <a href="{{ route('clients.edit', $c) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
              <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('clients.destroy', $c) }}" method="post" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this client?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center py-4 text-muted">No clients found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- PAGINATION --}}
@if($clients->hasPages())
  <div class="mt-4 d-flex justify-content-center">
    {{ $clients->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection
