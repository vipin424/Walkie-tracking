@extends('layouts.app')
@section('title','Convert Order to Dispatch')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Convert to Dispatch</h4>
            <p class="text-muted mb-0">Order → Dispatch auto-prefilled for quick processing</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <form method="post" action="{{ route('dispatches.store') }}">
        @csrf

        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <!-- DISPATCH INFO -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-info-circle me-2 text-warning"></i>Dispatch Information
                </h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <!-- Select Client -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Select Client
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-warning bg-opacity-10 border-0">
                                <i class="bi bi-person-fill text-warning"></i>
                            </span>
                            <select name="client_id" class="form-select border-0 shadow-sm" required>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}"
                                        {{ $order->client_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->contact_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dispatch Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Dispatch Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-info bg-opacity-10 border-0">
                                <i class="bi bi-calendar-event text-info"></i>
                            </span>
                            <input type="date" name="dispatch_date"
                                   class="form-control border-0 shadow-sm"
                                   required value="{{ $order->start_date }}">
                        </div>
                    </div>

                    <!-- Return Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Expected Return Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-warning bg-opacity-10 border-0">
                                <i class="bi bi-calendar-check text-warning"></i>
                            </span>
                            <input type="date" name="expected_return_date"
                                   class="form-control border-0 shadow-sm"
                                   value="{{ $order->end_date }}">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam text-success me-2"></i>Dispatch Items
                </h5>
                <button type="button" class="btn btn-warning" onclick="addItem()">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </button>
            </div>

            <div class="card-body p-4" style="background:#f8f9fa;">
                <div id="items">
                    @foreach ($order->items as $idx => $i)
                        <div class="card border-0 shadow-sm mb-3 item-row" style="animation: slideIn 0.3s;">
                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between align-items-center mb-3">
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
                                            <option value="Walkie" {{ $i->item_type=='Walkie'?'selected':'' }}>Walkie</option>
                                            <option value="Charger" {{ $i->item_type=='Charger'?'selected':'' }}>Charger</option>
                                            <option value="Battery" {{ $i->item_type=='Battery'?'selected':'' }}>Battery</option>
                                            <option value="Headphone" {{ $i->item_type=='Headphone'?'selected':'' }}>Headphone</option>
                                            <option value="Accessory" {{ $i->item_type=='Accessory'?'selected':'' }}>Accessory</option>
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
                                        <label class="form-label small fw-semibold">Quantity</label>
                                        <input type="number" class="form-control" name="items[{{ $idx }}][quantity]"
                                               min="1" value="{{ $i->quantity }}">
                                    </div>

                                    <!-- Rate Per Day -->
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Rate/Day</label>
                                        <input type="number" class="form-control"
                                               name="items[{{ $idx }}][rate_per_day]"
                                               value="{{ $i->rate_per_day }}">
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="emptyState" class="text-center text-muted py-5" style="{{ count($order->items) ? 'display:none;' : '' }}">
                    <i class="bi bi-inbox display-4 d-block mb-2 opacity-50"></i>
                    <p>No Items Yet</p>
                </div>
            </div>
        </div>

        <!-- SUBMIT -->
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-warning px-4 shadow">
                <i class="bi bi-check-circle me-2"></i>Create Dispatch
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ count($order->items) }};

function addItem() {
    document.getElementById('emptyState').style.display = 'none';

    let html = `
    <div class="card border-0 shadow-sm mb-3 item-row" style="animation:slideIn 0.3s;">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold text-secondary mb-0">
                    <i class="bi bi-box me-2"></i>Item #${itemIndex + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Item Type</label>
                    <select class="form-select" name="items[${itemIndex}][item_type]">
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
                    <label class="form-label small fw-semibold">Quantity</label>
                    <input type="number" class="form-control" name="items[${itemIndex}][quantity]" min="1">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Rate/Day</label>
                    <input type="number" class="form-control"
                           name="items[${itemIndex}][rate_per_day]">
                </div>

            </div>

        </div>
    </div>`;
    
    document.getElementById("items").insertAdjacentHTML("beforeend", html);
    itemIndex++;
}

function removeItem(btn) {
    let row = btn.closest('.item-row');
    row.style.animation = "slideOut 0.3s ease";
    setTimeout(() => {
        row.remove();
        if (document.querySelectorAll('.item-row').length === 0) {
            document.getElementById('emptyState').style.display = 'block';
        }
    }, 300);
}
</script>

<style>
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes slideOut {
    from { opacity: 1; transform: translateY(0); }
    to   { opacity: 0; transform: translateY(-10px); }
}
</style>
@endpush
@endsection
