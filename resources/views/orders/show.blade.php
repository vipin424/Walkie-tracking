@extends('layouts.app')
@section('title', 'Order Details')
@section('content')
<div class="container-fluid px-4 py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-2"></i>Back to Orders
      </a>
      <h4 class="mb-1 fw-bold">Order: {{ $order->order_code }}</h4>
      <p class="text-muted mb-0">View and manage order details</p>
    </div>
    <div>
      @php
        $statusColors = [
          'pending' => 'warning',
          'approved' => 'success',
          'rejected' => 'danger'
        ];
        $color = $statusColors[$order->status] ?? 'secondary';
      @endphp
      <span class="badge bg-{{ $color }} px-4 py-2 fs-6" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.15) !important; color: var(--bs-{{ $color }}) !important;">
        {{ ucfirst($order->status) }}
      </span>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      
      <!-- Primary Actions -->
      <div class="mb-3">
        <h6 class="text-muted mb-3 fw-semibold">Order Actions</h6>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square me-2"></i>Edit Order
          </a>

          <form action="{{ route('orders.generatePdf', $order) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-primary">
              <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
            </button>
          </form>

          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#emailModal">
            <i class="bi bi-envelope me-2"></i>Send Email
          </button>

          <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#waModal">
            <i class="bi bi-whatsapp me-2"></i>Send WhatsApp
          </button>

          @if($order->pdf_path)
          <a href="{{ Storage::url(str_replace('storage/','public/',$order->pdf_path)) }}?v={{ $order->updated_at->timestamp }}"
            target="_blank"
            class="btn btn-outline-secondary">
            <i class="bi bi-eye me-2"></i> View PDF
          </a>
          @endif

          {{-- Combined Dues PDF: only when settled + payment still pending/partial --}}
          @if($order->settlement_status === 'settled' && in_array($order->payment_status, ['pending','partial']) && $order->final_payable > 0)
          <a href="{{ route('orders.combinedDuesPdf', $order) }}" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Combined Dues Statement
          </a>
          @endif

        </div>
      </div>

      <!-- Agreement Actions -->
      @if($order->agreement_required)
        <hr class="my-3">
        
        <div>
          <h6 class="text-muted mb-3 fw-semibold">Agreement Actions</h6>
          
          @if($order->agreement && $order->agreement->status === 'signed')
            <!-- Agreement Signed Status -->
            <div class="alert alert-success d-inline-flex align-items-center mb-0 py-2">
              <i class="bi bi-check-circle-fill me-2"></i>
              <strong>Agreement Signed</strong>
            </div>
            
          @else
            <!-- Agreement Actions -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
              
              @if(!$order->agreement || $order->agreement->status !== 'signed')
                <form action="{{ route('orders.generateAgreement', $order) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-warning">
                    <i class="bi bi-file-earmark-text me-2"></i>Generate Agreement Link
                  </button>
                </form>
              @endif

              @if($order->agreement && $order->agreement->status !== 'signed')

               @if ($order->agreement && $order->agreement->signed_url)
                      <button type="button"
                              class="btn btn-success"
                              data-bs-toggle="modal"
                              data-bs-target="#sendAgreementMailModal">
                          <i class="bi bi-envelope me-2"></i>Send Agreement Email
                      </button>

                      <a href="{{ route('orders.sendAgreementWhatsapp', $order) }}"
                        class="btn btn-info text-white">
                        <i class="bi bi-whatsapp me-2"></i>Send Agreement WhatsApp
                      </a>

                      <button type="button"
                            class="btn btn-outline-secondary"
                            id="copyLinkBtn"
                            data-link="{{ $order->agreement->signed_url }}">
                      <i class="bi bi-clipboard me-2"></i>Copy Link
                    </button>
                @endif
                  {{-- Include Modal --}}
                  @include('orders.partials.agreement-email-modal')
                <span class="badge bg-warning text-dark py-2 px-3">
                  <i class="bi bi-clock-history me-1"></i>Pending Signature
                </span>
              @endif
              
            </div>
          @endif
        </div>
      @endif

    </div>
  </div>


  <div class="row">
    <!-- Client Information -->
    <div class="col-lg-3 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-person-circle me-2 text-warning"></i>Client Information
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label class="text-muted small fw-semibold mb-1">Name</label>
            <p class="mb-0 fw-medium">{{ $order->client_name }}</p>
          </div>
          <div class="mb-3">
            <label class="text-muted small fw-semibold mb-1">Phone</label>
            <p class="mb-0">
              <i class="bi bi-telephone-fill text-success me-2"></i>{{ $order->client_phone }}
            </p>
          </div>
        @if($order->client_email)
          <div class="mb-0">
            <label class="text-muted small fw-semibold mb-1">Email</label>
            <p class="mb-0">
              <i class="bi bi-envelope-fill text-primary me-2"></i>{{ $order->client_email }}
            </p>
          </div>
        @endif
        </div>
      </div>
    </div>

    <!-- Event Details -->
    <div class="col-lg-3 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-calendar-event me-2 text-warning"></i>Event Details
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label class="text-muted small fw-semibold mb-1">Event Period</label>
            <p class="mb-0 fw-medium">
              {{ \Carbon\Carbon::parse($order->event_from)->format('d M Y') }} → 
              {{ \Carbon\Carbon::parse($order->event_to)->format('d M Y') }}
            </p>
          </div>
          @if($order->event_time)
          <div class="mb-3">
            <label class="text-muted small fw-semibold mb-1">Event Time</label>
            <p class="mb-0">
              <i class="bi bi-clock-fill text-info me-2"></i>{{ \Carbon\Carbon::parse($order->event_time)->format('h:i A') }}
            </p>
          </div>
          @endif
          @if($order->event_location)
          <div class="mb-0">
            <label class="text-muted small fw-semibold mb-1">Location</label>
            <p class="mb-0">
              <i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $order->event_location }}
            </p>
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Summary Information -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 p-4">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-calculator me-2 text-warning"></i>Order Summary
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="p-3 bg-light rounded">
                <label class="text-muted small fw-semibold mb-1 d-block">Subtotal</label>
                <h5 class="mb-0 fw-bold">₹{{ number_format($order->subtotal, 2) }}</h5>
              </div>
            </div>
            @if($order->tax_amount > 0)
            <div class="col-md-3 mb-3">
              <div class="p-3 bg-light rounded">
                <label class="text-muted small fw-semibold mb-1 d-block">Tax Amount</label>
                <h5 class="mb-0 fw-bold text-info">₹{{ number_format($order->tax_amount, 2) }}</h5>
              </div>
            </div>
            @endif
            <div class="col-md-4 mb-3">
              <div class="p-3 bg-light rounded">
                <label class="text-muted small fw-semibold mb-1 d-block">Discount</label>
                <h5 class="mb-0 fw-bold text-danger">₹{{ number_format($order->discount_amount, 2) }}</h5>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="p-3 bg-warning bg-opacity-10 rounded border border-warning">
                <label class="text-muted small fw-semibold mb-1 d-block">Total Amount</label>
                <h5 class="mb-0 fw-bold text-warning">₹{{ number_format($order->total_amount, 2) }}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Items Table -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-box-seam me-2 text-warning"></i>Order Items
      </h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3 text-muted fw-semibold">#</th>
              <th class="px-4 py-3 text-muted fw-semibold">Item Details</th>
              <th class="px-4 py-3 text-muted fw-semibold">Type</th>
              <th class="px-4 py-3 text-muted fw-semibold text-center">Quantity</th>
              <th class="px-4 py-3 text-muted fw-semibold text-end">Unit Price</th>
              <th class="px-4 py-3 text-muted fw-semibold text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $i => $it)
            <tr>
              <td class="px-4 py-3">
                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $i+1 }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="fw-medium">{{ $it->item_name }}</div>
                @if($it->description)
                  <small class="text-muted">{{ $it->description }}</small>
                @endif
              </td>
              <td class="px-4 py-3">
                <span class="badge bg-info bg-opacity-10 text-info">{{ $it->item_type }}</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="fw-semibold">{{ $it->quantity }}</span>
              </td>
              <td class="px-4 py-3 text-end">
                ₹{{ number_format($it->unit_price, 2) }}
              </td>
              <td class="px-4 py-3 text-end">
                <span class="fw-semibold text-dark">₹{{ number_format($it->total_price, 2) }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
          
          <tfoot class="bg-light">

              {{-- Event Duration --}}
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Event Duration:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold">
                      {{ $order->total_days }}
                      Day{{ $order->total_days > 1 ? 's' : '' }}
                  </td>
              </tr>

              {{-- Total Tax --}}
              @if($order->tax_amount > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Total Tax:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold">
                      ₹{{ number_format($order->tax_amount, 2) }}
                  </td>
              </tr>
              @endif

              {{-- Extra Charges --}}
              @if($order->extra_charge_type === 'delivery')
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Delivery Charges:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold">
                      ₹{{ number_format($order->extra_charge_total, 2) }}
                  </td>
              </tr>
              @endif

                @if($order->extra_charge_type === 'staff')
                <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                    Support Staff
                    ({{ $order->staff_count }} Staff × ₹{{ number_format($order->extra_charge_rate, 2) }}/day × {{ $order->total_days }} Day{{ $order->total_days > 1 ? 's' : '' }})
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold">
                    ₹{{ number_format($order->extra_charge_total, 2) }}
                  </td>
                </tr>
                @endif

              {{-- Discount --}}
              @if($order->discount_amount > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Discount:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold text-danger">
                      − ₹{{ number_format($order->discount_amount, 2) }}
                  </td>
              </tr>
              @endif

            {{-- Sub Total --}}
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold">
                      Sub Total:
                  </td>
                  <td class="px-4 py-3 text-end">
                      <span class="fs-5 fw-bold text-warning">
                          ₹{{ number_format($order->total_amount, 2) }}
                      </span>
                  </td>
              </tr>

              {{-- Advance Payment --}}
              @if($order->advance_paid > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Advance Paid:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold text-success">
                      - ₹{{ number_format($order->advance_paid, 2) }}
                  </td>
              </tr>
              @endif

              {{-- Security Deposit --}}
              @if($order->security_deposit > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Security Deposit (Refundable):
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold">
                      ₹{{ number_format($order->security_deposit, 2) }}
                  </td>
              </tr>
              @endif

              {{-- Remaining Rent Payable --}}
              <tr class="bg-warning bg-opacity-10">
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold">
                      Remaining Rent Payable:
                  </td>
                  <td class="px-4 py-3 text-end">
                      <span class="fs-5 fw-bold text-warning">
                          ₹{{ number_format($order->balance_amount, 2) }}
                      </span>
                  </td>
              </tr>

              {{-- Settlement Section --}}
              @if($order->settlement_status === 'settled')
              <tr class="bg-light">
                  <td colspan="6" class="px-4 py-3">
                      <strong class="text-muted">Final Settlement Details</strong>
                  </td>
              </tr>

              @if(($order->damage_charge ?? 0) > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Damage Charges:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold text-danger">
                      + ₹{{ number_format($order->damage_charge, 2) }}
                  </td>
              </tr>
              @endif

              @if(($order->late_fee ?? 0) > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Late Return Fee:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold text-danger">
                      + ₹{{ number_format($order->late_fee, 2) }}
                  </td>
              </tr>
              @endif

              @if($order->security_deposit > 0)
              <tr>
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold text-muted">
                      Security Deposit Adjusted:
                  </td>
                  <td class="px-4 py-3 text-end fw-semibold text-success">
                      - ₹{{ number_format($order->security_deposit, 2) }}
                  </td>
              </tr>
              @endif

              @if(($order->final_payable ?? 0) > 0)
              <tr class="bg-danger bg-opacity-10">
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold">
                      <strong>FINAL AMOUNT PAYABLE BY CLIENT:</strong>
                  </td>
                  <td class="px-4 py-3 text-end">
                      <span class="fs-5 fw-bold text-danger">
                          ₹{{ number_format($order->final_payable, 2) }}
                      </span>
                  </td>
              </tr>
              @elseif(($order->final_payable ?? 0) == 0 && ($order->refund_amount ?? 0) == 0)
              <tr class="bg-success bg-opacity-10">
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold">
                      <strong>SETTLEMENT STATUS:</strong>
                  </td>
                  <td class="px-4 py-3 text-end">
                      <span class="fs-6 fw-bold text-success">
                          ✓ FULLY SETTLED - NO DUES
                      </span>
                  </td>
              </tr>
              @endif

              @if(($order->refund_amount ?? 0) > 0)
              <tr class="bg-success bg-opacity-10">
                  <td colspan="5" class="px-4 py-3 text-end fw-semibold">
                      <strong>REFUND AMOUNT TO CLIENT:</strong>
                  </td>
                  <td class="px-4 py-3 text-end">
                      <span class="fs-5 fw-bold text-success">
                          ₹{{ number_format($order->refund_amount, 2) }}
                      </span>
                  </td>
              </tr>
              @endif
              @endif

          </tfoot>


        </table>
      </div>
    </div>
  </div>

  <!-- Notes Section -->
  @if($order->notes)
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-4">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-sticky me-2 text-warning"></i>Additional Notes
      </h5>
    </div>
    <div class="card-body p-4">
      <p class="mb-0 text-muted">{{ $order->notes }}</p>
    </div>
  </div>
  @endif

  {{-- ============================================================
       📦 ITEM RETURN TRACKING SECTION
  ============================================================ --}}
  @php
    $order->load('items', 'returnItems.orderItem', 'returnItems.recordedBy');

    // Build per-item return summary
    $returnSummary = [];
    foreach ($order->items as $item) {
        $returnedQty = $order->returnItems
            ->where('order_item_id', $item->id)
            ->sum('returned_qty');
        $returnedQty = min($returnedQty, $item->quantity);
        $returnSummary[$item->id] = [
            'item'         => $item,
            'dispatched'   => $item->quantity,
            'returned'     => $returnedQty,
            'pending'      => max(0, $item->quantity - $returnedQty),
        ];
    }

    $isOverdue = $order->isReturnOverdue();
    $overdueDays = $order->returnOverdueDays();
  @endphp

  <div class="card border-0 shadow-sm mt-4" id="return-tracking-section">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-box-arrow-in-left me-2 text-warning"></i>Item Return Tracking
        </h5>

        {{-- Return Status Badge --}}
        @if($order->return_status === 'fully_returned')
          <span class="badge px-3 py-2" style="background:#d1fae5;color:#065f46;font-size:0.78rem;">
            <i class="bi bi-check-circle-fill me-1"></i>Fully Returned
          </span>
        @elseif($order->return_status === 'partial')
          <span class="badge px-3 py-2" style="background:#fef3c7;color:#92400e;font-size:0.78rem;">
            <i class="bi bi-arrow-left-right me-1"></i>Partial Return
          </span>
        @else
          <span class="badge px-3 py-2" style="background:#fee2e2;color:#991b1b;font-size:0.78rem;">
            <i class="bi bi-clock-history me-1"></i>Return Pending
          </span>
        @endif
      </div>

      @if($order->return_status !== 'fully_returned')
        <button class="btn btn-warning btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#recordReturnModal">
          <i class="bi bi-plus-circle me-2"></i>Record Return
        </button>
      @endif
    </div>

    <div class="card-body p-0">

      {{-- Session Alerts (visible after page reload) --}}
      @if(session('success'))
        <div class="mx-4 mt-4">
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-0" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
          </div>
        </div>
      @endif
      @if(session('error'))
        <div class="mx-4 mt-4">
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-0" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
          </div>
        </div>
      @endif
      @if($errors->any())
        <div class="mx-4 mt-4">
          <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-0" role="alert">
            <strong><i class="bi bi-exclamation-circle-fill me-1"></i>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1 ps-3">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
          </div>
        </div>
      @endif

      {{-- Overdue Alert --}}
      @if($isOverdue)
        <div class="mx-4 mt-4">
          <div class="alert mb-0 d-flex align-items-center gap-3 py-3 px-4"
               style="background:#fff1f2;border:1.5px solid #fca5a5;border-radius:10px;">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div>
              <strong class="text-danger">Return Overdue!</strong>
              <span class="text-danger ms-2">
                Event ended {{ $overdueDays }} day{{ $overdueDays > 1 ? 's' : '' }} ago — items have not been returned yet.
              </span>
            </div>
          </div>
        </div>
      @endif

      {{-- Per-Item Return Table --}}
      <div class="table-responsive px-4 pt-4">
        <table class="table align-middle mb-0" style="border-radius:10px;overflow:hidden;">
          <thead style="background:#f8fafc;">
            <tr>
              <th class="py-3 px-3 text-muted fw-semibold" style="font-size:0.82rem;">Item</th>
              <th class="py-3 px-3 text-muted fw-semibold text-center" style="font-size:0.82rem;">Dispatched</th>
              <th class="py-3 px-3 text-muted fw-semibold text-center" style="font-size:0.82rem;">Returned</th>
              <th class="py-3 px-3 text-muted fw-semibold text-center" style="font-size:0.82rem;">Pending</th>
              <th class="py-3 px-3 text-muted fw-semibold text-center" style="font-size:0.82rem;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($returnSummary as $summary)
            <tr style="border-bottom:1px solid #f1f5f9;">
              <td class="py-3 px-3">
                <div class="fw-medium">{{ $summary['item']->item_name }}</div>
                @if($summary['item']->item_type)
                  <small class="text-muted">{{ $summary['item']->item_type }}</small>
                @endif
              </td>
              <td class="py-3 px-3 text-center">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 fw-semibold">
                  {{ $summary['dispatched'] }}
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="badge px-3 py-2 fw-semibold"
                      style="{{ $summary['returned'] > 0 ? 'background:#d1fae5;color:#065f46;' : 'background:#f1f5f9;color:#64748b;' }}">
                  {{ $summary['returned'] }}
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="badge px-3 py-2 fw-semibold"
                      style="{{ $summary['pending'] > 0 ? 'background:#fee2e2;color:#991b1b;' : 'background:#d1fae5;color:#065f46;' }}">
                  {{ $summary['pending'] }}
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                @if($summary['pending'] === 0)
                  <span class="badge px-3 py-2" style="background:#d1fae5;color:#065f46;font-size:0.75rem;">
                    <i class="bi bi-check-circle-fill me-1"></i>Returned
                  </span>
                @elseif($summary['returned'] > 0)
                  <span class="badge px-3 py-2" style="background:#fef3c7;color:#92400e;font-size:0.75rem;">
                    <i class="bi bi-arrow-left-right me-1"></i>Partial
                  </span>
                @else
                  <span class="badge px-3 py-2" style="background:#fee2e2;color:#991b1b;font-size:0.75rem;">
                    <i class="bi bi-clock-history me-1"></i>Pending
                  </span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Return History Log --}}
      @if($order->returnItems->count() > 0)
        <div class="px-4 py-4">
          <h6 class="fw-semibold text-muted mb-3" style="font-size:0.85rem;letter-spacing:0.5px;">
            <i class="bi bi-clock-history me-2"></i>Return History
          </h6>
          <div class="d-flex flex-column gap-2">
            @foreach($order->returnItems->sortByDesc('created_at') as $ret)
              <div class="d-flex align-items-start justify-content-between p-3 rounded-3"
                   style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="d-flex align-items-start gap-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:36px;height:36px;
                              {{ $ret->condition === 'good' ? 'background:#d1fae5;color:#059669;' : ($ret->condition === 'damaged' ? 'background:#fef3c7;color:#d97706;' : 'background:#fee2e2;color:#dc2626;') }}">
                    <i class="bi bi-{{ $ret->condition === 'good' ? 'check-circle' : ($ret->condition === 'damaged' ? 'exclamation-triangle' : 'x-circle') }} fs-6"></i>
                  </div>
                  <div>
                    <div class="fw-semibold" style="font-size:0.9rem;">
                      {{ $ret->orderItem->item_name ?? 'Item' }}
                      <span class="ms-2 badge px-2 py-1 fw-medium"
                            style="{{ $ret->condition === 'good' ? 'background:#d1fae5;color:#065f46;' : ($ret->condition === 'damaged' ? 'background:#fef3c7;color:#92400e;' : 'background:#fee2e2;color:#991b1b;') }}font-size:0.72rem;">
                        {{ ucfirst($ret->condition) }}
                      </span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:0.82rem;">
                      <i class="bi bi-arrow-return-left me-1"></i>
                      <strong>{{ $ret->returned_qty }}</strong> unit{{ $ret->returned_qty > 1 ? 's' : '' }} returned
                      on <strong>{{ $ret->return_date->format('d M Y') }}</strong>
                      @if($ret->recordedBy)
                        &bull; Recorded by <strong>{{ $ret->recordedBy->name }}</strong>
                      @endif
                    </div>
                    @if($ret->notes)
                      <div class="mt-1 text-muted" style="font-size:0.8rem;">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $ret->notes }}
                      </div>
                    @endif
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted" style="font-size:0.75rem;">{{ $ret->created_at->diffForHumans() }}</span>
                  @if($order->return_status !== 'fully_returned')
                    <form action="{{ route('orders.return-item.destroy', [$order, $ret]) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this return entry?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Delete">
                        <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="text-center py-4 text-muted" style="font-size:0.88rem;">
          <i class="bi bi-inbox fs-4 d-block mb-2 opacity-50"></i>
          No return entries recorded yet.
        </div>
      @endif

    </div>{{-- end card-body --}}
  </div>

  {{-- Record Return Modal --}}
  <div class="modal fade" id="recordReturnModal" tabindex="-1" aria-labelledby="recordReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('orders.record-return', $order) }}" method="POST">
        @csrf
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0 p-4" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
            <h5 class="modal-title fw-semibold" id="recordReturnModalLabel">
              <i class="bi bi-box-arrow-in-left me-2 text-warning"></i>Record Return
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">

            {{-- Item Select --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Select Item <span class="text-danger">*</span></label>
              <select name="order_item_id" id="returnItemSelect" class="form-select" required>
                <option value="">-- Choose an Item --</option>
                @foreach($returnSummary as $summary)
                  @if($summary['pending'] > 0)
                    <option value="{{ $summary['item']->id }}"
                            data-pending="{{ $summary['pending'] }}"
                            data-name="{{ $summary['item']->item_name }}">
                      {{ $summary['item']->item_name }}
                      ({{ $summary['pending'] }} pending)
                    </option>
                  @endif
                @endforeach
              </select>
            </div>

            {{-- Returned Qty --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Returned Quantity <span class="text-danger">*</span></label>
              <input type="number" name="returned_qty" id="returnQtyInput"
                     class="form-control" min="1" value="1" required>
              <div class="form-text" id="returnQtyHint">Select an item to see the maximum returnable quantity.</div>
            </div>

            {{-- Return Date --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Return Date <span class="text-danger">*</span></label>
              <input type="date" name="return_date" class="form-control"
                     value="{{ date('Y-m-d') }}" required>
            </div>

            {{-- Condition --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Item Condition <span class="text-danger">*</span></label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="condition" id="cond_good" value="good" checked>
                  <label class="form-check-label fw-medium" for="cond_good">
                    <span style="color:#059669;">✅ Good</span>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="condition" id="cond_damaged" value="damaged">
                  <label class="form-check-label fw-medium" for="cond_damaged">
                    <span style="color:#d97706;">⚠️ Damaged</span>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="condition" id="cond_missing" value="missing">
                  <label class="form-check-label fw-medium" for="cond_missing">
                    <span style="color:#dc2626;">❌ Missing</span>
                  </label>
                </div>
              </div>
            </div>

            {{-- Notes --}}
            <div class="mb-0">
              <label class="form-label fw-semibold">Notes (Optional)</label>
              <textarea name="notes" class="form-control" rows="2"
                        placeholder="Add any damage details or special notes here..."></textarea>
            </div>

          </div>
          <div class="modal-footer border-0 p-4 pt-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning fw-semibold">
              <i class="bi bi-check-circle me-2"></i>Save Return
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  @if($order->status == 'completed' || $order->settlement_status == 'pending')
  <div class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
              <i class="bi bi-cash-coin text-success me-2"></i>
              Final Settlement
          </h5>
          @if($order->settlement_status === 'settled')
            <a href="{{ route('orders.settlementPdf', $order) }}" target="_blank"
               class="btn btn-sm fw-semibold"
               style="background:linear-gradient(135deg,#e84393,#c026a0);color:#fff;border:none;">
              <i class="bi bi-file-earmark-pdf me-2"></i>Download Invoice PDF
            </a>
          @endif
      </div>

      <div class="card-body p-4">

          <div class="row mb-3">
              <div class="col-md-6">
                  <label class="form-label fw-semibold">Total Rent</label>
                  <input class="form-control" value="₹{{ number_format($order->total_amount,2) }}" disabled>
              </div>

              <div class="col-md-6">
                  <label class="form-label fw-semibold">Advance Paid</label>
                  <input class="form-control" value="₹{{ number_format($order->advance_paid,2) }}" disabled>
              </div>
          </div>

          <div class="row mb-3">
              <div class="col-md-6">
                  <label class="form-label fw-semibold">Security Deposit</label>
                  <input class="form-control" value="₹{{ number_format($order->security_deposit,2) }}" disabled>
              </div>

              <div class="col-md-6">
                  <label class="form-label fw-semibold">Remaining Rent payable</label>
                  <input class="form-control"
                        value="₹{{ number_format($order->balance_amount,2) }}"
                        disabled>
              </div>
          </div>

          <form action="{{ route('orders.complete', $order) }}" method="POST" id="settle-form">
              @csrf

              <hr class="my-4">

              <h5 class="fw-bold mb-3">Deductions (if applicable)</h5>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-semibold">Damage Charges</label>
                      <div class="input-group">
                          <span class="input-group-text">₹</span>
                          <input type="number" step="0.01" name="damage_charge"
                                 class="form-control settlement-charge-input" value="{{ $order->damage_charge ?? 0 }}"
                                 min="0">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-semibold">Late Fee</label>
                      <div class="input-group">
                          <span class="input-group-text">₹</span>
                          <input type="number" step="0.01" name="late_fee"
                                 class="form-control settlement-charge-input" value="{{ $order->late_fee ?? 0 }}"
                                 min="0">
                      </div>
                  </div>
              </div>

              <hr class="my-4">

              <h5 class="fw-bold mb-3">Additional Charges</h5>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-semibold">
                          <i class="bi bi-car-front-fill text-primary me-1"></i>Travelling Charges
                      </label>
                      <div class="input-group">
                          <span class="input-group-text">₹</span>
                          <input type="number" step="0.01" name="settlement_travelling_charges"
                                 id="settlementTravellingCharges"
                                 class="form-control settlement-charge-input"
                                 value="{{ $order->settlement_travelling_charges ?? 0 }}"
                                 min="0" placeholder="0.00">
                      </div>
                      <div class="form-text text-muted">Transport/logistics charges for the event</div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-semibold">
                          <i class="bi bi-cup-hot-fill text-warning me-1"></i>Food Charges
                      </label>
                      <div class="input-group">
                          <span class="input-group-text">₹</span>
                          <input type="number" step="0.01" name="settlement_food_charges"
                                 id="settlementFoodCharges"
                                 class="form-control settlement-charge-input"
                                 value="{{ $order->settlement_food_charges ?? 0 }}"
                                 min="0" placeholder="0.00">
                      </div>
                      <div class="form-text text-muted">Food/catering charges during the event</div>
                  </div>
              </div>

              {{-- Live Settlement Preview --}}
              <div class="rounded-3 p-4 mt-3 mb-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;">
                  <h6 class="fw-bold mb-3" style="color:#15803d;">
                      <i class="bi bi-calculator me-2"></i>Settlement Preview
                  </h6>
                  <div class="row g-3">
                      <div class="col-6 col-md-4">
                          <div class="text-muted" style="font-size:0.8rem;">Remaining Rent</div>
                          <div class="fw-semibold">₹{{ number_format($order->balance_amount, 2) }}</div>
                      </div>
                      <div class="col-6 col-md-4">
                          <div class="text-muted" style="font-size:0.8rem;">Additional Charges</div>
                          <div class="fw-semibold text-warning" id="previewAdditional">₹0.00</div>
                      </div>
                      <div class="col-6 col-md-4">
                          <div class="text-muted" style="font-size:0.8rem;">Security Deposit</div>
                          <div class="fw-semibold text-success">- ₹{{ number_format($order->security_deposit, 2) }}</div>
                      </div>
                      <div class="col-12">
                          <hr class="my-1">
                          <div class="d-flex justify-content-between align-items-center">
                              <span class="fw-bold" style="font-size:1rem;">Estimated Final Payable</span>
                              <span class="fw-bold fs-5" id="previewFinalPayable" style="color:#15803d;">₹0.00</span>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="alert" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
                  <i class="bi bi-info-circle-fill me-2" style="color:#3b82f6;"></i>
                  <strong>Note:</strong> Damage/Late fee will be settled from security deposit first. Travelling & Food charges are added to the final payable amount.
              </div>

              <button class="btn btn-success mt-3 px-4 fw-semibold">
                  <i class="bi bi-check-circle me-2"></i>
                  Proceed Settlement
              </button>

          </form>

      </div>
  </div>
  @endif

@if($order->agreement_required || optional($order->agreement)->aadhaar_status !== 'uploaded')
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 p-4">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-credit-card-2-front me-2 text-warning"></i>
            Aadhaar Upload (Admin Only)
        </h5>
    </div>

    <div class="card-body p-4">

        @if(optional($order->agreement)->aadhaar_status === 'uploaded')
            <div class="alert alert-success">
                Aadhaar uploaded successfully.
            </div>
        @else

        <form action="{{ route('orders.uploadAadhaar', $order) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            {{-- Aadhaar Type --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Aadhaar Type</label>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="aadhaar_type"
                           id="aadhaar_fb"
                           value="front_back"
                           checked>
                    <label class="form-check-label" for="aadhaar_fb">
                        Front & Back
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="aadhaar_type"
                           id="aadhaar_full"
                           value="full">
                    <label class="form-check-label" for="aadhaar_full">
                        Full Aadhaar
                    </label>
                </div>
            </div>

            {{-- Front & Back --}}
            <div id="aadhaar-front-back">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Aadhaar Front</label>
                        <input type="file"
                               name="aadhaar_front"
                               class="form-control aadhaar-input"
                               accept="image/*">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Aadhaar Back</label>
                        <input type="file"
                               name="aadhaar_back"
                               class="form-control aadhaar-input"
                               accept="image/*">
                    </div>
                </div>
            </div>

            {{-- Full Aadhaar --}}
            <div id="aadhaar-full" class="d-none">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Aadhaar</label>
                    <input type="file"
                           name="aadhaar_full"
                           class="form-control aadhaar-input"
                           accept="image/*,application/pdf">
                </div>
            </div>

            <button class="btn btn-primary mt-3">
                <i class="bi bi-upload me-2"></i>Upload Aadhaar
            </button>
        </form>

        @endif
    </div>
</div>
@endif


</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('orders.sendEmail', $order) }}" method="POST" id="sendEmailForm">
      @csrf
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success bg-opacity-10 border-0 p-4">
          <h5 class="modal-title fw-semibold" id="emailModalLabel">
            <i class="bi bi-envelope me-2 text-success"></i>Send Email
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">To Email</label>
            <input name="to_email" class="form-control" value="{{ $order->client_email }}" required>
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold">Message</label>
            <textarea name="message" class="form-control" rows="4" required>Hello {{ $order->client_name }}, Please find attached the order {{ $order->code }}.</textarea>
          </div>
        </div>
        <div class="modal-footer border-0 p-4">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="sendEmailBtn">
              <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
              <span class="btn-text">
                  <i class="bi bi-send me-2"></i>Send Email
              </span>
          </button>

        </div>
      </div>
    </form>
  </div>
</div>

<!-- WhatsApp Modal -->
<div class="modal fade" id="waModal" tabindex="-1" aria-labelledby="waModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('orders.sendWhatsapp', $order) }}" method="POST" target="_blank">
      @csrf
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-info bg-opacity-10 border-0 p-4">
          <h5 class="modal-title fw-semibold" id="waModalLabel">
            <i class="bi bi-whatsapp me-2 text-info"></i>Send WhatsApp
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">To Phone</label>
            <input name="to_phone" class="form-control" value="{{ $order->client_phone }}" required>
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold">Message</label>
            <textarea name="message" class="form-control" rows="4" required>Hello {{ $order->client_name }}, Here is the order {{ $order->code }}. Please download from the link and reply to confirm.</textarea>
          </div>
        </div>
        <div class="modal-footer border-0 p-4">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-info">
            <i class="bi bi-whatsapp me-2"></i>Open WhatsApp
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crop Aadhaar Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="cropperImage" style="max-width:100%;">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="cropConfirmBtn">Crop & Use</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  <div id="copyToast" class="toast hide" role="alert">
    <div class="toast-header bg-success text-white">
      <i class="bi bi-check-circle me-2"></i>
      <strong class="me-auto">Success</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
      Agreement link copied to clipboard!
    </div>
  </div>
</div>


<style>
  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
  }
  
  .badge {
    font-weight: 500;
    letter-spacing: 0.3px;
  }
  
  .card {
    transition: all 0.3s ease;
  }
  
  .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
  }

  .modal-content {
    border-radius: 0.5rem;
  }
</style>

<script src="{{ asset('js/aadhaar-crop.js') }}"></script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('sendEmailForm');
    const btn  = document.getElementById('sendEmailBtn');
    const spinner = btn.querySelector('.spinner-border');
    const btnText = btn.querySelector('.btn-text');

    form.addEventListener('submit', function () {
        // Disable button
        btn.disabled = true;

        // Show spinner
        spinner.classList.remove('d-none');

        // Change text
        btnText.innerHTML = 'Sending...';
    });

});

document.addEventListener('DOMContentLoaded', function () {

    const frontBackRadio = document.getElementById('aadhaar_fb');
    const fullRadio = document.getElementById('aadhaar_full');

    const frontBackDiv = document.getElementById('aadhaar-front-back');
    const fullDiv = document.getElementById('aadhaar-full');

    function toggleAadhaarInputs() {
        if (frontBackRadio.checked) {
            frontBackDiv.classList.remove('d-none');
            fullDiv.classList.add('d-none');
        }

        if (fullRadio.checked) {
            fullDiv.classList.remove('d-none');
            frontBackDiv.classList.add('d-none');
        }
    }

    frontBackRadio.addEventListener('change', toggleAadhaarInputs);
    fullRadio.addEventListener('change', toggleAadhaarInputs);

    // Initial load
    toggleAadhaarInputs();
});
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const copyBtn = document.getElementById('copyLinkBtn');
  
  if (copyBtn) {
    copyBtn.addEventListener('click', function() {
      const link = this.getAttribute('data-link');
      
      // Fallback function for older browsers
      function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "-999999px";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
          const successful = document.execCommand('copy');
          document.body.removeChild(textArea);
          return successful;
        } catch (err) {
          document.body.removeChild(textArea);
          return false;
        }
      }
      
      // Try modern clipboard API first
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(link).then(function() {
          showToast();
        }).catch(function(err) {
          // Fallback to old method
          if (fallbackCopyTextToClipboard(link)) {
            showToast();
          } else {
            alert('Failed to copy link. Please copy manually: ' + link);
          }
        });
      } else {
        // Use fallback for non-secure contexts
        if (fallbackCopyTextToClipboard(link)) {
          showToast();
        } else {
          alert('Failed to copy link. Please copy manually: ' + link);
        }
      }
    });
  }
  
  function showToast() {
    const toastEl = document.getElementById('copyToast');
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
  }
});
</script>
@endpush
@push('scripts')
<!-- <script>
@if(session('whatsapp_link'))
  window.open('{{ session('whatsapp_link') }}', '_blank');
@endif
</script> -->
@endpush
@push('scripts')
<script>
// Return Modal: Update max quantity based on selected item
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect  = document.getElementById('returnItemSelect');
    const qtyInput    = document.getElementById('returnQtyInput');
    const qtyHint     = document.getElementById('returnQtyHint');

    if (!itemSelect) return;

    itemSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) {
            qtyInput.max  = '';
            qtyInput.value = 1;
            qtyHint.textContent = 'Select an item to see the maximum returnable quantity.';
            return;
        }

        const pending  = parseInt(selected.dataset.pending) || 1;
        const itemName = selected.dataset.name || 'Item';

        qtyInput.max   = pending;
        qtyInput.value = pending; // default to all pending
        qtyHint.textContent = `Max: ${pending} unit(s) can be returned (${itemName})`;
    });
});
</script>
@endpush
@push('scripts')
<script>
// Auto-open return modal if there are validation errors (so user sees them)
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('recordReturnModal');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
    // Also scroll to return section
    const section = document.getElementById('return-tracking-section');
    if (section) {
        setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300);
    }
});
@endif

// Scroll to return section if URL has #return-tracking-section
if (window.location.hash === '#return-tracking-section') {
    document.addEventListener('DOMContentLoaded', function () {
        const section = document.getElementById('return-tracking-section');
        if (section) {
            setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 200);
        }
    });
}
</script>
@endpush
@push('scripts')
<script>
// Live Settlement Preview Calculator
document.addEventListener('DOMContentLoaded', function () {
    const chargeInputs = document.querySelectorAll('.settlement-charge-input');
    const previewAdditional = document.getElementById('previewAdditional');
    const previewFinalPayable = document.getElementById('previewFinalPayable');

    if (!previewFinalPayable) return;

    const balance     = {{ floatval($order->balance_amount) }};
    const deposit     = {{ floatval($order->security_deposit) }};

    function updatePreview() {
        const damageEl   = document.querySelector('input[name="damage_charge"]');
        const lateEl     = document.querySelector('input[name="late_fee"]');
        const travelEl   = document.querySelector('input[name="settlement_travelling_charges"]');
        const foodEl     = document.querySelector('input[name="settlement_food_charges"]');

        const damage     = parseFloat(damageEl?.value || 0);
        const late       = parseFloat(lateEl?.value   || 0);
        const travelling = parseFloat(travelEl?.value || 0);
        const food       = parseFloat(foodEl?.value   || 0);

        const extraCharges      = travelling + food;
        const depositRemaining  = deposit - (damage + late);
        let finalPayable;

        if (depositRemaining >= 0) {
            finalPayable = balance + extraCharges - depositRemaining;
            finalPayable = Math.max(0, finalPayable);
        } else {
            finalPayable = balance + extraCharges + Math.abs(depositRemaining);
        }

        // Update preview
        if (previewAdditional) {
            previewAdditional.textContent = '₹' + extraCharges.toFixed(2);
            previewAdditional.style.color = extraCharges > 0 ? '#d97706' : '#64748b';
        }

        if (previewFinalPayable) {
            previewFinalPayable.textContent = '₹' + finalPayable.toFixed(2);
            previewFinalPayable.style.color = finalPayable > 0 ? '#dc2626' : '#15803d';
        }
    }

    chargeInputs.forEach(function (input) {
        input.addEventListener('input', updatePreview);
    });

    // Run once on page load
    updatePreview();
});
</script>
@endpush
@endsection