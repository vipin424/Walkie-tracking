<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','Super Admin') — CrewRent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body { display:flex; min-height:100vh; background:#f4f6fb; }
    .sa-sidebar { width:240px; background:#1a1f36; color:#fff; flex-shrink:0; min-height:100vh; padding:0; }
    .sa-sidebar .brand { padding:20px 16px; font-size:1.1rem; font-weight:700; border-bottom:1px solid #2d3354; }
    .sa-sidebar .nav-link { color:#adb5bd; padding:10px 16px; display:flex; align-items:center; gap:10px; }
    .sa-sidebar .nav-link:hover, .sa-sidebar .nav-link.active { color:#fff; background:#2d3354; border-radius:6px; }
    .sa-main { flex:1; display:flex; flex-direction:column; }
    .sa-topbar { background:#fff; border-bottom:1px solid #e0e0e0; padding:12px 24px; display:flex; justify-content:space-between; align-items:center; }
    .sa-content { padding:24px; flex:1; }
  </style>
</head>
<body>
<aside class="sa-sidebar">
  <div class="brand"><i class="bi bi-shield-lock me-2"></i>Super Admin</div>
  <nav class="mt-2 px-2">
    <a href="{{ route('super.dashboard') }}" class="nav-link @if(request()->routeIs('super.dashboard')) active @endif">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('super.companies.index') }}" class="nav-link @if(request()->routeIs('super.companies.*')) active @endif">
      <i class="bi bi-building"></i> Companies
    </a>
    <a href="{{ route('super.plans.index') }}" class="nav-link @if(request()->routeIs('super.plans.*')) active @endif">
      <i class="bi bi-layers"></i> Plans
    </a>
    <a href="{{ route('super.billing.index') }}" class="nav-link @if(request()->routeIs('super.billing.*')) active @endif">
      <i class="bi bi-receipt"></i> Billing
    </a>
  </nav>
</aside>

<div class="sa-main">
  <div class="sa-topbar">
    <strong>@yield('title','Dashboard')</strong>
    <form method="POST" action="{{ route('super.logout') }}">
      @csrf
      <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
    </form>
  </div>
  <div class="sa-content">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())
      <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
      </div>
    @endif
    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
