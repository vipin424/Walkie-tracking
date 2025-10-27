<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','Walkie Tracking')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
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
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  @stack('scripts')
</body>
</html>
