@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<div class="container-fluid p-4">
  <div class="mb-4">
    <h4 class="fw-semibold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Item</h4>
  </div>

  <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
              <option value="">-- Select Category --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Item Image</label>
            @if($item->image_path)
              <div class="mb-2">
                <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}" alt="Current Image" width="100" class="img-thumbnail">
              </div>
            @endif
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Leave blank to keep current image</small>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $item->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Unit Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $item->unit_price) }}" required>
            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Security Deposit</label>
            <input type="number" step="0.01" name="security_deposit" class="form-control @error('security_deposit') is-invalid @enderror" value="{{ old('security_deposit', $item->security_deposit) }}">
            @error('security_deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Total Stock <span class="text-danger">*</span></label>
            <input type="number" name="total_stock" class="form-control @error('total_stock') is-invalid @enderror" value="{{ old('total_stock', $item->total_stock) }}" required>
            @error('total_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Tax %</label>
            <input type="number" step="0.01" name="tax_percent" class="form-control @error('tax_percent') is-invalid @enderror" value="{{ old('tax_percent', $item->tax_percent) }}">
            @error('tax_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-semibold">Buffer Days (Before)</label>
            <input type="number" name="buffer_days_before" class="form-control" value="{{ old('buffer_days_before', $item->buffer_days_before) }}">
            <small class="text-muted">Days needed to prep item.</small>
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-semibold">Buffer Days (After)</label>
            <input type="number" name="buffer_days_after" class="form-control" value="{{ old('buffer_days_after', $item->buffer_days_after) }}">
            <small class="text-muted">Days needed for inspection after return.</small>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="is_active" class="form-select">
              <option value="1" {{ $item->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$item->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-2"></i>Update Item
      </button>
      <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
