@extends('layouts.app')
@section('title','Orders')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Order List</h4>
            <p class="text-muted mb-0">All customer orders</p>
        </div>

        <a href="{{ route('orders.create') }}" class="btn btn-warning shadow-sm px-4">
            <i class="bi bi-plus-circle me-2"></i>Create Order
        </a>
    </div>

    <!-- FILTER BAR -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3">

                <!-- Client Filter -->
                <div class="col-md-4">
                    <select name="client" class="form-select">
                        <option value="">Filter by Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Filter by Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="col-md-4">
                    <button class="btn btn-warning w-100">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Order Code</th>
                            <th>Client</th>
                            <th>Order Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ ($orders->currentPage()-1)*$orders->perPage() + $loop->iteration }}</td>

                                <td class="fw-semibold">{{ $order->order_code }}</td>

                                <td>{{ $order->client->name }}</td>

                                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>

                                <td>{{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }}</td>

                                <td>{{ \Carbon\Carbon::parse($order->end_date)->format('d M Y') }}</td>

                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($order->status == 'approved')
                                        <span class="badge bg-primary">Approved</span>
                                    @elseif($order->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($order->status == 'dispatched')
                                        <span class="badge bg-info text-dark">Dispatched</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>

                                <td class="text-end">

                                    {{-- Show --}}
                                    <a href="{{ route('orders.show', $order->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('orders.edit', $order->id) }}"
                                    class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Approve / Reject only when pending --}}
                                    @if($order->status == 'pending')

                                        {{-- Approve --}}
                                        <form action="{{ route('orders.approve', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        {{-- Reject --}}
                                        <form action="{{ route('orders.reject', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>

                                    @endif
                                    {{-- Convert To Dispatch (Only if approved) --}}
                                    @if($order->status == 'approved')
                                        <a href="javascript:void(0);"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="confirmDispatch('{{ route('orders.convertToDispatch', $order->id) }}')">
                                            <i class="bi bi-truck"></i>
                                        </a>
                                    @endif
                                    {{-- Delete --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete('{{ route('orders.destroy', $order->id) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 opacity-50"></i>
                                    <p class="mt-2">No orders found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <!-- PAGINATION -->
        @if($orders->hasPages())
            <div class="card-footer p-4 bg-white border-0">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
@push('scripts')
<script>
function confirmDispatch(url) {
    Swal.fire({
        title: "Convert to Dispatch?",
        text: "Are you sure you want to convert this order into a dispatch?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Convert",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#f1c40f",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}


function confirmDelete(url) {
    Swal.fire({
        title: "Are you sure?",
        text: "This record will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e3342f",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit a form dynamically
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            let token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';

            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(token);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
