@php
  function nav_active($pattern){ return request()->routeIs($pattern) ? 'active' : ''; }
@endphp

<aside id="sidebar" class="sidebar shadow-sm">
  <div class="brand d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}" class="brand-text d-flex align-items-center gap-2">
      <img src="{{ asset('image/logo.png') }}" alt="logo" height="100" weight="100">
    </a>
    <button id="sidebarToggle" class="btn btn-sm btn-outline-light d-md-none">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <nav class="menu mt-3">
    <a href="{{ route('dashboard') }}" class="menu-item {{ nav_active('dashboard') }}">
      <i class="bi bi-house-door"></i> <span>Dashboard</span>
    </a>
    <a href="{{ route('clients.index') }}" class="menu-item {{ nav_active('clients.*') }}">
      <i class="bi bi-people"></i> <span>Clients</span>
    </a>
    <a href="{{ route('dispatches.index') }}" class="menu-item {{ nav_active('dispatches.*') }}">
      <i class="bi bi-truck"></i> <span>Dispatches</span>
    </a>
    <!-- <a href="{{ route('invoices.index') }}" class="menu-item {{ nav_active('dispatches.*') }}">
      <i class="bi bi-truck"></i> <span>Invoices</span>
    </a> -->
    <a href="{{ route('payments.index') }}" class="menu-item {{ nav_active('payments.*') }}">
      <i class="bi bi-wallet2"></i> <span>Payments</span>
    </a>
  </nav>
</aside>
