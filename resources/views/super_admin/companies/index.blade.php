@extends('super_admin.layout')
@section('title','Companies')

@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0">All Companies</h5>
  <a href="{{ route('super.companies.create') }}" class="btn btn-primary btn-sm">+ New Company</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Name</th><th>Email</th><th>Plan</th><th>Expires</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($companies as $company)
        <tr>
          <td>
            @if($company->logo)
              <img src="{{ asset('storage/'.$company->logo) }}" height="24" class="me-2 rounded">
            @endif
            {{ $company->name }}
          </td>
          <td>{{ $company->email }}</td>
          <td>{{ $company->plan?->name ?? '—' }}</td>
          <td>{{ $company->subscription_expires_at?->format('d M Y') ?? '∞' }}</td>
          <td>
            <span class="badge bg-{{ $company->status === 'active' ? 'success' : ($company->status === 'suspended' ? 'warning' : 'danger') }}">
              {{ ucfirst($company->status) }}
            </span>
          </td>
          <td class="d-flex gap-1">
            <a href="{{ route('super.companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('super.companies.toggle-status', $company) }}">
              @csrf
              <button class="btn btn-sm btn-outline-{{ $company->status === 'active' ? 'danger' : 'success' }}">
                {{ $company->status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
            <!-- <form method="POST" action="{{ route('super.companies.destroy', $company) }}" onsubmit="return confirm('Delete this company?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form> -->
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No companies yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $companies->links() }}</div>
@endsection
