@php
    $items = old('items') ?: ($order ? $order->items->toArray() : []);
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
        <input name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $order->client_name ?? '') }}" required>
        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Client Email</label>
        <input type="email" name="client_email" class="form-control @error('client_email') is-invalid @enderror" value="{{ old('client_email', $order->client_email ?? '') }}">
        @error('client_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Client Phone <span class="text-danger">*</span></label>
        <input type="tel" name="client_phone" class="form-control @error('client_phone') is-invalid @enderror" value="{{ old('client_phone', $order->client_phone ?? '') }}" pattern="[0-9]{10}" maxlength="10" required>
        @error('client_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="col-md-12">
        <label class="form-label fw-semibold">Bill To</label>
        <textarea
            name="bill_to"
            id="bill_editor"
            class="form-control"
            rows="4"
        >{{ old('bill_to', $order->bill_to ?? '') }}</textarea>
    </div>

    <div class="row g-3 mt-2">

      <div class="col-md-4">
          <label class="form-label fw-semibold">Event From <span class="text-danger">*</span></label>
          <input type="date" name="event_from" id="event_from" class="form-control @error('event_from') is-invalid @enderror"
          value="{{ old('event_from', $order && $order->event_from ? \Carbon\Carbon::parse($order->event_from)->format('Y-m-d') : '') }}" required>
          @error('event_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-4">
          <label class="form-label fw-semibold">Event To <span class="text-danger">*</span></label>
          <input type="date" name="event_to" id="event_to" class="form-control @error('event_to') is-invalid @enderror"
          value="{{ old('event_to', $order && $order->event_to ? \Carbon\Carbon::parse($order->event_to)->format('Y-m-d') : '') }}" required>
          @error('event_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-4">
          <label class="form-label fw-semibold">Event Time</label>
          <input type="time" name="event_time" class="form-control"
          value="{{ old('event_time', $order->event_time ?? '') }}">
      </div>

      <div class="col-md-8">
          <label class="form-label fw-semibold">Event Location</label>
          <input type="text" name="event_location" class="form-control" placeholder="Enter event location"
          value="{{ old('event_location', $order->event_location ?? '') }}">
      </div>

      <div class="col-md-4">
          <label class="form-label fw-semibold">Handling Type <span class="text-danger">*</span></label>
            <select name="handle_type" class="form-select @error('handle_type') is-invalid @enderror" required>
                <option value="">Select</option>
                <option value="staff" {{ old('handle_type', optional($order)->handle_type) == 0 ? 'selected' : '' }}>
                    Our Staff Onsite
                </option>
                <option value="self" {{ old('handle_type', optional($order)->handle_type) == 1 ? 'selected' : '' }}>
                    Client Pickup (Self)
                </option>
            </select>
            @error('handle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold">Notes</label>
        <textarea
            name="notes"
            id="notes_editor"
            class="form-control"
            rows="4"
        >{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>


    </div>

    <!-- <div class="row g-3 mt-2">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Event From</label>
        <input type="date" name="event_from" class="form-control" 
        value="{{ old('event_from', $order && $order->event_from ? \Carbon\Carbon::parse($order->event_from)->format('Y-m-d') : '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Notes</label>
        <textarea name="notes" class="form-control" rows="1">{{ old('notes', $order->notes ?? '') }}</textarea>
      </div>
    </div> -->
  </div>
</div>

<!-- Items Card -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-0 p-4">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-box-seam me-2 text-warning"></i>Order Items
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
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Event Duration:</span>
            <span class="fw-semibold">
                <span id="total_days">1</span> Day(s)
            </span>
        </div>
        <!-- Additional Charges -->
        <div class="mb-3">
            <span class="text-muted d-block mb-2">Additional Charges:</span>

            <!-- Delivery Charges -->
            <div class="form-check d-flex align-items-center gap-2 mb-2">
                <input class="form-check-input extra-charge-option mt-0"
                      type="radio"
                      name="extra_charge_type"
                      id="delivery_charge_option"
                      value="delivery" {{ old('extra_charge_type', $order->extra_charge_type ?? '') === 'delivery' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="delivery_charge_option">
                    Delivery Charges
                </label>
            </div>

            <div id="delivery_charge_input" class="ms-4 mb-3" style="{{ old('extra_charge_type', $order->extra_charge_type ?? '') === 'delivery' ? '' : 'display:none;' }}">
                <div class="input-group" style="max-width:200px;">
                    <span class="input-group-text">₹</span>
                    <input type="number"
                          step="0.01"
                          name="delivery_charge_amount"
                          class="form-control extra-charge-amount"
                          id="delivery_charge_amount"
                          value="{{ old('extra_charge_total', $order->extra_charge_total ?? '') }}"
                          placeholder="Enter amount">
                </div>
            </div>

            <!-- Attendance / Support Staff -->
            <div class="form-check d-flex align-items-center gap-2 mb-2">
                <input class="form-check-input extra-charge-option mt-0"
                      type="radio"
                      name="extra_charge_type"
                      id="staff_charge_option"
                      value="staff" {{ old('extra_charge_type', $order->extra_charge_type ?? '') === 'staff' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="staff_charge_option">
                    Attendance / Support Staff <span class="text-muted">(Per Day)</span>
                </label>

              <div id="staff_charge_input"
                  class="ms-4 mb-2"
                  style="{{ old('extra_charge_type', $order->extra_charge_type ?? '') === 'staff' ? '' : 'display:none;' }}">
                  <div class="input-group mb-1" style="max-width:200px;">
                      <span class="input-group-text">₹</span>
                      <input type="number"
                            step="0.01"
                            class="form-control"
                            name="extra_charge_rate"
                            id="staff_charge_amount"
                            value="{{ old('extra_charge_rate', $order->extra_charge_rate ?? '') }}"
                            placeholder="Per day amount">
                  </div>

                    <small class="text-muted">
                        Total for <span id="staff_days">1</span> day(s):
                        <strong>₹ <span id="staff_total">0.00</span></strong>
                    </small>
                </div>

            </div>

            <div id="staff_charge_input" class="ms-4 mb-2" style="display:none;">
                <div class="input-group" style="max-width:200px;">
                    <span class="input-group-text">₹</span>
                    <input type="number"
                          step="0.01"
                          class="form-control extra-charge-amount"
                          id="staff_charge_amount"
                          placeholder="Per day amount">
                </div>
                <small class="text-muted d-block mt-1">
                    This amount will be multiplied by total days
                </small>
            </div>
        </div>


          <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="text-muted mb-0">Discount:</label>
            <div class="input-group" style="width: 150px;">
              <span class="input-group-text">₹</span>
              <input type="number" step="0.01" name="discount_amount" id="discount" class="form-control" value="{{ old('discount_amount', $order->discount_amount ?? 0) }}">
            </div>
          </div>

          <!-- ✅ ADVANCE PAID (ORDER ONLY) -->
          <div class="d-flex justify-content-between mb-3">
            <label class="fw-semibold mb-0">
              Advance Paid <span class="text-danger">*</span>
            </label>
            <div>
              <input type="number"
                     step="0.01"
                     name="advance_paid"
                     class="form-control form-control-sm @error('advance_paid') is-invalid @enderror"
                     style="width:150px;" value="{{ old('advance_paid', $order->advance_paid ?? 0) }}"
                     required>
              @error('advance_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          
          <div class="d-flex justify-content-between mb-3">
            <label class="fw-semibold mb-0">
              Security Deposit
            </label>
            <input type="number"
                   step="0.01"
                   name="security_deposit"
                   class="form-control form-control-sm"
                   style="width:150px;" value="{{ old('security_deposit', $order->security_deposit ?? '') }}">
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

@push('scripts')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
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
            row.find('input[name*="[item_type]"]').val(item.type || '');
            row.find('input[name*="[description]"]').val(item.description || '');
            row.find('input[name*="[unit_price]"]').val(item.unit_price || 0);
            row.find('input[name*="[tax_percent]"]').val(item.tax_percent || 0);
            
            // Trigger recalculation
            setTimeout(() => recalcAll(), 100);
        }
    });
}

document.addEventListener('DOMContentLoaded', function(){
    // Initialize autocomplete for existing rows
    document.querySelectorAll('.item-name').forEach(input => initAutocomplete(input));

    /* ======================
       TOTAL DAYS
    ====================== */
    function getTotalDays() {
        const from = document.getElementById('event_from').value;
        const to   = document.getElementById('event_to').value;

        if (!from || !to) return 1;

        const start = new Date(from);
        const end   = new Date(to);

        const diff = (end - start) / (1000 * 60 * 60 * 24);
        return diff >= 0 ? diff + 1 : 1;
    }

    /* ======================
       EXTRA CHARGE CALC
    ====================== */
    function getExtraCharge() {
        const selected = document.querySelector('input[name="extra_charge_type"]:checked');
        if (!selected) return 0;

        const days = getTotalDays();

        // Delivery → one time
        if (selected.value === 'delivery') {
            return parseFloat(document.getElementById('delivery_charge_amount').value) || 0;
        }

        // Staff → per day
        if (selected.value === 'staff') {
            const perDay = parseFloat(document.getElementById('staff_charge_amount').value) || 0;
            const total = perDay * days;

            document.getElementById('staff_days').innerText = days;
            document.getElementById('staff_total').innerText = total.toFixed(2);

            return total;
        }

        return 0;
    }

    /* ======================
       ROW CALCULATION
    ====================== */
    function recalcRow(row){
        const qty  = parseFloat(row.querySelector('.qty').value) || 0;
        const unit = parseFloat(row.querySelector('.unit').value) || 0;
        const tax  = parseFloat(row.querySelector('.tax').value) || 0;
        const days = getTotalDays();

        const baseTotal = qty * unit * days;
        const taxAmount = baseTotal * (tax / 100);

        row.querySelector('.line_total').innerText = '₹' + (baseTotal + taxAmount).toFixed(2);
        row.dataset.baseAmount = baseTotal.toFixed(2);
        row.dataset.taxAmount  = taxAmount.toFixed(2);
    }

    /* ======================
       SUMMARY CALC
    ====================== */
    function recalcAll(){
        let subtotal = 0;
        let totalTax = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            recalcRow(row);
            subtotal += parseFloat(row.dataset.baseAmount || 0);
            totalTax += parseFloat(row.dataset.taxAmount || 0);
        });

        const discount    = parseFloat(document.getElementById('discount').value) || 0;
        const extraCharge = getExtraCharge();

        const total = subtotal + totalTax + extraCharge - discount;

        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('tax_amount').innerText = totalTax.toFixed(2);
        document.getElementById('total_amount').innerText = total.toFixed(2);
        document.getElementById('total_days').innerText = getTotalDays();
    }

    /* ======================
       EXTRA CHARGE UI
    ====================== */
    const deliveryInput = document.getElementById('delivery_charge_input');
    const staffInput    = document.getElementById('staff_charge_input');

    document.querySelectorAll('.extra-charge-option').forEach(opt => {
        opt.addEventListener('change', function () {
            deliveryInput.style.display = 'none';
            staffInput.style.display = 'none';

            document.getElementById('delivery_charge_amount').value = '';
            document.getElementById('staff_charge_amount').value = '';

            if (this.value === 'delivery') deliveryInput.style.display = 'block';
            if (this.value === 'staff') staffInput.style.display = 'block';

            recalcAll();
        });
    });

    /* ======================
       EVENT LISTENERS
    ====================== */
    document.addEventListener('input', function(e){
        if (
            e.target.matches('.qty') ||
            e.target.matches('.unit') ||
            e.target.matches('.tax') ||
            e.target.matches('#discount') ||
            e.target.matches('#delivery_charge_amount') ||
            e.target.matches('#staff_charge_amount')
        ) {
            recalcAll();
        }
    });

    document.getElementById('event_from').addEventListener('change', recalcAll);
    document.getElementById('event_to').addEventListener('change', recalcAll);

    /* ======================
       ADD ROW
    ====================== */
    document.getElementById('add-row').addEventListener('click', function(){
        const tbody = document.querySelector('#items-table tbody');
        const index = tbody.children.length;

        const tr = document.createElement('tr');
        tr.classList.add('item-row');
        tr.innerHTML = `
            <td><input name="items[${index}][item_name]" class="form-control form-control-sm item-name"></td>
            <td><input name="items[${index}][item_type]" class="form-control form-control-sm"></td>
            <td><input name="items[${index}][description]" class="form-control form-control-sm"></td>
            <td><input name="items[${index}][quantity]" class="form-control form-control-sm qty text-center" value="1"></td>
            <td><input name="items[${index}][unit_price]" class="form-control form-control-sm unit" value="0"></td>
            <td><input name="items[${index}][tax_percent]" class="form-control form-control-sm tax text-center" value="0"></td>
            <td class="line_total text-end fw-semibold">₹0.00</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
        `;
        tbody.appendChild(tr);
        
        // Initialize autocomplete for new row
        initAutocomplete(tr.querySelector('.item-name'));
        
        recalcAll();
    });

    /* ======================
       REMOVE ROW
    ====================== */
    document.addEventListener('click', function(e){
        if (e.target.closest('.remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                recalcAll();
            }
        }
    });

    // Initial run
    recalcAll();
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#notes_editor'), {
            toolbar: [
                'bold', 'italic', 'underline',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'undo', 'redo'
            ]
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#bill_editor'), {
            toolbar: [
                'bold', 'italic', 'underline',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'undo', 'redo'
            ]
        })
        .catch(error => {
            console.error(error);
        });
</script>
<!-- <script>
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
        alert('Please save the order first. After saving, you can generate PDF from the order details page.');
    });

    document.getElementById('send-email').addEventListener('click', function(){
        alert('Please save the order first. After saving, you can send email from the order details page.');
    });

    document.getElementById('send-whatsapp').addEventListener('click', function(){
        alert('Please save the order first. After saving, you can send WhatsApp message from the order details page.');
    });
});
</script> -->

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