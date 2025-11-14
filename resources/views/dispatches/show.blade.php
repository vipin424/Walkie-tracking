@extends('layouts.app')
@section('content')
<style>
  :root {
    --primary: #3b82f6;
    --primary-hover: #2563eb;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
  }

  .modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gray-200);
  }

  .dispatch-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
  }

  .status-badge {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 1rem;
  }

  .status-returned { background: #d1fae5; color: #065f46; }
  .status-pending { background: #fef3c7; color: #92400e; }
  .status-dispatched { background: #dbeafe; color: #1e40af; }

  .btn-modern {
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
  }

  .btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-200);
    color: var(--gray-900);
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .grid-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 1.5rem;
  }

  .modern-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid var(--gray-200);
  }

  .card-header-modern {
    padding: 1.25rem 1.5rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    font-weight: 700;
    font-size: 1rem;
    color: var(--gray-900);
  }

  .card-body-modern {
    padding: 1.5rem;
  }

  .info-grid {
    display: grid;
    gap: 1.25rem;
  }

  .info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .info-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
  }

  .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
  }

  .items-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .items-table thead {
    background: var(--gray-50);
  }

  .items-table th {
    padding: 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    border-bottom: 2px solid var(--gray-200);
  }

  .items-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
    font-size: 0.875rem;
  }

  .items-table tbody tr:hover {
    background: var(--gray-50);
  }

  .quantity-badge {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-weight: 600;
    font-size: 0.875rem;
  }

  .qty-total { background: #dbeafe; color: #1e40af; }
  .qty-returned { background: #d1fae5; color: #065f46; }
  .qty-pending { background: #fee2e2; color: #991b1b; }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-label-modern {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
  }

  .form-input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
  }

  .form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .item-return-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
    align-items: end;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
  }

  .payment-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  @media (max-width: 1024px) {
    .grid-layout {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .modern-container {
      padding: 1rem;
    }

    .payment-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="modern-container">
  <!-- Header -->
  <div class="header-section">
    <div>
      <h1 class="dispatch-title">
        {{ $dispatch->code }}
        <span class="status-badge status-{{ strtolower($dispatch->status) }}">
          {{ $dispatch->status }}
        </span>
      </h1>
    </div>
    <a href="{{ route('dispatches.index') }}" class="btn-modern btn-secondary">
      ← Back to Dispatches
    </a>
  </div>

  <!-- Main Grid Layout -->
  <div class="grid-layout">
    <!-- Sidebar: Client & Dispatch Info -->
    <div>
      <div class="modern-card">
        <div class="card-header-modern">Dispatch Information</div>
        <div class="card-body-modern">
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Client</span>
              <span class="info-value">{{ $dispatch->client->name }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Dispatch Date</span>
              <span class="info-value">{{ $dispatch->dispatch_date->format('d M Y') }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Expected Return</span>
              <span class="info-value">{{ optional($dispatch->expected_return_date)->format('d M Y') ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Total Items</span>
              <span class="info-value">{{ $dispatch->total_items }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- whatsapp preview -->
      <div class="modern-card mt-4">
        <div class="card-header-modern">WhatsApp Preview</div>
        <div class="card-body-modern">
            <div class="card-body">
              <pre class="bg-light p-3 rounded small text-dark">{{ $dispatch->whatsappMessage() }}</pre>
              <a href="{{ $dispatch->whatsappLink() }}" target="_blank" class="btn btn-success">
                <i class="bi bi-whatsapp"></i> Open in WhatsApp
              </a>
            </div>
        </div>
      </div>
      <!-- End whatsapp -->

    </div>

    <!-- Main Content -->
    <div>

      <!-- Items Table -->
      <div class="modern-card" style="margin-bottom: 1.5rem;">
        <div class="card-header-modern">Dispatch Items</div>
        <div style="overflow-x: auto;">
          <table class="items-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>Brand</th>
                <th>Model</th>
                <th style="text-align: center;">Quantity</th>
                <th style="text-align: center;">Returned</th>
                <th style="text-align: center;">Pending</th>
              </tr>
            </thead>
            <tbody>
              @foreach($dispatch->items as $it)
              <tr>
                <td><strong>{{ $it->item_type }}</strong></td>
                <td>{{ $it->brand }}</td>
                <td>{{ $it->model }}</td>
                <td style="text-align: center;">
                  <span class="quantity-badge qty-total">{{ $it->quantity }}</span>
                </td>
                <td style="text-align: center;">
                  <span class="quantity-badge qty-returned">{{ $it->returned_qty }}</span>
                </td>
                <td style="text-align: center;">
                  <span class="quantity-badge qty-pending">{{ $it->quantity - $it->returned_qty }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Return Form -->
      <div class="modern-card" style="margin-bottom: 1.5rem;">
        <div class="card-header-modern">📦 Record Item Return</div>
        <div class="card-body-modern">
          <form method="post" action="{{ route('returns.store') }}">
            @csrf
            <input type="hidden" name="dispatch_id" value="{{ $dispatch->id }}">
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label-modern">Return Date</label>
                <input type="date" name="return_date" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label-modern">Remarks</label>
                <input name="remarks" class="form-input" placeholder="Optional notes">
              </div>
            </div>

            <div style="margin-top: 1.5rem;">
              <label class="form-label-modern" style="margin-bottom: 1rem;">Return Quantities</label>
              @foreach($dispatch->items as $i => $it)
                @php $pending = $it->quantity - $it->returned_qty; @endphp
                @if($pending > 0)
                <div class="item-return-row">
                  <div>
                    <strong>{{ $it->item_type }}</strong> - {{ $it->brand }} {{ $it->model }}
                    <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">
                      Pending: {{ $pending }} units
                    </div>
                    <input type="hidden" name="items[{{ $i }}][dispatch_item_id]" value="{{ $it->id }}">
                  </div>
                  <div>
                    <input type="number" 
                           name="items[{{ $i }}][returned_qty]" 
                           class="form-input" 
                           min="1" 
                           max="{{ $pending }}" 
                           placeholder="Quantity">
                  </div>
                </div>
                @endif
              @endforeach
            </div>

            <button type="submit" class="btn-modern btn-primary" style="margin-top: 1rem;">
              💾 Save Return
            </button>
          </form>
        </div>
      </div>

      <!-- Payment Form -->
      <div class="modern-card">
        <div class="card-header-modern">💰 Payment Details</div>
        <div class="card-body-modern">
          <form method="post" action="{{ route('payments.store', $dispatch) }}">
            @csrf
            
            <div class="payment-grid">
              <div class="form-group">
                <label class="form-label-modern">Payment Status</label>
                <select name="payment_status" class="form-input">
                  @foreach(['Paid','Unpaid','Advance Received'] as $s)
                    <option value="{{ $s }}" @selected($dispatch->payment?->payment_status === $s)>{{ $s }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label class="form-label-modern">Advance Amount (₹)</label>
                <input type="number" 
                       step="0.01" 
                       name="advance_amount" 
                       class="form-input" 
                       value="{{ $dispatch->payment?->advance_amount }}"
                       placeholder="0.00">
              </div>

              <div class="form-group">
                <label class="form-label-modern">Total Amount (₹)</label>
                <input type="number" 
                       step="0.01" 
                       name="total_amount" 
                       class="form-input" 
                       value="{{ $dispatch->payment?->total_amount }}"
                       placeholder="0.00">
              </div>

              <div class="form-group">
                <label class="form-label-modern">Payment Remarks</label>
                <input name="remarks" 
                       class="form-input" 
                       value="{{ $dispatch->payment?->remarks }}"
                       placeholder="Optional notes">
              </div>
            </div>

            <button type="submit" class="btn-modern btn-primary" style="margin-top: 1rem;">
              ✓ Update Payment
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection