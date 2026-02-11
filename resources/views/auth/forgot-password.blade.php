<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password - {{ config('app.name') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  <style>
    :root {
      --primary: #004d40;
      --primary-light: #00695c;
    }
    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      overflow: hidden;
      max-width: 420px;
      width: 100%;
    }
    .login-header {
      background: var(--primary);
      color: #fff;
      padding: 2rem;
      text-align: center;
    }
    .login-header img {
      max-height: 80px;
      margin-bottom: 1rem;
    }
    .login-body {
      padding: 2rem;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(0,77,64,0.15);
    }
    .btn-login {
      background: var(--primary);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-login:hover {
      background: var(--primary-light);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,77,64,0.3);
    }
    .input-group-text {
      background: #f8f9fa;
      border-right: none;
    }
    .form-control {
      border-left: none;
    }
    .alert {
      border-radius: 8px;
      border: none;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <img src="{{ asset('image/logo.png') }}" alt="Logo">
      <h4 class="mb-0">Forgot Password?</h4>
      <small>Reset your password</small>
    </div>
    
    <div class="login-body">
      <p class="text-muted small mb-4">
        No problem. Just enter your email address and we'll send you a password reset link.
      </p>

      @if (session('status'))
        <div class="alert alert-success mb-3">
          <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="mb-4">
          <label class="form-label fw-semibold">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus placeholder="Enter your email">
          </div>
          @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
          <i class="bi bi-send me-2"></i>Send Reset Link
        </button>

        <div class="text-center">
          <a href="{{ route('login') }}" class="small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
