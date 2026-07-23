@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container-fluid p-4">
  <div class="mb-4">
    <h4 class="fw-semibold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Category</h4>
  </div>

  <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="row g-3">
          
          <div class="col-md-6">
            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Category Image</label>
            @if($category->image_path)
              <div class="mb-2">
                <img src="{{ Str::startsWith($category->image_path, 'http') ? $category->image_path : asset('storage/' . $category->image_path) }}" alt="Current Image" width="100" class="img-thumbnail">
              </div>
            @endif
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Leave blank to keep current image</small>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $category->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Badge Color (Hex)</label>
            <input type="color" name="badge_color" class="form-control form-control-color" value="{{ old('badge_color', $category->badge_color ?? '#03a9f4') }}" title="Choose your color">
            <small class="text-muted">Used for the bottom strip on featured cards.</small>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Featured Category</label>
            <select name="is_featured" class="form-select">
              <option value="0" {{ !$category->is_featured ? 'selected' : '' }}>No (Top Carousel only)</option>
              <option value="1" {{ $category->is_featured ? 'selected' : '' }}>Yes (Large Card section)</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="is_active" class="form-select">
              <option value="1" {{ $category->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$category->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

        </div>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-2"></i>Update Category
      </button>
      <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
