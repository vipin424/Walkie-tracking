<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') — {{ $currentCompany->name ?? 'CrewRent' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
  @if(isset($currentCompany))
  <style>
    :root {
      --brand-primary:   {{ $currentCompany->primary_color }};
      --brand-secondary: {{ $currentCompany->secondary_color }};
    }
    .sidebar { background: var(--brand-primary) !important; }
    .menu-item.active, .menu-item:hover { background: var(--brand-secondary) !important; }
    .btn-primary { background-color: var(--brand-primary); border-color: var(--brand-primary); }
    .btn-primary:hover { filter: brightness(0.9); }
  </style>
  @endif
  @stack('styles')
</head>
<body>
  {{-- Sidebar --}}
  @include('partials.sidebar')

  {{-- Main content area --}}
  <div id="content" class="content-wrapper">
    {{-- Topbar --}}
    @include('partials.topbar')

    <main class="p-3 p-md-4">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('plan_limit_reached'))
        <div class="alert alert-warning d-flex align-items-center justify-content-between">
          <span><i class="bi bi-exclamation-triangle me-2"></i>{{ session('plan_limit_reached') }}</span>
          <a href="{{ route('company.settings') }}" class="btn btn-sm btn-warning">View Plan</a>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <strong>Validation Errors:</strong>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

  @stack('scripts')
</body>
</html>
