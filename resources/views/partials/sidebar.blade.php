@php
  function nav_active($pattern){ return request()->routeIs($pattern) ? 'active' : ''; }
@endphp

<aside id="sidebar" class="sidebar shadow-sm">
  <div class="brand d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}" class="brand-text d-flex align-items-center gap-2">
      @if(isset($currentCompany) && $currentCompany->logo)
        <img src="{{ asset('storage/'.$currentCompany->logo) }}" alt="logo" height="40">
      @else
        <img src="{{ asset('image/logo.png') }}" alt="logo" height="40">
      @endif
      @if(isset($currentCompany))
        <span class="text-white fw-semibold small">{{ $currentCompany->name }}</span>
      @endif
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
    <a href="{{ route('items.index') }}" class="menu-item {{ nav_active('items.*') }}">
      <i class="bi bi-box-seam"></i> <span>Items</span>
    </a>
    <a href="{{ route('quotations.index') }}" class="menu-item {{ nav_active('quotations.*') }}">
      <i class="bi bi-receipt"></i> <span>Quotations</span>
    </a>
    <a href="{{ route('orders.index') }}" class="menu-item {{ nav_active('orders.*') }}">
      <i class="bi bi-diagram-2"></i> <span>Orders</span>
    </a>
    <a href="{{ route('subscriptions.index') }}" class="menu-item {{ nav_active('subscriptions.*') }}">
      <i class="bi bi-calendar-check"></i> <span>Monthly Subscriptions</span>
    </a>
    <!-- <a href="{{ route('dispatches.index') }}" class="menu-item {{ nav_active('dispatches.*') }}">
      <i class="bi bi-truck"></i> <span>Dispatches</span>
    </a> -->
    <!-- <a href="{{ route('invoices.index') }}" class="menu-item {{ nav_active('dispatches.*') }}">
      <i class="bi bi-truck"></i> <span>Invoices</span>
    </a> -->
    <a href="{{ route('payments.index') }}" class="menu-item {{ nav_active('payments.*') }}">
      <i class="bi bi-wallet2"></i> <span>Payments</span>
    </a>
    <a href="{{ route('company.settings') }}" class="menu-item {{ nav_active('company.*') }}">
      <i class="bi bi-gear"></i> <span>Company Settings</span>
    </a>
  </nav>
</aside>
