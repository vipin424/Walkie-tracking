@extends('layouts.app')
@section('title','New Dispatch')
@section('content')
<div class="container-fluid px-4 py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold">Create New Dispatch</h4>
      <p class="text-muted mb-0">Fill in the details to create a new dispatch order</p>
    </div>
    <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Back to List
    </a>
  </div>

  <form method="post" action="{{ route('dispatches.store') }}" id="dispatchForm">
    @csrf

    <!-- Client & Date Information Card -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-info-circle me-2 text-primary"></i>Dispatch Information
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">
              Select Client <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-primary bg-opacity-10 border-0">
                <i class="bi bi-person-fill text-primary"></i>
              </span>
              <select name="client_id" class="form-select border-0 shadow-sm" required>
                <option value="">-- Choose Client --</option>
                @foreach($clients as $c)
                  <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->contact_number }})</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">
              Dispatch Date <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-info bg-opacity-10 border-0">
                <i class="bi bi-calendar-event text-info"></i>
              </span>
              <input type="date" name="dispatch_date" class="form-control border-0 shadow-sm" required value="{{ date('Y-m-d') }}">
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">
              Expected Return Date
            </label>
            <div class="input-group">
              <span class="input-group-text bg-warning bg-opacity-10 border-0">
                <i class="bi bi-calendar-check text-warning"></i>
              </span>
              <input type="date" name="expected_return_date" class="form-control border-0 shadow-sm">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Items Section Card -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 p-4">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-box-seam me-2 text-success"></i>Dispatch Items
          </h5>
          <button type="button" class="btn btn-warning" onclick="addItem()">
            <i class="bi bi-plus-circle me-2"></i>Add Item
          </button>
        </div>
      </div>
      <div class="card-body p-4" style="background-color: #f8f9fa;">
        <div id="items"></div>
        <div id="emptyState" class="text-center py-5 text-muted">
          <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
          <p class="mb-0">No items added yet. Click "Add Item" to get started.</p>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="d-flex justify-content-end gap-3">
      <a href="{{ route('dispatches.index') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-x-circle me-2"></i>Cancel
      </a>
      <button type="submit" class="btn btn-warning px-4 shadow">
        <i class="bi bi-check-circle me-2"></i>Create Dispatch
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
  // Hide empty state
  document.getElementById('emptyState').style.display = 'none';

  const itemTypes = ['Walkie', 'Charger', 'Headphone', 'Battery', 'Accessory'];
  const brands = ['Access', 'Tokimo', 'Turbo', 'Motorola'];

  let itemOptions = itemTypes.map(t => `<option value="${t}">${t}</option>`).join('');
  let brandOptions = brands.map(b => `<option value="${b}">${b}</option>`).join('');

  const itemCard = `
  <div class="card border-0 shadow-sm mb-3 item-row" style="animation: slideIn 0.3s ease;">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0 fw-semibold text-secondary">
          <i class="bi bi-box me-2"></i>Item #${itemIndex + 1}
        </h6>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
          <i class="bi bi-trash me-1"></i>Remove
        </button>
      </div>
      
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Item Type <span class="text-danger">*</span></label>
          <select name="items[${itemIndex}][item_type]" class="form-select" required>
            <option value="">Select Type</option>
            ${itemOptions}
          </select>
        </div>
        
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Brand</label>
          <select name="items[${itemIndex}][brand]" class="form-select">
            <option value="">Select Brand</option>
            ${brandOptions}
          </select>
        </div>
        
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">Model</label>
          <input name="items[${itemIndex}][model]" class="form-control" placeholder="Enter model No.">
        </div>
        
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">Quantity <span class="text-danger">*</span></label>
          <input type="number" min="1" name="items[${itemIndex}][quantity]" class="form-control" placeholder="0" required>
        </div>
       <div class="col-md-2"> <label class="form-label fw-semibold small text-muted">Rate <span class="text-danger">*</span></label> 
       <input type="number" min="0" step="0.01" name="items[${itemIndex}][rate_per_day]" class="form-control" placeholder="Rate/day" required> 
       </div>

        
      </div>
    </div>
  </div>`;

  document.getElementById('items').insertAdjacentHTML('beforeend', itemCard);
  itemIndex++;
}

function removeItem(button) {
  const itemRow = button.closest('.item-row');
  itemRow.style.animation = 'slideOut 0.3s ease';
  
  setTimeout(() => {
    itemRow.remove();
    
    // Show empty state if no items
    const itemsContainer = document.getElementById('items');
    if (itemsContainer.children.length === 0) {
      document.getElementById('emptyState').style.display = 'block';
    }
    
    // Renumber remaining items
    updateItemNumbers();
  }, 300);
}

function updateItemNumbers() {
  const items = document.querySelectorAll('.item-row');
  items.forEach((item, index) => {
    const header = item.querySelector('h6');
    header.innerHTML = `<i class="bi bi-box me-2"></i>Item #${index + 1}`;
  });
}


// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
  addItem();
});
</script>

<style>
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideOut {
  from {
    opacity: 1;
    transform: translateY(0);
  }
  to {
    opacity: 0;
    transform: translateY(-20px);
  }
}

.form-select:focus,
.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.input-group-text {
  font-size: 0.90rem;
}

.card {
  transition: all 0.3s ease;
}

.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-2px);
}

.btn:active {
  transform: translateY(0);
}
</style>
@endpush

@endsection