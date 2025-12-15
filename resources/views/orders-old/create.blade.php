@extends('layouts.app')
@section('title','New Order')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Create New Order</h4>
            <p class="text-muted mb-0">Fill the details to create a new order</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <form method="post" action="{{ route('orders.store') }}">
        @csrf

        <!-- ORDER INFO CARD -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-receipt-cutoff text-warning me-2"></i>Order Information
                </h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <!-- Select Client -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select class="form-select shadow-sm" name="client_id" required>
                            <option value="">-- Choose Client --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->contact_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Order Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control shadow-sm" required value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control shadow-sm" required>
                    </div>

                    <!-- End Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control shadow-sm" required>
                    </div>

                    <!-- Event Name -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Event Name</label>
                        <input type="text" name="event_name" class="form-control shadow-sm" placeholder="Optional">
                    </div>

                    <!-- Location -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Location</label>
                        <input type="text" name="location" class="form-control shadow-sm" placeholder="Event / Site Location">
                    </div>

                    <!-- Delivery Type -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Delivery Type <span class="text-danger">*</span></label>
                        <select class="form-select shadow-sm" id="delivery_type" name="delivery_type" onchange="toggleDeliveryCharges()">
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>

                    <!-- Delivery Charges (Hidden by Default) -->
                    <div class="col-md-4" id="delivery_charges_box" style="display:none;">
                        <label class="form-label fw-semibold">Delivery Charges</label>
                        <input type="number" step="0.01" name="delivery_charges" class="form-control shadow-sm" placeholder="Enter amount">
                    </div>

                    <!-- Remarks -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea name="remarks" class="form-control shadow-sm" rows="2"></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- ORDER ITEMS CARD -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam text-success me-2"></i>Order Items
                </h5>

                <button type="button" onclick="addItem()" class="btn btn-warning">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </button>
            </div>

            <div class="card-body p-4" style="background:#fafafa;">
                <div id="items"></div>

                <div id="emptyState" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 opacity-50"></i>
                    <p>No items added yet. Click “Add Item”.</p>
                </div>
            </div>
        </div>

        <!-- PAYMENT CARD -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header p-4 bg-white border-0">
                <h5 class="fw-semibold">
                    <i class="bi bi-cash-coin text-warning me-2"></i>Payment Details
                </h5>
            </div>
            <div class="card-body p-4 row g-4">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Advance Amount</label>
                    <input type="number" step="0.01" name="advance_paid" class="form-control shadow-sm" placeholder="0.00">
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

        <!-- SUBMIT BUTTONS -->
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button class="btn btn-warning px-4">
                <i class="bi bi-check-circle me-2"></i>Create Order
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    document.getElementById('emptyState').style.display = 'none';

    let html = `
    <div class="card shadow-sm border-0 mb-3 item-row" style="animation: slideIn 0.3s;">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
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
                    <select name="items[${itemIndex}][item_type]" class="form-select" required>
                        <option value="Walkie">Walkie</option>
                        <option value="Charger">Charger</option>
                        <option value="Battery">Battery</option>
                        <option value="Headphone">Headphone</option>
                        <option value="Accessory">Accessory</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Brand</label>
                    <input class="form-control" name="items[${itemIndex}][brand]" placeholder="Brand">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Model</label>
                    <input class="form-control" name="items[${itemIndex}][model]" placeholder="Model">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Qty</label>
                    <input type="number" class="form-control" min="1" name="items[${itemIndex}][quantity]" required>
                </div>

                <!-- Rental Type -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Rental Type</label>
                    <select class="form-select rental-type" name="items[${itemIndex}][rental_type]" onchange="toggleRate(this)">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <!-- Rate Per Day -->
                <div class="col-md-2 rate-day">
                    <label class="form-label small fw-semibold">Rate/Day</label>
                    <input type="number" class="form-control" name="items[${itemIndex}][rate_per_day]" placeholder="0.00" required>
                </div>

                <!-- Rate Per Month -->
                <div class="col-md-2 rate-month" style="display:none;">
                    <label class="form-label small fw-semibold">Rate/Month</label>
                    <input type="number" class="form-control" name="items[${itemIndex}][rate_per_month]" placeholder="0.00">
                </div>

            </div>

        </div>
    </div>
    `;

    document.getElementById('items').insertAdjacentHTML('beforeend', html);
    itemIndex++;
}

function removeItem(btn) {
    let card = btn.closest('.item-row');
    card.style.animation = 'slideOut .3s';

    setTimeout(() => {
        card.remove();
        if (document.querySelectorAll('.item-row').length === 0) {
            document.getElementById('emptyState').style.display = 'block';
        }
    }, 300);
}

function toggleRate(select) {
    let card = select.closest('.item-row');
    let dayRate = card.querySelector('.rate-day');
    let monthRate = card.querySelector('.rate-month');

    if (select.value === "monthly") {
        dayRate.style.display = 'none';
        monthRate.style.display = 'block';
    } else {
        monthRate.style.display = 'none';
        dayRate.style.display = 'block';
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
