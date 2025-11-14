@extends('layouts.app')
@section('title', 'Invoices')
@section('content')
<div class="container">
    <h3 class="mb-3">Monthly Invoices</h3>

    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <label>Client</label>
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Month</label>
            <input type="month" name="month" class="form-control" value="{{ $month }}">
        </div>

        <div class="col-md-2 align-self-end">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

        <div class="col-md-3 align-self-end">
            <form method="POST" action="{{ route('invoices.generate') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $clientId }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <button class="btn btn-success w-100">Generate Invoice</button>
            </form>
        </div>
    </form>

    <div class="card">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Invoice Code</th>
                    <th>Client</th>
                    <th>Period</th>
                    <th>Total Items</th>
                    <th>Total Days</th>
                    <th>Total Amount</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>{{ $inv->invoice_code }}</td>
                        <td>{{ $inv->client->name }}</td>
                        <td>{{ $inv->start_date->format('d M') }} - {{ $inv->end_date->format('d M Y') }}</td>
                        <td>{{ $inv->total_items }}</td>
                        <td>{{ $inv->total_days }}</td>
                        <td>₹{{ number_format($inv->total_amount,2) }}</td>
                        <td>
                            @if($inv->invoice_path)
                                <a href="{{ asset('storage/'.$inv->invoice_path) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-file-earmark-pdf"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $invoices->links('pagination::bootstrap-5') }}
</div>
@endsection
