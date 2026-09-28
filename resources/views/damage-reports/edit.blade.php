@extends('layouts.app')
@section('title', 'Edit – '.$damageReport->report_code)

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Edit Damage Report</h4>
      <p class="text-muted mb-0">{{ $damageReport->report_code }} — Update details &amp; regenerate invoice PDF</p>
    </div>
    <a href="{{ route('damage-reports.show', $damageReport) }}" class="btn btn-outline-secondary btn-md">
      <i class="bi bi-arrow-left me-2"></i>Back
    </a>
  </div>

  <form method="POST" action="{{ route('damage-reports.update', $damageReport) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ═══ CLIENT INFORMATION ═══ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-person-circle me-2 text-warning"></i>Client Information
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Client</label>
            <input type="text" class="form-control" value="{{ $damageReport->client_name }}" disabled>
            <small class="text-muted">Client cannot be changed after creation</small>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Linked Subscription</label>
            <input type="text" class="form-control"
                   value="{{ $damageReport->subscription ? $damageReport->subscription->subscription_code : 'Not linked' }}" disabled>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Billing Details (Bill To)</label>
            <textarea name="billing_details" class="form-control @error('billing_details') is-invalid @enderror" rows="4"
              placeholder="Company Name&#10;GST Number&#10;Address">{{ old('billing_details', $damageReport->billing_details) }}</textarea>
            @error('billing_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">These details will appear in the invoice as "Bill To" section</small>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ EQUIPMENT DETAILS ═══ --}}
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
                $oldUnits = old('walkie_units', $damageReport->walkie_units ?: [['model' => '', 'serial' => '', 'unit_id' => '']]);
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

    {{-- ═══ DAMAGE DETAILS ═══ --}}
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
                   value="{{ old('damage_date', $damageReport->damage_date->format('Y-m-d')) }}" required>
            @error('damage_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Replacement / Repair Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">₹</span>
              <input type="number" name="replacement_amount" class="form-control @error('replacement_amount') is-invalid @enderror"
                     value="{{ old('replacement_amount', $damageReport->replacement_amount) }}"
                     step="0.01" min="0" required>
            </div>
            @error('replacement_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
              // Parse existing reasons from stored description (lines starting with •)
              $storedDesc    = old('damage_description', $damageReport->damage_description ?? '');
              $storedReasons = old('damage_reasons', []);
              if (empty($storedReasons) && $storedDesc) {
                  // Extract bullet lines that match known reasons
                  foreach ($damageReasons as $r) {
                      if (str_contains($storedDesc, $r['label'])) {
                          $storedReasons[] = $r['label'];
                      }
                  }
              }
              // Extra note = last line that doesn't match any known reason
              $extraNote = old('damage_extra_note', '');
              if (!$extraNote && $storedDesc) {
                  $knownLabels = array_column($damageReasons, 'label');
                  $lines = array_filter(array_map(fn($l) => ltrim(trim($l), '•- '), explode("\n", $storedDesc)));
                  $extra = array_filter($lines, fn($l) => !in_array($l, $knownLabels));
                  $extraNote = implode('; ', $extra);
              }
            @endphp

            <div class="row g-2 mt-1">
              @foreach($damageReasons as $key => $reason)
              <div class="col-md-4 col-sm-6">
                <label class="d-flex align-items-center gap-2 p-3 border rounded w-100"
                       style="cursor:pointer; transition: all .15s; background:{{ in_array($reason['label'], $storedReasons) ? '#fff8e1' : '#fff' }};"
                       for="reason_edit_{{ $key }}"
                       onmouseover="this.style.background='#f8f9fa'"
                       onmouseout="this.style.background=document.getElementById('reason_edit_{{ $key }}').checked?'#fff8e1':'#fff'">
                  <input type="checkbox"
                         name="damage_reasons[]"
                         id="reason_edit_{{ $key }}"
                         value="{{ $reason['label'] }}"
                         class="reason-cb form-check-input mt-0 flex-shrink-0"
                         style="width:18px;height:18px;"
                         onchange="buildDescription(); this.closest('label').style.background=this.checked?'#fff8e1':'#fff'"
                         {{ in_array($reason['label'], $storedReasons) ? 'checked' : '' }}>
                  <i class="bi {{ $reason['icon'] }}" style="color:{{ $reason['color'] }}; font-size:1.1rem;"></i>
                  <span class="fw-medium" style="font-size:.88rem; line-height:1.3;">{{ $reason['label'] }}</span>
                </label>
              </div>
              @endforeach
            </div>

            <input type="hidden" name="damage_description" id="damage_description_hidden"
                   value="{{ $storedDesc }}" required>

            <div id="descriptionPreview" class="mt-3 p-3 rounded border" style="background:#f8f9fa; font-size:.88rem;">
              <strong class="text-muted small">📋 Invoice will show:</strong>
              <div id="descriptionPreviewText" class="mt-1 text-dark">{{ $storedDesc }}</div>
            </div>

            <small class="text-muted mt-1 d-block">
              <i class="bi bi-info-circle me-1"></i>
              Selected reasons are auto-compiled into the invoice description
            </small>
          </div>

          {{-- Additional Details --}}
          <div class="col-12">
            <label class="form-label fw-semibold">Additional Details (Optional)</label>
            <textarea name="damage_extra_note" id="damage_extra_note"
                      class="form-control" rows="2"
                      oninput="buildDescription()"
                      placeholder="e.g. Antenna broken from base due to fall...">{{ $extraNote }}</textarea>
            <small class="text-muted">Extra detail will be appended to the description in the invoice</small>
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ DAMAGE PHOTOS ═══ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-camera me-2 text-warning"></i>Damage Photographs
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">

          {{-- Existing Photos --}}
          @if($damageReport->damage_photos && count($damageReport->damage_photos) > 0)
          <div class="col-12">
            <label class="form-label fw-semibold">Existing Photos</label>
            <div class="d-flex flex-wrap gap-3">
              @foreach($damageReport->damage_photos as $photo)
              <div class="position-relative" id="photo-wrap-{{ $loop->index }}">
                <img src="{{ asset('storage/'.$photo) }}"
                     style="width:110px; height:110px; object-fit:cover; border-radius:8px; border:2px solid #dee2e6; box-shadow:0 2px 6px rgba(0,0,0,.1);"
                     id="photo-img-{{ $loop->index }}">
                <input type="checkbox" name="delete_photos[]" value="{{ $photo }}"
                       id="del-{{ $loop->index }}" class="d-none delete-photo-cb">
                <button type="button"
                        onclick="togglePhotoDelete({{ $loop->index }})"
                        id="del-btn-{{ $loop->index }}"
                        class="btn btn-sm btn-danger position-absolute"
                        style="top:4px;right:4px;padding:1px 6px;font-size:11px;border-radius:50%;line-height:1.4;"
                        title="Mark for deletion">✕</button>
              </div>
              @endforeach
            </div>
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>Click ✕ on a photo to mark it for deletion (dimmed = will be deleted on save)
            </small>
          </div>
          @endif

          {{-- Add New Photos --}}
          <div class="col-12">
            <label class="form-label fw-semibold">Add New Photos</label>
            <input type="file" name="new_damage_photos[]" class="form-control @error('new_damage_photos') is-invalid @enderror"
                   accept="image/*" multiple id="newPhotoInput">
            @error('new_damage_photos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Max 5 MB each. Supported: JPG, PNG, WebP.</small>
          </div>
          <div class="col-12">
            <div id="newPhotoPreview" class="d-flex flex-wrap gap-2"></div>
          </div>

        </div>
      </div>
    </div>

    {{-- ═══ NOTES ═══ --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-chat-text me-2 text-warning"></i>Notes / Internal Remarks
        </h5>
      </div>
      <div class="card-body p-4">
        <textarea name="notes" class="form-control" rows="3"
                  placeholder="Any internal notes...">{{ old('notes', $damageReport->notes) }}</textarea>
      </div>
    </div>

    {{-- Submit --}}
    <div class="mb-4">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="bi bi-save me-2"></i>Update &amp; Regenerate PDF
      </button>
      <a href="{{ route('damage-reports.show', $damageReport) }}" class="btn btn-secondary btn-lg">
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
// ── Build description from checked reasons + extra note ───────────────────────
function buildDescription() {
  const checked = [...document.querySelectorAll('.reason-cb:checked')].map(cb => cb.value);
  const extra   = (document.getElementById('damage_extra_note')?.value || '').trim();
  let parts     = checked.map(r => '• ' + r);
  if (extra) parts.push('• ' + extra);
  const compiled = parts.join('\n');
  document.getElementById('damage_description_hidden').value = compiled;
  const previewText = document.getElementById('descriptionPreviewText');
  if (previewText) previewText.innerText = compiled || '—';
}
buildDescription();

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

function togglePhotoDelete(idx) {
  const cb  = document.getElementById('del-' + idx);
  const img = document.getElementById('photo-img-' + idx);
  const btn = document.getElementById('del-btn-' + idx);
  cb.checked = !cb.checked;
  if (cb.checked) {
    img.style.opacity = '0.3';
    btn.classList.replace('btn-danger', 'btn-secondary');
    btn.title = 'Undo deletion';
  } else {
    img.style.opacity = '1';
    btn.classList.replace('btn-secondary', 'btn-danger');
    btn.title = 'Mark for deletion';
  }
}

document.getElementById('newPhotoInput').addEventListener('change', function () {
  const preview = document.getElementById('newPhotoPreview');
  preview.innerHTML = '';
  Array.from(this.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.innerHTML = `<img src="${e.target.result}" style="width:110px;height:110px;object-fit:cover;border-radius:8px;border:2px solid #dee2e6;box-shadow:0 2px 6px rgba(0,0,0,.1);">`;
      preview.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endpush
@endsection
