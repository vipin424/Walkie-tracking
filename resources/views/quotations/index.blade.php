@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Quotations <a href="{{ route('quotations.create') }}" class="btn btn-primary float-right">Create Quotation</a></h3>

    <form method="GET" class="mb-3">
        <input type="text" name="search" placeholder="Search code / client" value="{{ request('search') }}" class="form-control" />
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Client</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotations as $q)
            <tr>
                <td>{{ $q->code }}</td>
                <td>{{ $q->client_name }}<br><small>{{ $q->client_phone }}</small></td>
                <td>₹{{ number_format($q->total_amount,2) }}</td>
                <td>{{ ucfirst($q->status) }}</td>
                <td>{{ $q->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('quotations.edit', $q) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('quotations.destroy', $q) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $quotations->links() }}
</div>
@endsection
