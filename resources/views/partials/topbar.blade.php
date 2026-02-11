<header class="topbar d-flex align-items-center justify-content-between px-3 shadow-sm">
  <div class="d-flex align-items-center">
    <button id="sidebarToggleMobile" class="btn btn-light btn-sm me-2 d-md-none">
      <i class="bi bi-list"></i>
    </button>
    <button id="sidebarToggleDesktop" class="btn btn-light btn-sm me-2 d-none d-md-inline">
      <i class="bi bi-list"></i>
    </button>
    <h6 class="m-0 fw-semibold text-secondary">@yield('title','Dashboard')</h6>
  </div>

  <div class="dropdown">
    <button class="btn btn-light border dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
      <i class="bi bi-person-circle me-2 text-primary"></i>
      {{ auth()->user()->name ?? 'Admin' }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
      <li><h6 class="dropdown-header">Account</h6></li>
      <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item text-danger">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </div>
</header>
