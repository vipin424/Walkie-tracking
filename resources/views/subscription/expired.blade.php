<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Subscription Expired</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f4f6fb; display:flex; align-items:center; justify-content:center; min-height:100vh; }
  </style>
</head>
<body>
<div class="text-center p-5">
  <div class="display-1 text-warning mb-3"><i class="bi bi-exclamation-triangle"></i></div>
  <h2 class="fw-bold">Subscription Expired</h2>
  <p class="text-muted mb-4">Your subscription has expired. Please contact your administrator to renew.</p>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button class="btn btn-outline-secondary">Logout</button>
  </form>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</body>
</html>
