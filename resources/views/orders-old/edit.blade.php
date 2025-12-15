@extends('layouts.app')
@section('title','Edit Order')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Order</h4>
            <p class="text-muted mb-0">Update order details</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <form method="post" action="{{ route('orders.update', $order->id) }}">
        @csrf
        @method('PUT')

        <!-- ORDER INFO -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-info-circle text-warning me-2"></i>Order Information
                </h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <!-- Client -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select class="form-select shadow-sm" name="client_id" required>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ $order->client_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->contact_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Order Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control shadow-sm" required
                               value="{{ $order->order_date }}">
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control shadow-sm" required
                               value="{{ $order->start_date }}">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control shadow-sm" required
                               value="{{ $order->end_date }}">
                    </div>

                    <!-- Event Name -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Event Name</label>
                        <input type="text" name="event_name" class="form-control shadow-sm"
                               value="{{ $order->event_name }}">
                    </div>

                    <!-- Location -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Location</label>
                        <input type="text" name="location" class="form-control shadow-sm"
                               value="{{ $order->location }}">
                    </div>

                    <!-- Delivery Type -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Delivery Type</label>
                        <select id="delivery_type" name="delivery_type" class="form-select shadow-sm" onchange="toggleDeliveryCharges()">
                            <option value="pickup" {{ $order->delivery_type == 'pickup' ? 'selected' : '' }}>Pickup</option>
                            <option value="delivery" {{ $order->delivery_type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    <!-- Delivery Charges -->
                    <div class="col-md-4" id="delivery_charges_box"
                         style="{{ $order->delivery_type == 'delivery' ? '' : 'display:none;' }}">
                        <label class="form-label fw-semibold">Delivery Charges</label>
                        <input type="number" name="delivery_charges" class="form-control shadow-sm"
                               value="{{ $order->delivery_charges }}">
                    </div>

                    <!-- Remarks -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea name="remarks" class="form-control shadow-sm" rows="2">{{ $order->remarks }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- ORDER ITEMS -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam text-success me-2"></i>Order Items
                </h5>
                <button type="button" class="btn btn-warning" onclick="addItem()">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </button>
            </div>

            <div class="card-body p-4" style="background:#fafafa;">
                <div id="items">
                    @foreach ($order->items as $idx => $i)
                        <div class="card shadow-sm border-0 mb-3 item-row" style="animation:slideIn .3s;">
                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between mb-3">
                                    <h6 class="fw-semibold text-secondary mb-0">
                                        <i class="bi bi-box me-2"></i>Item #{{ $idx+1 }}
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="row g-3">

                                    <!-- Item Type -->
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Item Type</label>
                                        <select name="items[{{ $idx }}][item_type]" class="form-select">
                                            <option value="Walkie" {{ $i->item_type=='Walkie' ? 'selected':'' }}>Walkie</option>
                                            <option value="Charger" {{ $i->item_type=='Charger' ? 'selected':'' }}>Charger</option>
                                            <option value="Battery" {{ $i->item_type=='Battery' ? 'selected':'' }}>Battery</option>
                                            <option value="Headphone" {{ $i->item_type=='Headphone' ? 'selected':'' }}>Headphone</option>
                                            <option value="Accessory" {{ $i->item_type=='Accessory' ? 'selected':'' }}>Accessory</option>
                                        </select>
                                    </div>

                                    <!-- Brand -->
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Brand</label>
                                        <input class="form-control" name="items[{{ $idx }}][brand]"
                                               value="{{ $i->brand }}">
                                    </div>

                                    <!-- Model -->
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Model</label>
                                        <input class="form-control" name="items[{{ $idx }}][model]"
                                               value="{{ $i->model }}">
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Qty</label>
                                        <input type="number" min="1" class="form-control"
                                               name="items[{{ $idx }}][quantity]"
                                               value="{{ $i->quantity }}">
                                    </div>

                                    <!-- Rental Type -->
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Rental Type</label>
                                        <select class="form-select rental-type"
                                                name="items[{{ $idx }}][rental_type]"
                                                onchange="toggleRate(this)">
                                            <option value="daily" {{ $i->rental_type=='daily'?'selected':'' }}>Daily</option>
                                            <option value="monthly" {{ $i->rental_type=='monthly'?'selected':'' }}>Monthly</option>
                                        </select>
                                    </div>

                                    <!-- Rate Per Day -->
                                    <div class="col-md-2 rate-day" style="{{ $i->rental_type=='monthly'?'display:none':'' }}">
                                        <label class="form-label small fw-semibold">Rate/Day</label>
                                        <input type="number" name="items[{{ $idx }}][rate_per_day]"
                                               class="form-control"
                                               value="{{ $i->rate_per_day }}">
                                    </div>

                                    <!-- Rate Per Month -->
                                    <div class="col-md-2 rate-month" style="{{ $i->rental_type=='daily'?'display:none':'' }}">
                                        <label class="form-label small fw-semibold">Rate/Month</label>
                                        <input type="number" name="items[{{ $idx }}][rate_per_month]"
                                               class="form-control"
                                               value="{{ $i->rate_per_month }}">
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PAYMENT SECTION -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header p-4 bg-white border-0">
                <h5 class="fw-semibold">
                    <i class="bi bi-cash-coin text-warning me-2"></i>Payment Details
                </h5>
            </div>

            <div class="card-body p-4 row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Advance Amount</label>
                    <input type="number" step="0.01" name="advance_paid" class="form-control shadow-sm"
                           value="{{ $order->payment->advance_paid }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Payment Mode</label>
                    <select name="payment_mode" class="form-select shadow-sm">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SUBMIT -->
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button class="btn btn-warning px-4">
                <i class="bi bi-check-circle me-2"></i>Update Order
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ count($order->items) }};

function addItem() {
    let html = `
    <div class="card shadow-sm border-0 mb-3 item-row" style="animation:slideIn .3s;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-semibold text-secondary mb-0">
                    <i class="bi bi-box me-2"></i>Item #${itemIndex + 1}
                </h6>
                <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Item Type</label>
                    <select name="items[${itemIndex}][item_type]" class="form-select">
                        <option value="Walkie">Walkie</option>
                        <option value="Charger">Charger</option>
                        <option value="Battery">Battery</option>
                        <option value="Headphone">Headphone</option>
                        <option value="Accessory">Accessory</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Brand</label>
                    <input class="form-control" name="items[${itemIndex}][brand]">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Model</label>
                    <input class="form-control" name="items[${itemIndex}][model]">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Qty</label>
                    <input type="number" min="1" class="form-control" name="items[${itemIndex}][quantity]">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Rental Type</label>
                    <select class="form-select" name="items[${itemIndex}][rental_type]" onchange="toggleRate(this)">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div class="col-md-2 rate-day">
                    <label class="form-label small fw-semibold">Rate/Day</label>
                    <input type="number" class="form-control" name="items[${itemIndex}][rate_per_day]">
                </div>

                <div class="col-md-2 rate-month" style="display:none;">
                    <label class="form-label small fw-semibold">Rate/Month</label>
                    <input type="number" class="form-control" name="items[${itemIndex}][rate_per_month]">
                </div>

            </div>

        </div>
    </div>`;

    document.getElementById("items").insertAdjacentHTML("beforeend", html);
    itemIndex++;
}

function removeItem(btn) {
    let card = btn.closest(".item-row");
    card.style.animation = "slideOut .3s";

    setTimeout(() => card.remove(), 300);
}

function toggleRate(select) {
    let card = select.closest(".item-row");
    let dayRate = card.querySelector(".rate-day");
    let monthRate = card.querySelector(".rate-month");

    if (select.value === "monthly") {
        dayRate.style.display = "none";
        monthRate.style.display = "block";
    } else {
        monthRate.style.display = "none";
        dayRate.style.display = "block";
    }
}

function toggleDeliveryCharges() {
    let type = document.getElementById("delivery_type").value;
    document.getElementById("delivery_charges_box").style.display =
        type === "delivery" ? "block" : "none";
}
</script>

<style>
@keyframes slideIn { from{opacity:0; transform:translateY(-8px);} to{opacity:1;} }
@keyframes slideOut { from{opacity:1;} to{opacity:0; transform:translateY(-8px);} }
</style>
@endpush

@endsection
