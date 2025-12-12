@php
    $items = old('items') ?: ($quotation ? $quotation->items->toArray() : []);
@endphp

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
        <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
        <input name="client_name" class="form-control" value="{{ old('client_name', $quotation->client_name ?? '') }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Client Email</label>
        <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $quotation->client_email ?? '') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Client Phone</label>
        <input name="client_phone" class="form-control" value="{{ old('client_phone', $quotation->client_phone ?? '') }}">
      </div>
    </div>

    <div class="row g-3 mt-2">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Event From</label>
        <input type="date" name="event_from" class="form-control" 
        value="{{ old('event_from', $quotation && $quotation->event_from ? \Carbon\Carbon::parse($quotation->event_from)->format('Y-m-d') : '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Notes</label>
        <textarea name="notes" class="form-control" rows="1">{{ old('notes', $quotation->notes ?? '') }}</textarea>
      </div>
    </div>
  </div>
</div>

<!-- Items Card -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-0 p-4">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-box-seam me-2 text-warning"></i>Quotation Items
      </h5>
      <button type="button" id="add-row" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-2"></i>Add Item
      </button>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="items-table">
        <thead class="bg-light">
          <tr>
            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Item Name</th>
            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 120px;">Type</th>
            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Description</th>
            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 100px;">Qty</th>
            <th class="px-3 py-3 text-muted fw-semibold" style="width: 120px;">Unit Price</th>
            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 100px;">Tax %</th>
            <th class="px-3 py-3 text-muted fw-semibold text-end" style="width: 120px;">Total</th>
            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 60px;"></th>
          </tr>
        </thead>
        <tbody>
          @if(count($items))
            @foreach($items as $i => $it)
            <tr class="item-row">
              <td class="px-3 py-2"><input name="items[{{ $i }}][item_name]" class="form-control form-control-sm item-name" value="{{ $it['item_name'] ?? '' }}"></td>
              <td class="px-3 py-2"><input name="items[{{ $i }}][item_type]" class="form-control form-control-sm" value="{{ $it['item_type'] ?? '' }}"></td>
              <td class="px-3 py-2"><input name="items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $it['description'] ?? '' }}"></td>
              <td class="px-3 py-2"><input name="items[{{ $i }}][quantity]" type="number" step="1" class="form-control form-control-sm qty text-center" value="{{ $it['quantity'] ?? 1 }}"></td>
              <td class="px-3 py-2"><input name="items[{{ $i }}][unit_price]" type="number" step="0.01" class="form-control form-control-sm unit" value="{{ $it['unit_price'] ?? 0 }}"></td>
              <td class="px-3 py-2"><input name="items[{{ $i }}][tax_percent]" type="number" step="0.01" class="form-control form-control-sm tax text-center" value="{{ $it['tax_percent'] ?? 0 }}"></td>
              <td class="px-3 py-2 line_total text-end fw-semibold">₹0.00</td>
              <td class="px-3 py-2 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            @endforeach
          @else
            <tr class="item-row">
              <td class="px-3 py-2"><input name="items[0][item_name]" class="form-control form-control-sm item-name"></td>
              <td class="px-3 py-2"><input name="items[0][item_type]" class="form-control form-control-sm"></td>
              <td class="px-3 py-2"><input name="items[0][description]" class="form-control form-control-sm"></td>
              <td class="px-3 py-2"><input name="items[0][quantity]" type="number" step="1" class="form-control form-control-sm qty text-center" value="1"></td>
              <td class="px-3 py-2"><input name="items[0][unit_price]" type="number" step="0.01" class="form-control form-control-sm unit" value="0"></td>
              <td class="px-3 py-2"><input name="items[0][tax_percent]" type="number" step="0.01" class="form-control form-control-sm tax text-center" value="0"></td>
              <td class="px-3 py-2 line_total text-end fw-semibold">₹0.00</td>
              <td class="px-3 py-2 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Summary Card -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-0 p-4">
    <h5 class="mb-0 fw-semibold">
      <i class="bi bi-calculator me-2 text-warning"></i>Summary
    </h5>
  </div>
  <div class="card-body p-4">
    <div class="row">
      <div class="col-md-6 offset-md-6">
        <div class="bg-light rounded p-4">
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Subtotal:</span>
            <span class="fw-semibold">₹ <span id="subtotal">0.00</span></span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Tax (calculated):</span>
            <span class="fw-semibold">₹ <span id="tax_amount">0.00</span></span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="text-muted mb-0">Discount:</label>
            <div class="input-group" style="width: 150px;">
              <span class="input-group-text">₹</span>
              <input type="number" step="0.01" name="discount_amount" id="discount" class="form-control" value="{{ old('discount_amount', $quotation->discount_amount ?? 0) }}">
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex justify-content-between">
            <span class="fs-5 fw-bold">Total Amount:</span>
            <span class="fs-4 fw-bold text-warning">₹ <span id="total_amount">0.00</span></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Action Buttons Card (Optional - for after save actions) -->
<div class="card border-0 shadow-sm mt-4">
  <div class="card-header bg-white border-0 p-4">
    <h5 class="mb-0 fw-semibold">
      <i class="bi bi-send me-2 text-warning"></i>Additional Actions
    </h5>
  </div>
  <div class="card-body p-4">
    <div class="alert alert-info border-0 mb-3">
      <i class="bi bi-info-circle me-2"></i>
      These actions will be available after saving the quotation.
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" id="generate-pdf" class="btn btn-outline-primary" disabled>
        <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
      </button>
      <button type="button" id="send-email" class="btn btn-outline-success" disabled>
        <i class="bi bi-envelope me-2"></i>Send Email
      </button>
      <button type="button" id="send-whatsapp" class="btn btn-outline-info" disabled>
        <i class="bi bi-whatsapp me-2"></i>Send WhatsApp
      </button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    function recalcRow(row){
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const unit = parseFloat(row.querySelector('.unit').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const total = qty * unit;
        const taxAmount = total * (tax/100);
        row.querySelector('.line_total').innerText = '₹' + (total + taxAmount).toFixed(2);
        // Store tax amount as data attribute for later calculation
        row.setAttribute('data-tax-amount', taxAmount.toFixed(2));
        row.setAttribute('data-base-amount', total.toFixed(2));
    }

    function recalcAll(){
        let subtotal = 0;
        let totalTax = 0;
        
        document.querySelectorAll('.item-row').forEach(r=>{
            recalcRow(r);
            const baseAmount = parseFloat(r.getAttribute('data-base-amount')) || 0;
            const taxAmount = parseFloat(r.getAttribute('data-tax-amount')) || 0;
            subtotal += baseAmount;
            totalTax += taxAmount;
        });
        
        const discount = parseFloat(document.querySelector('#discount').value) || 0;
        const total = subtotal + totalTax - discount;
        
        document.querySelector('#subtotal').innerText = subtotal.toFixed(2);
        document.querySelector('#tax_amount').innerText = totalTax.toFixed(2);
        document.querySelector('#total_amount').innerText = total.toFixed(2);
    }

    // Initial calc
    recalcAll();

    // Live update events
    document.addEventListener('input', function(e){
        if (e.target.matches('.qty') || e.target.matches('.unit') || e.target.matches('.tax') || e.target.matches('#discount')) {
            recalcAll();
        }
    });

    // Add row
    document.getElementById('add-row').addEventListener('click', function(){
        const tbody = document.querySelector('#items-table tbody');
        const index = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.classList.add('item-row');
        tr.innerHTML = `
            <td class="px-3 py-2"><input name="items[${index}][item_name]" class="form-control form-control-sm item-name"></td>
            <td class="px-3 py-2"><input name="items[${index}][item_type]" class="form-control form-control-sm"></td>
            <td class="px-3 py-2"><input name="items[${index}][description]" class="form-control form-control-sm"></td>
            <td class="px-3 py-2"><input name="items[${index}][quantity]" type="number" step="1" class="form-control form-control-sm qty text-center" value="1"></td>
            <td class="px-3 py-2"><input name="items[${index}][unit_price]" type="number" step="0.01" class="form-control form-control-sm unit" value="0"></td>
            <td class="px-3 py-2"><input name="items[${index}][tax_percent]" type="number" step="0.01" class="form-control form-control-sm tax text-center" value="0"></td>
            <td class="px-3 py-2 line_total text-end fw-semibold">₹0.00</td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        recalcAll();
    });

    // Remove row
    document.addEventListener('click', function(e){
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                // Re-index names
                document.querySelectorAll('#items-table tbody tr').forEach((tr, i) => {
                    tr.querySelectorAll('input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            const newName = name.replace(/items\[\d+\]/, `items[${i}]`);
                            input.setAttribute('name', newName);
                        }
                    });
                });
                recalcAll();
            } else {
                alert('At least one item is required!');
            }
        }
    });

    // Info buttons
    document.getElementById('generate-pdf').addEventListener('click', function(){
        alert('Please save the quotation first. After saving, you can generate PDF from the quotation details page.');
    });

    document.getElementById('send-email').addEventListener('click', function(){
        alert('Please save the quotation first. After saving, you can send email from the quotation details page.');
    });

    document.getElementById('send-whatsapp').addEventListener('click', function(){
        alert('Please save the quotation first. After saving, you can send WhatsApp message from the quotation details page.');
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