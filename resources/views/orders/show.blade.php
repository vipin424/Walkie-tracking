@extends('layouts.app')
@section('title','Order Details')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Order Details</h4>
            <p class="text-muted mb-0">Order #{{ $order->order_code }}</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <!-- ORDER INFO -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="fw-semibold"><i class="bi bi-info-circle text-warning me-2"></i>Order Information</h5>
        </div>

        <div class="card-body p-4 row g-3">
            <div class="col-md-4">
                <strong>Client:</strong> {{ $order->client->name }}
            </div>

            <div class="col-md-4">
                <strong>Order Date:</strong> {{ $order->order_date }}
            </div>

            <div class="col-md-4">
                <strong>Start Date:</strong> {{ $order->start_date }}
            </div>

            <div class="col-md-4">
                <strong>End Date:</strong> {{ $order->end_date }}
            </div>

            <div class="col-md-4">
                <strong>Event:</strong> {{ $order->event_name ?? '-' }}
            </div>

            <div class="col-md-4">
                <strong>Location:</strong> {{ $order->location ?? '-' }}
            </div>

            <div class="col-md-4">
                <strong>Delivery Type:</strong> {{ ucfirst($order->delivery_type) }}
            </div>

            @if($order->delivery_type == 'delivery')
            <div class="col-md-4">
                <strong>Delivery Charges:</strong> ₹{{ $order->delivery_charges }}
            </div>
            @endif

            <div class="col-md-12">
                <strong>Remarks:</strong> {{ $order->remarks ?? '-' }}
            </div>
        </div>
    </div>

    <!-- ORDER ITEMS -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header p-4 bg-white border-0">
            <h5 class="fw-semibold"><i class="bi bi-box-seam text-success me-2"></i>Order Items</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Item Type</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Qty</th>
                        <th>Rental Type</th>
                        <th>Rate</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->items as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $i->item_type }}</td>
                        <td>{{ $i->brand }}</td>
                        <td>{{ $i->model }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>{{ ucfirst($i->rental_type) }}</td>
                        <td>
                            @if($i->rental_type=='daily')
                                ₹{{ $i->rate_per_day }}
                            @else
                                ₹{{ $i->rate_per_month }}
                            @endif
                        </td>
                        <td>₹{{ $i->total_amount }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAYMENT -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-semibold"><i class="bi bi-cash-coin text-warning me-2"></i>Payment Summary</h5>
        </div>

        <div class="card-body p-4 row">
            <div class="col-md-4">
                <strong>Total Amount:</strong> ₹{{ $order->payment->total_amount }}
            </div>

            <div class="col-md-4">
                <strong>Advance Paid:</strong> ₹{{ $order->payment->advance_paid }}
            </div>

            <div class="col-md-4">
                <strong>Due Amount:</strong> ₹{{ $order->payment->due_amount }}
            </div>
        </div>
    </div>

</div>
@endsection
