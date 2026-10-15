@extends('layouts.app')
@section('title', isset($monthlyQuotation) ? 'Edit Monthly Quotation' : 'New Monthly Quotation')

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <a href="{{ route('monthly-quotations.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-2"></i>Back to Quotations
      </a>
      <h4 class="mb-1 fw-bold">
        {{ isset($monthlyQuotation) ? 'Edit Quotation: ' . $monthlyQuotation->code : 'New Monthly Rate Quotation' }}
      </h4>
      <p class="text-muted mb-0">Rate contract for monthly walkie-talkie rental</p>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
      <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST"
        action="{{ isset($monthlyQuotation) ? route('monthly-quotations.update', $monthlyQuotation) : route('monthly-quotations.store') }}"
        id="mq-form">
    @csrf
    @if(isset($monthlyQuotation)) @method('PUT') @endif

    {{-- ═══════════════ CLIENT INFORMATION ═══════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-person-circle me-2 text-warning"></i>Client Information
          <small class="text-muted fw-normal ms-2" style="font-size:13px;">(copy from email / free text)</small>
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contact Person Name <span class="text-danger">*</span></label>
            <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror"
                   placeholder="e.g. Madhumitha Consultant"
                   value="{{ old('client_name', $monthlyQuotation->client_name ?? '') }}" required>
            @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Company Name</label>
            <input type="text" name="client_company" class="form-control @error('client_company') is-invalid @enderror"
                   placeholder="e.g. Omega HMS Pvt Ltd"
                   value="{{ old('client_company', $monthlyQuotation->client_company ?? '') }}">
            @error('client_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">GSTIN</label>
            <input type="text" name="client_gstin" class="form-control @error('client_gstin') is-invalid @enderror"
                   placeholder="e.g. 27AABCU9603R1ZX"
                   value="{{ old('client_gstin', $monthlyQuotation->client_gstin ?? '') }}">
            @error('client_gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Phone</label>
            <input type="text" name="client_phone" class="form-control @error('client_phone') is-invalid @enderror"
                   placeholder="e.g. 9876543210"
                   value="{{ old('client_phone', $monthlyQuotation->client_phone ?? '') }}">
            @error('client_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="client_email" class="form-control @error('client_email') is-invalid @enderror"
                   placeholder="e.g. consultant@company.com"
                   value="{{ old('client_email', $monthlyQuotation->client_email ?? '') }}">
            @error('client_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Location / Facilities</label>
            <input type="text" name="client_address" class="form-control @error('client_address') is-invalid @enderror"
                   placeholder="e.g. All Mumbai Facilities"
                   value="{{ old('client_address', $monthlyQuotation->client_address ?? '') }}">
            @error('client_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ═══════════════ QUOTATION TERMS ═══════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-sliders me-2 text-warning"></i>Quotation Terms
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Validity (Days) <span class="text-danger">*</span></label>
            <input type="number" name="validity_days" class="form-control @error('validity_days') is-invalid @enderror"
                   min="1" value="{{ old('validity_days', $monthlyQuotation->validity_days ?? 30) }}" required>
            @error('validity_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Quotation valid for this many days from today</small>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">GST % <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="number" name="gst_percent" id="gst_percent" class="form-control @error('gst_percent') is-invalid @enderror"
                     step="0.01" min="0" max="100"
                     value="{{ old('gst_percent', $monthlyQuotation->gst_percent ?? 18) }}" required>
              <span class="input-group-text">%</span>
            </div>
            @error('gst_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Delivery Timeline</label>
            <input type="text" name="delivery_timeline" class="form-control @error('delivery_timeline') is-invalid @enderror"
                   placeholder="e.g. Immediate / 2-3 days"
                   value="{{ old('delivery_timeline', $monthlyQuotation->delivery_timeline ?? '') }}">
            @error('delivery_timeline')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Payment Terms</label>
            <input type="text" name="payment_terms" class="form-control @error('payment_terms') is-invalid @enderror"
                   placeholder="e.g. Net 30 days"
                   value="{{ old('payment_terms', $monthlyQuotation->payment_terms ?? '') }}">
            @error('payment_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          @if(isset($monthlyQuotation))
          <div class="col-md-3">
            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="draft" {{ ($monthlyQuotation->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="sent"  {{ ($monthlyQuotation->status ?? '') == 'sent'  ? 'selected' : '' }}>Sent</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ═══════════════ ITEMS TABLE ═══════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-list-ul me-2 text-warning"></i>Items / Scope of Supply
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
                <th class="px-3 py-3 text-muted fw-semibold" style="min-width:220px;">Description <span class="text-danger">*</span></th>
                <th class="px-3 py-3 text-muted fw-semibold" style="width:100px;">UOM <span class="text-danger">*</span></th>
                <th class="px-3 py-3 text-muted fw-semibold text-center" style="width:90px;">Qty <span class="text-danger">*</span></th>
                <th class="px-3 py-3 text-muted fw-semibold" style="width:150px;">Rate / Month <span class="text-danger">*</span></th>
                <th class="px-3 py-3 text-muted fw-semibold text-end" style="width:130px;">Amount</th>
                <th class="px-3 py-3" style="width:50px;"></th>
              </tr>
            </thead>
            <tbody id="items-container">
              @php
                $existingItems = old('items', isset($monthlyQuotation)
                  ? $monthlyQuotation->items->map(fn($it) => [
                        'item_name'   => $it->item_name,
                        'description' => $it->description,
                        'uom'         => $it->uom,
                        'quantity'    => $it->quantity,
                        'rate'        => $it->rate,
                    ])->toArray()
                  : [['item_name'=>'','description'=>'','uom'=>'MTH','quantity'=>1,'rate'=>0]]
                );
              @endphp

              @foreach($existingItems as $i => $item)
              <tr class="item-row">
                <td class="px-3 py-2">
                  <input type="text" name="items[{{ $i }}][item_name]"
                         class="form-control form-control-sm item-name"
                         placeholder="e.g. Rental Walkie Talkie 2W-5W UHF"
                         value="{{ $item['item_name'] ?? '' }}" required>
                  <input type="text" name="items[{{ $i }}][description]"
                         class="form-control form-control-sm mt-1"
                         placeholder="Additional description (optional)"
                         value="{{ $item['description'] ?? '' }}">
                </td>
                <td class="px-3 py-2">
                  <select name="items[{{ $i }}][uom]" class="form-select form-select-sm item-uom">
                    @foreach(['MTH','Nos','Pcs','Set','Day','HR'] as $u)
                      <option value="{{ $u }}" {{ ($item['uom'] ?? 'MTH') == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                  </select>
                </td>
                <td class="px-3 py-2">
                  <input type="number" name="items[{{ $i }}][quantity]"
                         class="form-control form-control-sm text-center item-qty"
                         placeholder="Qty" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" required>
                </td>
                <td class="px-3 py-2">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text">₹</span>
                    <input type="number" name="items[{{ $i }}][rate]"
                           class="form-control item-rate"
                           placeholder="Rate" step="0.01" value="{{ $item['rate'] ?? 0 }}" min="0" required>
                  </div>
                </td>
                <td class="px-3 py-2 text-end fw-semibold item-amount">
                  ₹{{ number_format(($item['quantity'] ?? 1) * ($item['rate'] ?? 0), 2) }}
                </td>
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
                <td colspan="4" class="px-3 py-2 text-end fw-semibold text-muted">Subtotal:</td>
                <td class="px-3 py-2 text-end fw-semibold" id="display-subtotal">₹0.00</td>
                <td></td>
              </tr>
              <tr>
                <td colspan="4" class="px-3 py-2 text-end fw-semibold text-muted">
                  GST (<span id="gst-pct-label">{{ old('gst_percent', $monthlyQuotation->gst_percent ?? 18) }}</span>%):
                </td>
                <td class="px-3 py-2 text-end fw-semibold text-info" id="display-gst">₹0.00</td>
                <td></td>
              </tr>
              <tr>
                <td colspan="4" class="px-3 py-2 text-end fw-bold fs-6">Grand Total:</td>
                <td class="px-3 py-2 text-end fw-bold fs-5 text-warning" id="display-total">₹0.00</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    {{-- ═══════════════ NOTES / T&C ═══════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Terms &amp; Conditions
        </h5>
      </div>
      <div class="card-body p-4">
        <textarea name="notes" class="form-control" rows="5"
                  placeholder="Enter Terms & Conditions, special notes, etc.&#10;&#10;e.g.&#10;1. Rates are exclusive of GST.&#10;2. Payment to be made within 30 days of invoice.&#10;3. Security deposit of ₹5000 per set is applicable.">{{ old('notes', $monthlyQuotation->notes ?? '') }}</textarea>
      </div>
    </div>

    <div class="mb-4">
      <button type="submit" class="btn btn-primary btn-lg me-2">
        <i class="bi bi-check-circle me-2"></i>{{ isset($monthlyQuotation) ? 'Update' : 'Create' }} Quotation
      </button>
      <a href="{{ route('monthly-quotations.index') }}" class="btn btn-secondary btn-lg">
        <i class="bi bi-x-circle me-2"></i>Cancel
      </a>
    </div>
  </form>
</div>

@push('scripts')
{{-- Quill Editor --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
let itemIndex = {{ count($existingItems) }};
const uomOptions = ['MTH','Nos','Pcs','Set','Day','HR'];

function buildUomSelect(name, selected) {
  let opts = uomOptions.map(u =>
    `<option value="${u}" ${u === selected ? 'selected' : ''}>${u}</option>`
  ).join('');
  return `<select name="${name}" class="form-select form-select-sm item-uom">${opts}</select>`;
}

function calculateAll() {
  let subtotal = 0;
  $('.item-row').each(function () {
    const qty  = parseFloat($(this).find('.item-qty').val()) || 0;
    const rate = parseFloat($(this).find('.item-rate').val()) || 0;
    const amt  = qty * rate;
    $(this).find('.item-amount').text('₹' + amt.toFixed(2));
    subtotal += amt;
  });

  const gstPct = parseFloat($('#gst_percent').val()) || 0;
  const gstAmt = subtotal * gstPct / 100;
  const total  = subtotal + gstAmt;

  $('#display-subtotal').text('₹' + subtotal.toFixed(2));
  $('#display-gst').text('₹' + gstAmt.toFixed(2));
  $('#display-total').text('₹' + total.toFixed(2));
  $('#gst-pct-label').text(gstPct);
}

function initAutocomplete(input) {
  $(input).autocomplete({
    source: function (req, res) {
      $.ajax({
        url: '{{ route('items.search') }}',
        data: { q: req.term },
        success: function (data) {
          res(data.map(item => ({
            label: item.name + (item.type ? ' (' + item.type + ')' : ''),
            value: item.name,
            item: item
          })));
        }
      });
    },
    minLength: 2,
    select: function (e, ui) {
      const row = $(this).closest('tr');
      row.find('input[name*="[description]"]').val(ui.item.item.description || '');
      row.find('input[name*="[rate]"]').val(ui.item.item.unit_price || 0);
      setTimeout(calculateAll, 100);
    }
  });
}

$(document).ready(function () {
  // Init autocomplete on existing rows
  $('.item-name').each(function () { initAutocomplete(this); });

  calculateAll();

  // Add item row
  $('#add-item').click(function () {
    const uomSel = buildUomSelect(`items[${itemIndex}][uom]`, 'MTH');
    const row = `
      <tr class="item-row">
        <td class="px-3 py-2">
          <input type="text" name="items[${itemIndex}][item_name]"
                 class="form-control form-control-sm item-name"
                 placeholder="e.g. Rental Walkie Talkie 2W-5W UHF" required>
          <input type="text" name="items[${itemIndex}][description]"
                 class="form-control form-control-sm mt-1"
                 placeholder="Additional description (optional)">
        </td>
        <td class="px-3 py-2">${uomSel}</td>
        <td class="px-3 py-2">
          <input type="number" name="items[${itemIndex}][quantity]"
                 class="form-control form-control-sm text-center item-qty"
                 placeholder="Qty" value="1" min="0.01" step="0.01" required>
        </td>
        <td class="px-3 py-2">
          <div class="input-group input-group-sm">
            <span class="input-group-text">₹</span>
            <input type="number" name="items[${itemIndex}][rate]"
                   class="form-control item-rate"
                   placeholder="Rate" step="0.01" value="0" min="0" required>
          </div>
        </td>
        <td class="px-3 py-2 text-end fw-semibold item-amount">₹0.00</td>
        <td class="px-3 py-2 text-center">
          <button type="button" class="btn btn-sm btn-outline-danger remove-item">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>`;
    $('#items-container').append(row);
    initAutocomplete($('#items-container tr:last .item-name')[0]);
    itemIndex++;
    calculateAll();
  });

  // Remove item
  $(document).on('click', '.remove-item', function () {
    if ($('.item-row').length > 1) {
      $(this).closest('.item-row').remove();
      calculateAll();
    } else {
      alert('At least one item is required!');
    }
  });

  // Recalculate on input
  $(document).on('input change', '.item-qty, .item-rate, #gst_percent', calculateAll);

  // ── Quill Rich Text Editor for Notes ──
  const quill = new Quill('#notes-editor', {
    theme: 'snow',
    placeholder: 'Enter Terms & Conditions, special notes...\n\ne.g.\n1. Rates are exclusive of GST.\n2. Payment to be made within 30 days of invoice.\n3. Security deposit of ₹5000 per set is applicable.',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'size': ['small', false, 'large'] }],
        ['link', 'clean'],
      ]
    }
  });

  // Load existing content into Quill
  const existingNotes = document.getElementById('notes-hidden').value;
  if (existingNotes.trim()) {
    quill.root.innerHTML = existingNotes;
  }

  // Before form submit — copy Quill HTML to hidden textarea
  document.getElementById('mq-form').addEventListener('submit', function () {
    document.getElementById('notes-hidden').value = quill.root.innerHTML;
  });
});
</script>

<style>
  .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,.15); }
  #items-table input[type="number"] { -moz-appearance: textfield; }
  #items-table input[type="number"]::-webkit-outer-spin-button,
  #items-table input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  .card { transition: all 0.2s ease; }

  /* Quill editor styling */
  #notes-editor {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    background: #fff;
  }
  .ql-toolbar.ql-snow {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem 0.375rem 0 0;
    background: #f8f9fa;
  }
  .ql-container.ql-snow {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    font-size: 14px;
  }
  .ql-editor { min-height: 160px; }
  .ql-editor.ql-blank::before { color: #adb5bd; font-style: normal; }
</style>
@endpush
@endsection
