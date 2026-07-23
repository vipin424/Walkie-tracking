@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
<div class="container-fluid p-4">
  <div class="mb-4">
    <h4 class="fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Item</h4>
  </div>

  <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
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
            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
              <option value="">-- Select Category --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Item Image</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Unit Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', 0) }}" required>
            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Security Deposit</label>
            <input type="number" step="0.01" name="security_deposit" class="form-control @error('security_deposit') is-invalid @enderror" value="{{ old('security_deposit', 0) }}">
            @error('security_deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Total Stock <span class="text-danger">*</span></label>
            <input type="number" name="total_stock" class="form-control @error('total_stock') is-invalid @enderror" value="{{ old('total_stock', 1) }}" required>
            @error('total_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Tax %</label>
            <input type="number" step="0.01" name="tax_percent" class="form-control @error('tax_percent') is-invalid @enderror" value="{{ old('tax_percent', 0) }}">
            @error('tax_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-semibold">Buffer Days (Before)</label>
            <input type="number" name="buffer_days_before" class="form-control" value="{{ old('buffer_days_before', 0) }}">
            <small class="text-muted">Days needed to prep item.</small>
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-semibold">Buffer Days (After)</label>
            <input type="number" name="buffer_days_after" class="form-control" value="{{ old('buffer_days_after', 0) }}">
            <small class="text-muted">Days needed for inspection after return.</small>
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
