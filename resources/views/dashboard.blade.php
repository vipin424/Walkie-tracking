@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

{{-- DASHBOARD HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="fw-bold mb-0">📊 Dashboard</h4>
    <small class="text-muted">Overview of clients, dispatches, and payments</small>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
  {{-- Total Clients --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Total Clients</h6>
          <h3 class="fw-bold mb-0">{{ $stats['clients'] }}</h3>
        </div>
        <div class="icon-box bg-primary-subtle text-primary">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Dispatches --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Total Dispatches</h6>
          <h3 class="fw-bold mb-0">{{ $stats['dispatches'] }}</h3>
        </div>
        <div class="icon-box bg-secondary-subtle text-secondary">
          <i class="bi bi-truck"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Active Dispatches --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Active Dispatches</h6>
          <h3 class="fw-bold mb-0 text-info">{{ $stats['active_dispatches'] }}</h3>
        </div>
        <div class="icon-box bg-info-subtle text-info">
          <i class="bi bi-broadcast-pin"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Partially Returned --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Partially Returned</h6>
          <h3 class="fw-bold mb-0 text-warning">{{ $stats['partial_dispatches'] }}</h3>
        </div>
        <div class="icon-box bg-warning-subtle text-warning">
          <i class="bi bi-arrow-repeat"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Returned --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Returned Dispatches</h6>
          <h3 class="fw-bold mb-0 text-success">{{ $stats['returned_dispatches'] }}</h3>
        </div>
        <div class="icon-box bg-success-subtle text-success">
          <i class="bi bi-check2-circle"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Unpaid --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Unpaid Dispatches</h6>
          <h3 class="fw-bold mb-0 text-danger">{{ $stats['unpaid_dispatches'] }}</h3>
        </div>
        <div class="icon-box bg-danger-subtle text-danger">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Advance --}}
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm metric-card bg-white h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-muted mb-1">Advance Received</h6>
          <h3 class="fw-bold mb-0 text-purple">{{ $stats['advance_dispatches'] }}</h3>
        </div>
        <div class="icon-box" style="background:#f0e9ff;color:#6f42c1;">
          <i class="bi bi-wallet2"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- RECENT DISPATCH TABLE --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
    <div><i class="bi bi-clock-history text-primary me-2"></i> Recent Dispatches</div>
    <a href="{{ route('dispatches.index') }}" class="text-decoration-none small">View All</a>
  </div>

  <div class="card-body p-0">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Code</th>
          <th>Client</th>
          <th>Status</th>
          <th>Dispatch Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recent_dispatches as $d)
        <tr>
          <td>
            <a href="{{ route('dispatches.show', $d) }}" class="text-decoration-none fw-semibold text-primary">
              <i class="bi bi-file-earmark-text me-1"></i>{{ $d->code }}
            </a>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                <i class="bi bi-person-fill"></i>
              </div>
              <span>{{ $d->client->name }}</span>
            </div>
          </td>
          <td>
            @php
              $badge = [
                'Active' => 'bg-info-subtle text-info',
                'Partially Returned' => 'bg-warning-subtle text-warning',
                'Returned' => 'bg-success-subtle text-success',
              ][$d->status] ?? 'bg-secondary-subtle text-secondary';
            @endphp
            <span class="badge rounded-pill {{ $badge }}">{{ $d->status }}</span>
          </td>
          <td class="text-muted">{{ $d->dispatch_date->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center py-4 text-muted">No recent dispatches</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
