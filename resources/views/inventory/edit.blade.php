@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('inventory.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-semibold mb-0"><i class="bi bi-pencil me-2 text-primary"></i>Edit Inventory Item</h4>
            <p class="text-muted small mb-0">Serial: <span class="font-monospace fw-bold">{{ $inventory->serial_number }}</span></p>
        </div>
    </div>

    <form action="{{ route('inventory.update', $inventory) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card border-0 shadow-sm" style="border-radius:16px">
            <div class="card-body p-4">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                        <input type="text"
                               name="serial_number"
                               class="form-control font-monospace text-uppercase @error('serial_number') is-invalid @enderror"
                               value="{{ old('serial_number', $inventory->serial_number) }}"
                               style="letter-spacing:1px"
                               required>
                        @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Walkie Model</label>
                        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror">
                            <option value="">— कोई model नहीं —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('item_id', $inventory->item_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}{{ $item->type ? ' ('.$item->type.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(\App\Models\WalkieInventory::statusOptions() as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $inventory->status) == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                        <select name="condition" class="form-select @error('condition') is-invalid @enderror">
                            @foreach(\App\Models\WalkieInventory::conditionOptions() as $val => $label)
                                <option value="{{ $val }}" {{ old('condition', $inventory->condition) == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Purchase Date</label>
                        <input type="date"
                               name="purchase_date"
                               class="form-control @error('purchase_date') is-invalid @enderror"
                               value="{{ old('purchase_date', $inventory->purchase_date?->format('Y-m-d')) }}">
                        @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $inventory->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Update
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
