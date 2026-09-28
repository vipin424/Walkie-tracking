@extends('layouts.app')
@section('title', 'New Damage Report')

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">New Damage Report</h4>
      <p class="text-muted mb-0">Report physical damage and generate replacement invoice</p>
    </div>
    <a href="{{ route('damage-reports.index') }}" class="btn btn-outline-secondary btn-md">
      <i class="bi bi-arrow-left me-2"></i>Back to List
    </a>
  </div>

  <form method="POST" action="{{ route('damage-reports.store') }}" enctype="multipart/form-data" id="damageForm">
    @csrf

    {{-- ═══════════════════════════════════════════
         CLIENT INFORMATION
    ════════════════════════════════════════════ --}}
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
                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                  {{ $client->name }} - {{ $client->contact_number }}{{ $client->company_name ? ' (' . $client->company_name . ')' : '' }}
                </option>
              @endforeach
            </select>
            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Select the client who damaged the equipment</small>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Linked Subscription (Optional)</label>
            <select name="subscription_id" class="form-select @error('subscription_id') is-invalid @enderror">
              <option value="">-- Not linked to a subscription --</option>
              @foreach($subscriptions as $sub)
                <option value="{{ $sub->id }}" {{ old('subscription_id') == $sub->id ? 'selected' : '' }}>
                  {{ $sub->subscription_code }} – {{ $sub->client_name }}
                </option>
              @endforeach
            </select>
            @error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Link to an active monthly subscription for reference</small>
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Billing Details (Bill To)</label>
            <textarea name="billing_details" class="form-control @error('billing_details') is-invalid @enderror" rows="4"
              placeholder="Enter complete billing details like:&#10;Company Name&#10;GST Number: XXXXXXXXXXXX&#10;Address: Full Address&#10;Phone: XXXXXXXXXX">{{ old('billing_details') }}</textarea>
            @error('billing_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">If blank, client name &amp; contact will be used in the invoice</small>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════
         EQUIPMENT DETAILS
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-broadcast me-2 text-warning"></i>Equipment Details
          </h5>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addUnitBtn">
            <i class="bi bi-plus-circle me-1"></i>Add Unit
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="walkieUnitsTable">
            <thead class="bg-light">
              <tr>
                <th class="px-4 py-3 text-muted fw-semibold">Walkie Model</th>
                <th class="px-4 py-3 text-muted fw-semibold">Serial Number</th>
                <th class="px-4 py-3 text-muted fw-semibold">Unit ID / Asset No.</th>
                <th class="px-4 py-3 text-muted fw-semibold text-center" style="width:60px;"></th>
              </tr>
            </thead>
            <tbody id="walkieUnitsContainer">
              @php
                $oldUnits = old('walkie_units', [['model' => '', 'serial' => '', 'unit_id' => '']]);
              @endphp
              @foreach($oldUnits as $i => $unit)
              <tr class="unit-row">
                <td class="px-4 py-2">
                  <input type="text" name="walkie_units[{{ $i }}][model]" class="form-control form-control-sm"
                         value="{{ $unit['model'] }}" placeholder="e.g. Motorola DP1400">
                </td>
                <td class="px-4 py-2">
                  <input type="text" name="walkie_units[{{ $i }}][serial]" class="form-control form-control-sm"
                         value="{{ $unit['serial'] }}" placeholder="Serial / IMEI Number">
                </td>
                <td class="px-4 py-2">
                  <input type="text" name="walkie_units[{{ $i }}][unit_id]" class="form-control form-control-sm"
                         value="{{ $unit['unit_id'] }}" placeholder="e.g. WT-042">
                </td>
                <td class="px-4 py-2 text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-unit-btn" {{ count($oldUnits) == 1 ? 'disabled' : '' }}>
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════
         DAMAGE DETAILS
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-tools me-2 text-warning"></i>Damage Details &amp; Charges
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Date of Damage <span class="text-danger">*</span></label>
            <input type="date" name="damage_date" class="form-control @error('damage_date') is-invalid @enderror"
                   value="{{ old('damage_date', date('Y-m-d')) }}" required>
            @error('damage_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">When was the damage discovered or reported</small>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Replacement / Repair Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">₹</span>
              <input type="number" name="replacement_amount" class="form-control @error('replacement_amount') is-invalid @enderror"
                     value="{{ old('replacement_amount') }}" step="0.01" min="0" placeholder="0.00" required>
            </div>
            @error('replacement_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Amount to be charged from client for damage</small>
          </div>

          {{-- Damage Reasons — Multi-select checkboxes --}}
          <div class="col-12">
            <label class="form-label fw-semibold">
              Damage Reasons <span class="text-danger">*</span>
              <small class="text-muted fw-normal ms-2">Select all that apply</small>
            </label>

            @php
              $damageReasons = [
                'antenna'  => ['label' => 'Antenna Broken',           'icon' => 'bi-broadcast',        'color' => '#e65100'],
                'screen'   => ['label' => 'Screen Cracked / Damaged', 'icon' => 'bi-phone-landscape',  'color' => '#1565c0'],
                'body'     => ['label' => 'Body Damage / Dent',       'icon' => 'bi-shield-x',         'color' => '#6a1b9a'],
                'speaker'  => ['label' => 'Speaker Not Working',      'icon' => 'bi-volume-mute',      'color' => '#00695c'],
                'mic'      => ['label' => 'Microphone Damaged',       'icon' => 'bi-mic-mute',         'color' => '#558b2f'],
                'battery'  => ['label' => 'Battery Damaged / Lost',   'icon' => 'bi-battery-full',     'color' => '#f57f17'],
                'charger'  => ['label' => 'Charger / Cable Lost',     'icon' => 'bi-plug',             'color' => '#37474f'],
                'keypad'   => ['label' => 'Keypad / Button Damaged',  'icon' => 'bi-grid-3x3-gap',     'color' => '#ad1457'],
                'ptt'      => ['label' => 'PTT Button Damaged',       'icon' => 'bi-record-btn',       'color' => '#0277bd'],
                'earphone' => ['label' => 'Earphone / Accessory Lost','icon' => 'bi-headphones',       'color' => '#4e342e'],
                'water'    => ['label' => 'Water / Liquid Damage',    'icon' => 'bi-droplet',          'color' => '#01579b'],
                'lost'     => ['label' => 'Unit Lost / Missing',      'icon' => 'bi-question-circle',  'color' => '#b71c1c'],
              ];
              $oldReasons = old('damage_reasons', []);
            @endphp

            <div class="row g-2 mt-1">
              @foreach($damageReasons as $key => $reason)
              <div class="col-md-4 col-sm-6">
                <label class="d-flex align-items-center gap-2 p-3 border rounded w-100"
                       style="cursor:pointer; transition: all .15s; background:#fff;"
                       for="reason_{{ $key }}"
                       onmouseover="this.style.background='#f8f9fa'"
                       onmouseout="this.style.background=document.getElementById('reason_{{ $key }}').checked?'#fff8e1':'#fff'">
                  <input type="checkbox"
                         name="damage_reasons[]"
                         id="reason_{{ $key }}"
                         value="{{ $reason['label'] }}"
                         class="reason-cb form-check-input mt-0 flex-shrink-0"
                         style="width:18px;height:18px;"
                         onchange="buildDescription(); this.closest('label').style.background=this.checked?'#fff8e1':'#fff'"
                         {{ in_array($reason['label'], $oldReasons) ? 'checked' : '' }}>
                  <i class="bi {{ $reason['icon'] }}" style="color:{{ $reason['color'] }}; font-size:1.1rem;"></i>
                  <span class="fw-medium" style="font-size:.88rem; line-height:1.3;">{{ $reason['label'] }}</span>
                </label>
              </div>
              @endforeach
            </div>

            {{-- Hidden field that stores the compiled description string --}}
            <input type="hidden" name="damage_description" id="damage_description_hidden" value="{{ old('damage_description') }}" required>

            {{-- Live preview of compiled description --}}
            <div id="descriptionPreview" class="mt-3 p-3 rounded border" style="background:#f8f9fa; display:none; font-size:.88rem;">
              <strong class="text-muted small">📋 Invoice will show:</strong>
              <div id="descriptionPreviewText" class="mt-1 text-dark"></div>
            </div>

            @error('damage_reasons')<div class="text-danger small mt-1">Please select at least one damage reason.</div>@enderror
            <small class="text-muted mt-1 d-block">
              <i class="bi bi-info-circle me-1"></i>
              Selected reasons will be auto-compiled into the invoice description
            </small>
          </div>

          {{-- Additional Details --}}
          <div class="col-12">
            <label class="form-label fw-semibold">Additional Details (Optional)</label>
            <textarea name="damage_extra_note" id="damage_extra_note"
                      class="form-control" rows="2"
                      oninput="buildDescription()"
                      placeholder="e.g. Antenna broken from base due to fall, unit dropped from 10 feet height...">{{ old('damage_extra_note') }}</textarea>
            <small class="text-muted">Extra detail will be appended to the description in the invoice</small>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════
         DAMAGE PHOTOS
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-camera me-2 text-warning"></i>Damage Photographs
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Upload Damage Photos</label>
            <input type="file" name="damage_photos[]" id="photoInput"
                   class="form-control @error('damage_photos') is-invalid @enderror"
                   accept="image/*" multiple>
            @error('damage_photos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>
              Upload clear photos of the damaged equipment. Max 5 MB each. Supported: JPG, PNG, WebP.
              Photos will appear in the invoice PDF.
            </small>
          </div>

          {{-- Photo preview --}}
          <div class="col-12">
            <div id="photoPreview" class="d-flex flex-wrap gap-2"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════
         NOTES
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Internal Remarks
        </h5>
      </div>
      <div class="card-body p-4">
        <textarea name="notes" class="form-control" rows="3"
                  placeholder="Any internal notes about this damage report...">{{ old('notes') }}</textarea>
      </div>
    </div>

    {{-- Submit --}}
    <div class="mb-4">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="bi bi-file-earmark-x-fill me-2"></i>Create Damage Report &amp; Generate Invoice
      </button>
      <a href="{{ route('damage-reports.index') }}" class="btn btn-secondary btn-lg">
        <i class="bi bi-x-circle me-2"></i>Cancel
      </a>
    </div>

  </form>
</div>

<style>
  .card { transition: all 0.3s ease; }
  .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15); }
</style>

@push('scripts')
<script>
// ── Build description from checked reasons + extra note ──────────────────────
function buildDescription() {
  const checked = [...document.querySelectorAll('.reason-cb:checked')].map(cb => cb.value);
  const extra   = (document.getElementById('damage_extra_note')?.value || '').trim();

  let parts = checked.map(r => '• ' + r);
  if (extra) parts.push('• ' + extra);

  const compiled = parts.join('\n');
  document.getElementById('damage_description_hidden').value = compiled;

  const preview     = document.getElementById('descriptionPreview');
  const previewText = document.getElementById('descriptionPreviewText');
  if (compiled) {
    previewText.innerText = compiled;
    preview.style.display = 'block';
  } else {
    preview.style.display = 'none';
  }
}

// ── Dynamic Walkie Units ──────────────────────────────────────────────────────
let unitIndex = document.querySelectorAll('.unit-row').length;
document.getElementById('addUnitBtn').addEventListener('click', function() {
    const tbody = document.getElementById('walkieUnitsContainer');
    const tr = document.createElement('tr');
    tr.className = 'unit-row';
    tr.innerHTML = `
        <td class="px-4 py-2">
            <input type="text" name="walkie_units[${unitIndex}][model]" class="form-control form-control-sm" placeholder="e.g. Motorola DP1400">
        </td>
        <td class="px-4 py-2">
            <input type="text" name="walkie_units[${unitIndex}][serial]" class="form-control form-control-sm" placeholder="Serial / IMEI Number">
        </td>
        <td class="px-4 py-2">
            <input type="text" name="walkie_units[${unitIndex}][unit_id]" class="form-control form-control-sm" placeholder="e.g. WT-042">
        </td>
        <td class="px-4 py-2 text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-unit-btn">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    unitIndex++;
    updateRemoveButtons();
});

document.getElementById('walkieUnitsContainer').addEventListener('click', function(e) {
    if(e.target.closest('.remove-unit-btn')) {
        e.target.closest('tr').remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const btns = document.querySelectorAll('.remove-unit-btn');
    if(btns.length === 1) {
        btns[0].disabled = true;
    } else {
        btns.forEach(btn => btn.disabled = false);
    }
}

// ── Restore checked state styling on page load (e.g. validation errors) ──────
document.querySelectorAll('.reason-cb').forEach(cb => {
  if (cb.checked) {
    cb.closest('label').style.background = '#fff8e1';
  }
});
buildDescription();

// ── Form validation: at least 1 reason required ──────────────────────────────
document.getElementById('damageForm').addEventListener('submit', function (e) {
  const anyChecked = document.querySelectorAll('.reason-cb:checked').length > 0;
  if (!anyChecked) {
    e.preventDefault();
    // Highlight the section
    document.getElementById('damage_description_hidden').value = '';
    const grid = document.querySelector('.row.g-2.mt-1');
    grid.style.border = '2px solid #dc3545';
    grid.style.borderRadius = '8px';
    grid.style.padding = '8px';
    // Show error message
    let errEl = document.getElementById('reason-error');
    if (!errEl) {
      errEl = document.createElement('div');
      errEl.id = 'reason-error';
      errEl.className = 'text-danger small mt-2 fw-semibold';
      errEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Please select at least one damage reason.';
      grid.parentElement.appendChild(errEl);
    }
    grid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }
  buildDescription();
});

// ── Photo preview ─────────────────────────────────────────────────────────────
document.getElementById('photoInput').addEventListener('change', function () {
  const preview = document.getElementById('photoPreview');
  preview.innerHTML = '';
  Array.from(this.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.style.cssText = 'position:relative;';
      div.innerHTML = `
        <img src="${e.target.result}"
             style="width:110px; height:110px; object-fit:cover; border-radius:8px; border:2px solid #dee2e6; box-shadow:0 2px 6px rgba(0,0,0,.1);">
        <div style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,.5);color:white;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:11px;cursor:pointer;">✕</div>
      `;
      preview.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endpush
@endsection
