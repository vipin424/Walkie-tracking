{{-- Multi-Date Section --}}
@php
    $savedDates = old('dates') ?: ($quotation && $quotation->quotation_type === 'multi_date' ? $quotation->dates->map(function($d) {
        return [
            'event_date' => $d->event_date->format('Y-m-d'),
            'label'      => $d->label,
            'items'      => $d->items->map(fn($i) => [
                'item_name'   => $i->item_name,
                'item_type'   => $i->item_type,
                'description' => $i->description,
                'quantity'    => $i->quantity,
                'unit_price'  => $i->unit_price,
                'tax_percent' => $i->tax_percent,
            ])->toArray(),
        ];
    })->toArray() : []);
@endphp

<!-- Header Card for Multi-Date Section -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-1 fw-semibold">
        <i class="bi bi-calendar2-week me-2 text-warning"></i>Event Dates &amp; Items
      </h5>
      <small class="text-muted">Configure items and quantities needed for each specific event date</small>
    </div>
    <button type="button" id="add-date-btn" class="btn btn-warning text-dark fw-semibold btn-sm">
      <i class="bi bi-calendar-plus me-1"></i> Add Another Date
    </button>
  </div>
</div>

<div id="multi-date-container">
    {{-- Date blocks rendered here dynamically by JS --}}
</div>

<div class="text-center py-2 mb-4">
  <button type="button" id="add-date-btn-bottom" class="btn btn-outline-warning text-dark fw-semibold px-4 py-2">
    <i class="bi bi-calendar-plus me-2"></i> Add Another Date
  </button>
</div>

{{-- Templates (hidden, used by JS to clone) --}}
<template id="date-row-template">
    <div class="card border-0 shadow-sm mb-4 date-block">
        <div class="card-header bg-white border-bottom p-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="badge bg-warning bg-opacity-10 text-dark px-3 py-2 fs-6 fw-semibold">
                    <i class="bi bi-calendar-event text-warning me-1"></i> Date #<span class="date-seq">1</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-semibold small text-muted">Event Date <span class="text-danger">*</span>:</label>
                    <input type="date" class="form-control form-control-sm date-picker" style="width: 160px;">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-semibold small text-muted">Label (optional):</label>
                    <input type="text" class="form-control form-control-sm label-input" style="width: 170px;" placeholder="e.g. Day 1, Sangeet...">
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-date-btn" title="Remove this date">
                    <i class="bi bi-trash me-1"></i> Remove Date
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 date-items-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Item Name</th>
                            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 120px;">Type</th>
                            <th class="px-3 py-3 text-muted fw-semibold" style="min-width: 180px;">Description</th>
                            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 100px;">Qty</th>
                            <th class="px-3 py-3 text-muted fw-semibold" style="width: 120px;">Unit Price (₹)</th>
                            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 100px;">Tax %</th>
                            <th class="px-3 py-3 text-muted fw-semibold text-end" style="width: 120px;">Total</th>
                            <th class="px-3 py-3 text-muted fw-semibold text-center" style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-3 px-4 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-success btn-sm add-date-item-btn">
                <i class="bi bi-plus-circle me-1"></i> Add Item
            </button>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-semibold">Date Subtotal:</span>
                <span class="fs-6 fw-bold text-dark date-subtotal-display">₹0.00</span>
            </div>
        </div>
    </div>
</template>

<template id="date-item-row-template">
    <tr class="date-item-row">
        <td class="px-3 py-2">
            <input type="text" class="form-control form-control-sm date-item-name item-name" placeholder="Search item...">
        </td>
        <td class="px-3 py-2">
            <input type="text" class="form-control form-control-sm date-item-type" value="Walkie Talkie">
        </td>
        <td class="px-3 py-2">
            <input type="text" class="form-control form-control-sm date-item-desc" placeholder="Notes/Description">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="1" min="1" class="form-control form-control-sm text-center date-item-qty" value="1">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm date-item-unit" value="0">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center date-item-tax" value="0">
        </td>
        <td class="px-3 py-2 text-end fw-semibold date-item-total">
            ₹0.00
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-date-item-btn" title="Remove item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
(function () {
    'use strict';

    const INITIAL_DATES = @json($savedDates);
    const ITEMS_SEARCH_URL = '{{ route('items.search') }}';

    const container         = document.getElementById('multi-date-container');
    const addDateBtn        = document.getElementById('add-date-btn');
    const addDateBtnBottom  = document.getElementById('add-date-btn-bottom');
    const dateTpl           = document.getElementById('date-row-template');
    const itemTpl           = document.getElementById('date-item-row-template');

    function reindexAll() {
        container.querySelectorAll('.date-block').forEach((block, di) => {
            block.dataset.dateIndex = di;

            const seqEl = block.querySelector('.date-seq');
            if (seqEl) seqEl.textContent = di + 1;

            const datePicker = block.querySelector('.date-picker');
            const labelInput = block.querySelector('.label-input');
            datePicker.name  = `dates[${di}][event_date]`;
            labelInput.name  = `dates[${di}][label]`;

            block.querySelectorAll('.date-item-row').forEach((row, ii) => {
                row.querySelector('.date-item-name').name = `dates[${di}][items][${ii}][item_name]`;
                row.querySelector('.date-item-type').name = `dates[${di}][items][${ii}][item_type]`;
                row.querySelector('.date-item-desc').name = `dates[${di}][items][${ii}][description]`;
                row.querySelector('.date-item-qty').name  = `dates[${di}][items][${ii}][quantity]`;
                row.querySelector('.date-item-unit').name = `dates[${di}][items][${ii}][unit_price]`;
                row.querySelector('.date-item-tax').name  = `dates[${di}][items][${ii}][tax_percent]`;
            });
        });
    }

    function initDateItemAutocomplete(input) {
        if (typeof $ === 'undefined' || !$.fn.autocomplete) return;
        $(input).autocomplete({
            source: function (req, resp) {
                $.ajax({
                    url: ITEMS_SEARCH_URL,
                    data: { q: req.term },
                    success: function (data) {
                        resp(data.map(item => ({
                            label: item.name,
                            value: item.name,
                            item: item
                        })));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                const row  = $(this).closest('tr');
                const item = ui.item.item;
                row.find('.date-item-type').val(item.type || '');
                row.find('.date-item-desc').val(item.description || '');
                row.find('.date-item-unit').val(item.unit_price || 0);
                row.find('.date-item-tax').val(item.tax_percent || 0);
                setTimeout(() => {
                    if (typeof window.recalcAll === 'function') {
                        window.recalcAll();
                    }
                }, 50);
            }
        });
    }

    function addItemToBlock(block, prefill = {}) {
        const tplContent = itemTpl.content.cloneNode(true);
        const row = tplContent.querySelector('tr');

        if (prefill.item_name)   row.querySelector('.date-item-name').value = prefill.item_name;
        if (prefill.item_type)   row.querySelector('.date-item-type').value = prefill.item_type;
        if (prefill.description) row.querySelector('.date-item-desc').value = prefill.description;
        if (prefill.quantity !== undefined)    row.querySelector('.date-item-qty').value  = prefill.quantity;
        if (prefill.unit_price !== undefined)  row.querySelector('.date-item-unit').value = prefill.unit_price;
        if (prefill.tax_percent !== undefined) row.querySelector('.date-item-tax').value  = prefill.tax_percent;

        const tbody = block.querySelector('.date-items-table tbody');
        tbody.appendChild(tplContent);

        initDateItemAutocomplete(tbody.lastElementChild.querySelector('.date-item-name'));

        reindexAll();
        if (typeof window.recalcAll === 'function') {
            window.recalcAll();
        }
    }

    function addDateBlock(prefill = {}) {
        const tplContent = dateTpl.content.cloneNode(true);
        const block = tplContent.querySelector('.date-block');

        if (prefill.event_date) block.querySelector('.date-picker').value = prefill.event_date;
        if (prefill.label)      block.querySelector('.label-input').value  = prefill.label;

        container.appendChild(tplContent);

        const newBlock = container.lastElementChild;

        if (prefill.items && prefill.items.length > 0) {
            prefill.items.forEach(item => addItemToBlock(newBlock, item));
        } else {
            addItemToBlock(newBlock);
        }

        reindexAll();
        if (typeof window.recalcAll === 'function') {
            window.recalcAll();
        }
    }

    if (addDateBtn) {
        addDateBtn.addEventListener('click', () => addDateBlock());
    }
    if (addDateBtnBottom) {
        addDateBtnBottom.addEventListener('click', () => addDateBlock());
    }

    container.addEventListener('click', function (e) {
        // Remove date block
        const removeDateBtn = e.target.closest('.remove-date-btn');
        if (removeDateBtn) {
            const block = removeDateBtn.closest('.date-block');
            if (container.querySelectorAll('.date-block').length > 1) {
                block.remove();
            } else {
                alert('At least one date is required.');
                return;
            }
            reindexAll();
            if (typeof window.recalcAll === 'function') {
                window.recalcAll();
            }
            return;
        }

        // Add item to specific date
        const addItemBtn = e.target.closest('.add-date-item-btn');
        if (addItemBtn) {
            const block = addItemBtn.closest('.date-block');
            addItemToBlock(block);
            return;
        }

        // Remove item row
        const removeItemBtn = e.target.closest('.remove-date-item-btn');
        if (removeItemBtn) {
            const row   = removeItemBtn.closest('tr');
            const tbody = row.closest('tbody');
            if (tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                reindexAll();
                if (typeof window.recalcAll === 'function') {
                    window.recalcAll();
                }
            } else {
                alert('Each date must have at least one item.');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        if (INITIAL_DATES && INITIAL_DATES.length > 0) {
            INITIAL_DATES.forEach(d => addDateBlock(d));
        } else {
            addDateBlock();
        }

        const currentType = document.querySelector('input[name="quotation_type"]:checked')?.value || 'standard';
        if (currentType === 'standard') {
            container.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }

        if (typeof window.recalcAll === 'function') {
            window.recalcAll();
        }
    });
})();
</script>
@endpush
