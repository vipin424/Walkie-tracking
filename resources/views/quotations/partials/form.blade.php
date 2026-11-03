@php
    $items = old('items') ?: ($quotation ? $quotation->items->toArray() : []);
    $savedHT = $quotation->handle_type ?? null;
    $htVal = old('handle_type', $savedHT !== null ? ($savedHT == 1 ? 'self' : 'staff') : '');
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
        <label class="form-label fw-semibold">Client Phone <span class="text-danger">*</span></label>
        <input type="tel" name="client_phone" class="form-control" value="{{ old('client_phone', $quotation->client_phone ?? '') }}" pattern="[0-9]{10}" maxlength="10" required>
      </div>
    </div>

    <div class="col-md-12">
        <label class="form-label fw-semibold">Bill To</label>
        <textarea
            name="bill_to"
            id="bill_editor"
            class="form-control"
            rows="4"
        >{{ old('bill_to', $quotation->bill_to ?? '') }}</textarea>
    </div>

    <div class="row g-3 mt-2">

      <div class="col-md-4">
          <label class="form-label fw-semibold">Event From</label>
          <input type="date" name="event_from" id="event_from" class="form-control"
          value="{{ old('event_from', $quotation && $quotation->event_from ? \Carbon\Carbon::parse($quotation->event_from)->format('Y-m-d') : '') }}">
      </div>

      <div class="col-md-4">
          <label class="form-label fw-semibold">Event To</label>
          <input type="date" name="event_to" id="event_to" class="form-control"
          value="{{ old('event_to', $quotation && $quotation->event_to ? \Carbon\Carbon::parse($quotation->event_to)->format('Y-m-d') : '') }}">
      </div>

      <div class="col-md-4">
          <label class="form-label fw-semibold">Handling Type <span class="text-danger">*</span></label>

          <select name="handle_type" class="form-select" required>
              <option value="">Select</option>
              <option value="staff" {{ $htVal === 'staff' ? 'selected' : '' }}>Our Staff Onsite</option>
              <option value="self" {{ $htVal === 'self' ? 'selected' : '' }}>Client Pickup (Self)</option>
          </select>
      </div>

    <div class="col-md-12">
        <label class="form-label fw-semibold">Notes</label>
        <textarea
            name="notes"
            id="notes_editor"
            class="form-control"
            rows="4"
        >{{ old('notes', $quotation->notes ?? 'To proceed with the order confirmation, kindly make a 50% advance payment to the bank details mentioned below.') }}</textarea>
    </div>


    </div>

    <!-- <div class="row g-3 mt-2">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Event From</label>
        <input type="date" name="event_from" class="form-control" 
        value="{{ old('event_from', $quotation && $quotation->event_from ? \Carbon\Carbon::parse($quotation->event_from)->format('Y-m-d') : '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Notes</label>
        <textarea name="notes" class="form-control" rows="1">{{ old('notes', $quotation->notes ?? '') }}</textarea>
      </div>
    </div> -->
  </div>
</div>

<!-- Quotation Type Toggle -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <span class="fw-semibold"><i class="bi bi-ui-checks-grid me-2 text-warning"></i>Quotation Type:</span>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="quotation_type" id="type_standard" value="standard"
          {{ old('quotation_type', $quotation->quotation_type ?? 'standard') === 'standard' ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="type_standard">
          <i class="bi bi-calendar-range me-1 text-primary"></i> Standard (Continuous Date Range)
        </label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="quotation_type" id="type_multi_date" value="multi_date"
          {{ old('quotation_type', $quotation->quotation_type ?? 'standard') === 'multi_date' ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="type_multi_date">
          <i class="bi bi-calendar2-plus me-1 text-warning"></i> Multi-Date (Custom Items/Qty Per Date)
        </label>
      </div>
    </div>
  </div>
</div>

<!-- Standard Items Card -->
<div id="standard-items-section" class="card border-0 shadow-sm mb-4">
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

<!-- Multi-Date Items Section -->
<div id="multi-date-items-section" class="mb-4" style="display: none;">
  @include('quotations.partials.multi-date-form')
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
            <span class="text-muted" id="duration_label">Event Duration:</span>
            <span class="fw-semibold">
                <span id="total_days">1</span> <span id="duration_unit">Day(s)</span>
            </span>
          </div>
        <!-- Additional Charges -->
        <div class="mb-3">
            <span class="text-muted d-block mb-2">
                Additional Charges:
                <a href="#" id="clear_extra_charges" class="text-danger ms-2" style="font-size: 0.85em; text-decoration: none;"><i class="bi bi-x-circle"></i> Clear</a>
            </span>

            <!-- Delivery Charges -->
            <div class="form-check d-flex align-items-center gap-2 mb-2">
                <input class="form-check-input extra-charge-option mt-0"
                      type="radio"
                      name="extra_charge_type"
                      id="delivery_charge_option"
                      value="delivery" {{ old('extra_charge_type', $quotation->extra_charge_type ?? '') === 'delivery' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="delivery_charge_option">
                    Delivery Charges
                </label>
            </div>

            <div id="delivery_charge_input" class="ms-4 mb-3" style="{{ old('extra_charge_type', $quotation->extra_charge_type ?? '') === 'delivery' ? '' : 'display:none;' }}">
                <div class="input-group" style="max-width:200px;">
                    <span class="input-group-text">₹</span>
                    <input type="number"
                          step="0.01"
                          name="delivery_charge_amount"
                          class="form-control extra-charge-amount"
                          id="delivery_charge_amount"
                          value="{{ old('extra_charge_total', $quotation->extra_charge_total ?? '') }}"
                          placeholder="Enter amount">
                </div>
            </div>

            <!-- Travelling Charges -->
            <!-- Attendance / Support Staff -->
            <div id="attendance_support_staff_wrapper" style="display: {{ $htVal === 'staff' ? 'block' : 'none' }};">
                <div class="form-check d-flex align-items-center gap-2 mb-2">
                    <input class="form-check-input extra-charge-option mt-0"
                          type="radio"
                          name="extra_charge_type"
                          id="staff_charge_option"
                          value="staff" {{ old('extra_charge_type', $quotation->extra_charge_type ?? '') === 'staff' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="staff_charge_option">
                        Attendance / Support Staff <span class="text-muted">(Per Day)</span>
                    </label>
                </div>

                <div id="staff_charge_input"
                    class="ms-4 mb-2"
                    style="{{ old('extra_charge_type', $quotation->extra_charge_type ?? '') === 'staff' ? '' : 'display:none;' }}">
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <div class="input-group" style="max-width:160px;">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <input type="number"
                                  min="1"
                                  step="1"
                                  class="form-control"
                                  name="staff_count"
                                  id="staff_count"
                                  value="{{ old('staff_count', $quotation->staff_count ?? 1) }}"
                                  placeholder="No. of Staff">
                        </div>
                        <div class="input-group" style="max-width:160px;">
                            <span class="input-group-text">₹</span>
                            <input type="number"
                                  step="0.01"
                                  class="form-control"
                                  name="extra_charge_rate"
                                  id="staff_charge_amount"
                                  value="{{ old('extra_charge_rate', $quotation->extra_charge_rate ?? '') }}"
                                  placeholder="Rate/Staff/Day">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <span id="staff_count_display">1</span> Staff ×
                        ₹<span id="staff_rate_display">0</span> ×
                        <span id="staff_days">1</span> <span id="staff_days_unit">Day(s)</span> =
                        <strong>₹<span id="staff_total">0.00</span></strong>
                    </small>
                </div>
            </div>
        </div>


          <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="text-muted mb-0">Travelling Charges:</label>
            <div class="input-group" style="width: 150px;">
              <span class="input-group-text">₹</span>
              <input type="number" step="0.01" name="travelling_charge_amount" id="travelling_charge_amount" class="form-control" value="{{ old('travelling_charge_amount', $quotation->travelling_charge ?? 0) }}">
            </div>
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

@push('scripts')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
// Autocomplete for standard items
function initAutocomplete(input) {
    if (typeof $ === 'undefined' || !$.fn.autocomplete) return;
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
            
            row.find('input[name*="[item_type]"]').val(item.type || '');
            row.find('input[name*="[description]"]').val(item.description || '');
            row.find('input[name*="[unit_price]"]').val(item.unit_price || 0);
            row.find('input[name*="[tax_percent]"]').val(item.tax_percent || 0);
            
            setTimeout(() => window.recalcAll(), 100);
        }
    });
}

/* =====================================================================
   MASTER QUOTATION RECALCULATION ENGINE (STANDARD + MULTI-DATE)
   ===================================================================== */
window.recalcAll = function () {
    const isMultiDate = document.querySelector('input[name="quotation_type"]:checked')?.value === 'multi_date';
    let subtotal = 0;
    let totalTax = 0;
    let totalDays = 1;

    if (isMultiDate) {
        // Multi-Date Mode
        const dateBlocks = document.querySelectorAll('#multi-date-container .date-block');
        totalDays = dateBlocks.length;

        dateBlocks.forEach((block, di) => {
            let blockSub = 0;

            const seqEl = block.querySelector('.date-seq');
            if (seqEl) seqEl.textContent = di + 1;

            block.querySelectorAll('.date-item-row').forEach(row => {
                const qty  = parseFloat(row.querySelector('.date-item-qty')?.value)  || 0;
                const unit = parseFloat(row.querySelector('.date-item-unit')?.value) || 0;
                const taxP = parseFloat(row.querySelector('.date-item-tax')?.value)  || 0;

                const base = qty * unit;
                const taxAmt = base * (taxP / 100);
                const lineTotal = base + taxAmt;

                const lineEl = row.querySelector('.date-item-total');
                if (lineEl) lineEl.textContent = '₹' + lineTotal.toFixed(2);

                subtotal += base;
                totalTax += taxAmt;
                blockSub += lineTotal;
            });

            const blockSubEl = block.querySelector('.date-subtotal-display');
            if (blockSubEl) blockSubEl.textContent = '₹' + blockSub.toFixed(2);
        });

        // Label updates for multi-date
        const durLabel = document.getElementById('duration_label');
        if (durLabel) durLabel.textContent = 'Total Event Dates:';
        const durUnit = document.getElementById('duration_unit');
        if (durUnit) durUnit.textContent = 'Date' + (totalDays === 1 ? '' : 's');
        const staffUnit = document.getElementById('staff_days_unit');
        if (staffUnit) staffUnit.textContent = 'Date' + (totalDays === 1 ? '' : 's');

    } else {
        // Standard Date Range Mode
        const from = document.getElementById('event_from')?.value;
        const to   = document.getElementById('event_to')?.value;

        if (from && to) {
            const start = new Date(from);
            const end   = new Date(to);
            const diff  = (end - start) / (1000 * 60 * 60 * 24);
            totalDays   = diff >= 0 ? diff + 1 : 1;
        } else {
            totalDays = 1;
        }

        document.querySelectorAll('#items-table .item-row').forEach(row => {
            const qty  = parseFloat(row.querySelector('.qty')?.value)  || 0;
            const unit = parseFloat(row.querySelector('.unit')?.value) || 0;
            const taxP = parseFloat(row.querySelector('.tax')?.value)   || 0;

            const base = qty * unit * totalDays;
            const taxAmt = base * (taxP / 100);
            const lineTotal = base + taxAmt;

            const lineEl = row.querySelector('.line_total');
            if (lineEl) lineEl.textContent = '₹' + lineTotal.toFixed(2);

            row.dataset.baseAmount = base.toFixed(2);
            row.dataset.taxAmount  = taxAmt.toFixed(2);

            subtotal += base;
            totalTax += taxAmt;
        });

        const durLabel = document.getElementById('duration_label');
        if (durLabel) durLabel.textContent = 'Event Duration:';
        const durUnit = document.getElementById('duration_unit');
        if (durUnit) durUnit.textContent = 'Day' + (totalDays === 1 ? '' : 's');
        const staffUnit = document.getElementById('staff_days_unit');
        if (staffUnit) staffUnit.textContent = 'Day' + (totalDays === 1 ? '' : 's');
    }

    // Additional Charges
    const selectedExtra = document.querySelector('input[name="extra_charge_type"]:checked');
    let extraCharge = 0;

    if (selectedExtra) {
        if (selectedExtra.value === 'delivery') {
            extraCharge = parseFloat(document.getElementById('delivery_charge_amount')?.value) || 0;
        } else if (selectedExtra.value === 'staff') {
            const count  = parseInt(document.getElementById('staff_count')?.value) || 1;
            const perDay = parseFloat(document.getElementById('staff_charge_amount')?.value) || 0;
            extraCharge  = count * perDay * (totalDays || 1);

            const elCount = document.getElementById('staff_count_display');
            const elRate  = document.getElementById('staff_rate_display');
            const elDays  = document.getElementById('staff_days');
            const elTot   = document.getElementById('staff_total');

            if (elCount) elCount.innerText = count;
            if (elRate)  elRate.innerText  = perDay.toFixed(2);
            if (elDays)  elDays.innerText  = totalDays || 1;
            if (elTot)   elTot.innerText   = extraCharge.toFixed(2);
        }
    }

    const travellingCharge = parseFloat(document.getElementById('travelling_charge_amount')?.value) || 0;
    const discount         = parseFloat(document.getElementById('discount')?.value) || 0;

    const grandTotal = subtotal + totalTax + extraCharge + travellingCharge - discount;

    const elSub = document.getElementById('subtotal');
    if (elSub) elSub.innerText = subtotal.toFixed(2);

    const elTax = document.getElementById('tax_amount');
    if (elTax) elTax.innerText = totalTax.toFixed(2);

    const elDays = document.getElementById('total_days');
    if (elDays) elDays.innerText = totalDays;

    const elTot = document.getElementById('total_amount');
    if (elTot) elTot.innerText = grandTotal.toFixed(2);
};

/* =====================================================================
   TYPE VISIBILITY TOGGLE
   ===================================================================== */
function applyTypeVisibility(type) {
    const standardSection = document.getElementById('standard-items-section');
    const multiSection    = document.getElementById('multi-date-items-section');
    const eventFromWrap   = document.getElementById('event_from')?.closest('.col-md-4');
    const eventToWrap     = document.getElementById('event_to')?.closest('.col-md-4');

    if (type === 'multi_date') {
        if (standardSection) standardSection.style.display = 'none';
        if (multiSection)    multiSection.style.display    = 'block';
        if (eventFromWrap)   eventFromWrap.style.display   = 'none';
        if (eventToWrap)     eventToWrap.style.display     = 'none';

        if (standardSection) {
            standardSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }
        if (document.getElementById('event_from')) document.getElementById('event_from').disabled = true;
        if (document.getElementById('event_to'))   document.getElementById('event_to').disabled   = true;

        if (multiSection) {
            multiSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        }
    } else {
        if (standardSection) standardSection.style.display = 'block';
        if (multiSection)    multiSection.style.display    = 'none';
        if (eventFromWrap)   eventFromWrap.style.display   = '';
        if (eventToWrap)     eventToWrap.style.display     = '';

        if (standardSection) {
            standardSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        }
        if (document.getElementById('event_from')) document.getElementById('event_from').disabled = false;
        if (document.getElementById('event_to'))   document.getElementById('event_to').disabled   = false;

        if (multiSection) {
            multiSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }
    }

    window.recalcAll();
}

document.addEventListener('DOMContentLoaded', function(){
    // Type radio listeners
    document.querySelectorAll('input[name="quotation_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            applyTypeVisibility(this.value);
        });
    });

    const currentType = document.querySelector('input[name="quotation_type"]:checked')?.value || 'standard';
    applyTypeVisibility(currentType);

    // Autocomplete for standard items
    document.querySelectorAll('#items-table .item-name').forEach(input => initAutocomplete(input));

    // Handling Type / Support staff toggling
    const deliveryInput     = document.getElementById('delivery_charge_input');
    const staffInput        = document.getElementById('staff_charge_input');
    const handleTypeSelect  = document.querySelector('select[name="handle_type"]');
    const attendanceWrapper = document.getElementById('attendance_support_staff_wrapper');

    if (handleTypeSelect && attendanceWrapper) {
        function toggleAttendanceStaff() {
            if (handleTypeSelect.value === 'staff') {
                attendanceWrapper.style.display = 'block';
            } else {
                attendanceWrapper.style.display = 'none';
                const staffRadio = document.getElementById('staff_charge_option');
                if (staffRadio && staffRadio.checked) {
                    staffRadio.checked = false;
                    if (staffInput) staffInput.style.display = 'none';
                    const elAmt = document.getElementById('staff_charge_amount');
                    if (elAmt) elAmt.value = '';
                    const elCount = document.getElementById('staff_count');
                    if (elCount) elCount.value = '1';
                    window.recalcAll();
                }
            }
        }
        handleTypeSelect.addEventListener('change', toggleAttendanceStaff);
        toggleAttendanceStaff();
    }

    // Extra charge radio buttons
    document.querySelectorAll('.extra-charge-option').forEach(opt => {
        opt.addEventListener('change', function () {
            if (deliveryInput) deliveryInput.style.display = 'none';
            if (staffInput)    staffInput.style.display    = 'none';

            if (this.value === 'delivery' && deliveryInput) deliveryInput.style.display = 'block';
            if (this.value === 'staff' && staffInput)       staffInput.style.display    = 'block';

            window.recalcAll();
        });
    });

    // Clear extra charges
    const clearExtraChargesBtn = document.getElementById('clear_extra_charges');
    if (clearExtraChargesBtn) {
        clearExtraChargesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.extra-charge-option').forEach(opt => opt.checked = false);
            if (deliveryInput) deliveryInput.style.display = 'none';
            if (staffInput)    staffInput.style.display    = 'none';
            const delAmt = document.getElementById('delivery_charge_amount');
            if (delAmt) delAmt.value = '';
            const stAmt = document.getElementById('staff_charge_amount');
            if (stAmt) stAmt.value = '';
            const stCnt = document.getElementById('staff_count');
            if (stCnt) stCnt.value = '1';
            window.recalcAll();
        });
    }

    // Unified live input listener on document
    document.addEventListener('input', function(e){
        if (
            e.target.matches('.qty') ||
            e.target.matches('.unit') ||
            e.target.matches('.tax') ||
            e.target.matches('.date-item-qty') ||
            e.target.matches('.date-item-unit') ||
            e.target.matches('.date-item-tax') ||
            e.target.matches('#discount') ||
            e.target.matches('#delivery_charge_amount') ||
            e.target.matches('#travelling_charge_amount') ||
            e.target.matches('#staff_charge_amount') ||
            e.target.matches('#staff_count')
        ) {
            window.recalcAll();
        }
    });

    const eventFromEl = document.getElementById('event_from');
    if (eventFromEl) eventFromEl.addEventListener('change', () => window.recalcAll());

    const eventToEl = document.getElementById('event_to');
    if (eventToEl) eventToEl.addEventListener('change', () => window.recalcAll());

    // Standard Add Row
    const addRowBtn = document.getElementById('add-row');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function(){
            const tbody = document.querySelector('#items-table tbody');
            const index = tbody.children.length;

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
                <td class="px-3 py-2 text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            
            initAutocomplete(tr.querySelector('.item-name'));
            window.recalcAll();
        });
    }

    // Standard Remove Row
    document.addEventListener('click', function(e){
        if (e.target.closest('.remove-row')) {
            const rows = document.querySelectorAll('#items-table .item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                window.recalcAll();
            } else {
                alert('At least one item is required!');
            }
        }
    });

    // Initial run
    window.recalcAll();
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    if (document.querySelector('#notes_editor')) {
        ClassicEditor.create(document.querySelector('#notes_editor'), {
            toolbar: ['bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        }).catch(err => console.error(err));
    }
    if (document.querySelector('#bill_editor')) {
        ClassicEditor.create(document.querySelector('#bill_editor'), {
            toolbar: ['bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        }).catch(err => console.error(err));
    }
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
  input[type="number"] {
    -moz-appearance: textfield;
  }
  input[type="number"]::-webkit-outer-spin-button,
  input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  .card {
    transition: all 0.3s ease;
  }
</style>
@endpush