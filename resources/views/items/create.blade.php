@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
<div class="container-fluid p-4">
  <div class="mb-4">
    <h4 class="fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Item</h4>
  </div>

  <form action="{{ route('items.store') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Type</label>
            <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}">
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Unit Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', 0) }}" required>
            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Tax %</label>
            <input type="number" step="0.01" name="tax_percent" class="form-control @error('tax_percent') is-invalid @enderror" value="{{ old('tax_percent', 0) }}">
            @error('tax_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="is_active" class="form-select">
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-2"></i>Save Item
      </button>
      <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
