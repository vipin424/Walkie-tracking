@extends('layouts.app')
@section('title', isset($subscription) ? 'Edit Subscription' : 'Create Subscription')

@section('content')
<div class="container">
    <h3>{{ isset($subscription) ? 'Edit' : 'Create' }} Monthly Subscription</h3>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ isset($subscription) ? route('subscriptions.update', $subscription) : route('subscriptions.store') }}" id="subscription-form">
        @csrf
        @if(isset($subscription)) @method('PUT') @endif

        <!-- Client Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-person-circle me-2 text-warning"></i>Client Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Select Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        {{ old('client_id', $subscription->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} - {{ $client->contact_number }}{{ $client->company_name ? ' (' . $client->company_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Select client for this subscription</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CC Emails (Optional)</label>
                        <input type="text" name="cc_emails" class="form-control @error('cc_emails') is-invalid @enderror" 
                               placeholder="email1@example.com, email2@example.com"
                               value="{{ old('cc_emails', $subscription->cc_emails ?? '') }}">
                        @error('cc_emails')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Comma-separated emails for automatic invoice CC</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Billing Details (Bill To)</label>
                        <textarea name="billing_details" id="billing_details" class="form-control" rows="6"
                                  placeholder="Enter complete billing details like:&#10;Company Name&#10;GST Number: XXXXXXXXXXXX&#10;Address: Full Address&#10;Phone: XXXXXXXXXX&#10;Email: email@example.com">{{ old('billing_details', $subscription->billing_details ?? '') }}</textarea>
                        <small class="text-muted">These details will appear in invoice as "Bill To" section</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Details Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-calendar-check me-2 text-warning"></i>Billing Configuration
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @if(!isset($subscription))
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Billing Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="billing_start_date" class="form-control @error('billing_start_date') is-invalid @enderror" 
                               value="{{ old('billing_start_date') }}" required>
                        @error('billing_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">First billing cycle will start from this date</small>
                    </div>
                    @endif
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Billing Day of Month (1-28) <span class="text-danger">*</span></label>
                        <input type="number" name="billing_day_of_month" class="form-control @error('billing_day_of_month') is-invalid @enderror" 
                               min="1" max="28" value="{{ old('billing_day_of_month', $subscription->billing_day_of_month ?? 1) }}" required>
                        @error('billing_day_of_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Invoice will be generated on this day every month</small>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Monthly Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="monthly_amount" id="monthly_amount" class="form-control @error('monthly_amount') is-invalid @enderror" 
                                   step="0.01" value="{{ old('monthly_amount', $subscription->monthly_amount ?? '') }}" required readonly>
                        </div>
                        @error('monthly_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Auto-calculated from items below</small>
                    </div>
                    
                    @if(isset($subscription))
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ ($subscription->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ ($subscription->status ?? '') == 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ ($subscription->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-box-seam me-2 text-warning"></i>Subscription Items
                    </h5>
                    <button type="button" id="add-item" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Add Item
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="items-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Item Name <span class="text-danger">*</span></th>
                                <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 120px;">Type</th>
                                <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Description</th>
                                <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 100px;">Quantity <span class="text-danger">*</span></th>
                                <th class="px-3 py-3 text-muted fw-semibold" style="width: 150px;">Rate/Month <span class="text-danger">*</span></th>
                                <th class="px-3 py-3 text-muted fw-semibold text-end" style="width: 150px;">Amount</th>
                                <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            @php 
                                $items = old('items', isset($subscription) ? $subscription->items_json : [['name' => '', 'type' => '', 'description' => '', 'quantity' => 1, 'rate' => 0]]); 
                            @endphp
                            @foreach($items as $i => $item)
                            <tr class="item-row">
                                <td class="px-3 py-2">
                                    <input type="text" name="items[{{ $i }}][name]" class="form-control form-control-sm item-name" 
                                           placeholder="Item Name" value="{{ $item['name'] ?? '' }}" required>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="items[{{ $i }}][type]" class="form-control form-control-sm" 
                                           placeholder="Type" value="{{ $item['type'] ?? '' }}">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" 
                                           placeholder="Description" value="{{ $item['description'] ?? '' }}">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm text-center item-qty" 
                                           placeholder="Qty" value="{{ $item['quantity'] ?? 1 }}" min="1" required>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="items[{{ $i }}][rate]" class="form-control item-rate" 
                                               placeholder="Rate" step="0.01" value="{{ $item['rate'] ?? 0 }}" min="0" required>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-end fw-semibold item-amount">₹0.00</td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-end fw-bold">Total Monthly Amount:</td>
                                <td class="px-3 py-3 text-end fw-bold fs-5 text-warning" id="total-amount">₹0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Terms & Conditions
                </h5>
            </div>
            <div class="card-body p-4">
                <textarea name="notes" class="form-control" rows="4" 
                          placeholder="Add any notes, terms & conditions, or special instructions...">{{ old('notes', $subscription->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle me-2"></i>{{ isset($subscription) ? 'Update' : 'Create' }} Subscription
            </button>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
let itemIndex = {{ count($items) }};

function calculateRow(row) {
    const qty = parseFloat(row.find('.item-qty').val()) || 0;
    const rate = parseFloat(row.find('.item-rate').val()) || 0;
    const amount = qty * rate;
    row.find('.item-amount').text('₹' + amount.toFixed(2));
}

function calculateTotal() {
    let total = 0;
    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.item-qty').val()) || 0;
        const rate = parseFloat($(this).find('.item-rate').val()) || 0;
        total += qty * rate;
        calculateRow($(this));
    });
    $('#total-amount').text('₹' + total.toFixed(2));
    $('#monthly_amount').val(total.toFixed(2));
}

// Autocomplete for item names
function initAutocomplete(input) {
    $(input).autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route('items.search') }}',
                data: { q: request.term },
                success: function(data) {
                    response(data.map(item => ({
                        label: item.name,
                        value: item.name,
                        item: item
                    })));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            const row = $(this).closest('tr');
            const item = ui.item.item;
            
            // Auto-fill fields
            row.find('input[name*="[type]"]').val(item.type || '');
            row.find('input[name*="[description]"]').val(item.description || '');
            row.find('input[name*="[rate]"]').val(item.unit_price || 0);
            
            // Trigger recalculation
            setTimeout(() => calculateTotal(), 100);
        }
    });
}

$(document).ready(function() {
    // Initialize autocomplete for existing rows
    $('.item-name').each(function() {
        initAutocomplete(this);
    });
    
    // Initial calculation
    calculateTotal();

    // Add item
    $('#add-item').click(function() {
        const newRow = `
            <tr class="item-row">
                <td class="px-3 py-2">
                    <input type="text" name="items[${itemIndex}][name]" class="form-control form-control-sm item-name" 
                           placeholder="Item Name" required>
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="items[${itemIndex}][type]" class="form-control form-control-sm" 
                           placeholder="Type">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" 
                           placeholder="Description">
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm text-center item-qty" 
                           placeholder="Qty" value="1" min="1" required>
                </td>
                <td class="px-3 py-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="items[${itemIndex}][rate]" class="form-control item-rate" 
                               placeholder="Rate" step="0.01" value="0" min="0" required>
                    </div>
                </td>
                <td class="px-3 py-2 text-end fw-semibold item-amount">₹0.00</td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#items-container').append(newRow);
        
        // Initialize autocomplete for new row
        initAutocomplete($('#items-container tr:last .item-name')[0]);
        
        itemIndex++;
        calculateTotal();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        if($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        } else {
            alert('At least one item is required!');
        }
    });

    // Recalculate on input change
    $(document).on('input', '.item-qty, .item-rate', function() {
        calculateTotal();
    });
});
</script>

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
  }

  .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
  }

  .form-control-sm {
    font-size: 0.875rem;
  }

  #items-table input[type="number"] {
    -moz-appearance: textfield;
  }

  #items-table input[type="number"]::-webkit-outer-spin-button,
  #items-table input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .card {
    transition: all 0.3s ease;
  }
</style>
@endpush
@endsection
