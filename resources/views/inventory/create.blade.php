@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('inventory.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-semibold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Inventory Item</h4>
    </div>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm" style="border-radius:16px">
            <div class="card-body p-4">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                        <input type="text"
                               name="serial_number"
                               class="form-control font-monospace text-uppercase @error('serial_number') is-invalid @enderror"
                               value="{{ old('serial_number') }}"
                               style="letter-spacing:1px"
                               placeholder="e.g. KW-2024-001"
                               required>
                        @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Walkie Model</label>
                        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror">
                            <option value="">— कोई model नहीं —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}{{ $item->type ? ' ('.$item->type.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\WalkieInventory::statusOptions() as $val => $label)
                                <option value="{{ $val }}" {{ old('status', 'available') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Condition</label>
                        <select name="condition" class="form-select">
                            @foreach(\App\Models\WalkieInventory::conditionOptions() as $val => $label)
                                <option value="{{ $val }}" {{ old('condition', 'good') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="कोई extra जानकारी...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Save
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
