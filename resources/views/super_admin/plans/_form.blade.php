<div class="mb-3">
  <label class="form-label">Plan Name *</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Price (₹/month) *</label>
  <input type="number" name="price" class="form-control" value="{{ old('price', $plan->price ?? 0) }}" min="0" step="0.01" required>
</div>
<div class="mb-3">
  <label class="form-label">Max Orders/month *</label>
  <input type="number" name="max_orders" class="form-control" value="{{ old('max_orders', $plan->max_orders ?? 50) }}" min="1" required>
</div>
<div class="mb-3">
  <label class="form-label">Max Invoices/month *</label>
  <input type="number" name="max_invoices" class="form-control" value="{{ old('max_invoices', $plan->max_invoices ?? 100) }}" min="1" required>
</div>
<div class="mb-3">
  <label class="form-label">Max Users *</label>
  <input type="number" name="max_users" class="form-control" value="{{ old('max_users', $plan->max_users ?? 5) }}" min="1" required>
</div>
@isset($plan)
<div class="mb-3 form-check">
  <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true))>
  <label class="form-check-label" for="is_active">Active</label>
</div>
@endisset
