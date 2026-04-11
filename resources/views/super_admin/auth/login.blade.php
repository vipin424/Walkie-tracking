<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Super Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#1a1f36; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .login-card { background:#fff; border-radius:12px; padding:40px; width:100%; max-width:400px; }
  </style>
</head>
<body>
<div class="login-card shadow">
  <h4 class="mb-1 fw-bold">Super Admin</h4>
  <p class="text-muted mb-4">CrewRent SaaS Panel</p>

  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('super.login.post') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-dark w-100">Login</button>
  </form>
</div>
</body>
</html>
