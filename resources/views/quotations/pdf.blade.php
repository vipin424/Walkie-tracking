<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->code }} - Quotation</title>
    <style>
      body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222;}
      .header{margin-bottom:20px;}
      .company {text-align:left; margin-bottom:20px;}
      .company img{height:60px;}
      
      /* Bill To and Client Info Container */
      .billing-section {
          display:table;
          width:100%;
      }
      
      .bill-to {
          display:table-cell;
          width:50%; 
          font-size:11px;
          text-align:left;
        }
      
      .client-info {
          display:table-cell;
          width:50%;
          text-align:right;
          vertical-align:top;
      }
      
      .client-info h2 {
          margin-top:0;
          margin-bottom:10px;
          font-size:24px;
      }
      
      .client-info div {
          margin-bottom:5px;
      }
      
      table {width:100%; border-collapse:collapse; margin-top:20px;}
      th, td {padding:8px; border:1px solid #e1e1e1;}
      th {background:#ff9800; text-align:left;}
      .right {text-align:right;}
      .total-row td {font-weight:bold;}
      .notes {margin-top:20px; font-size:11px;}
      .footer {position:fixed; bottom:0px; font-size:10px; width:100%; text-align:center; color:#888;}
      .payment-details {
            margin-top: 30px;
            border: 2px solid #ff9800;
            padding: 15px;
            background: #fff8e1;
        }
        .payment-details h3 {
            margin: 0 0 10px 0;
            color: #ff9800;
        }
        .payment-grid {
            display: table;
            width: 100%;
        }
        .bank-details {
            display: table-cell;
            width: 60%;
            padding-right: 20px;
            vertical-align: top;
        }
        .qr-section {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: top;
        }
        .qr-section img {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
  <div class="header">
    <div class="company">
     <img src="{{ public_path('image/logo.png') }}" style="width:150px; height:150px; object-fit:contain;" alt="Company Logo">

      <div><strong>Crewrent Enterprises</strong></div>  
      <div style="font-size:11px; line-height:1.5; color:#444; margin-bottom:4px;">
          The Avenue, IA Project Rd, Chimatpada,<br>
          Marol, Andheri East, Mumbai,<br>
          Maharashtra – 400059
      </div>
      <div>Phone: +91-9324465314 | Email: info@crewrent.in</div>
    </div>
  </div>

  <!-- Bill To and Client Info Side by Side -->
  <div class="billing-section">
    <!-- BILL TO (Left) -->
    @if(!empty($quotation->bill_to))
    <div class="bill-to">
        <h3>Bill To</h3>
        {!! $quotation->bill_to !!}
    </div>
    @else
    <div class="bill-to"></div>
    @endif
    
    <!-- Client Info (Right) -->
    <div class="client-info">
      <h3>Quotation</h3>
      <div><strong>{{ $quotation->code }}</strong></div>
      <div>Date: {{ $quotation->created_at->format('d M Y') }}</div>
      <div>To: <strong>{{ $quotation->client_name }}</strong></div>
      <div>{{ $quotation->client_email }}</div>
      <div>{{ $quotation->client_phone }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Items</th>
        <th>Type</th>
        <th class="right">Qty</th>
        <th class="right">Rate / Item / Day</th>
        <th class="right">Tax %</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quotation->items as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>
          <strong>{{ $item->item_name }}</strong><br>
          <div style="font-size:11px; color:#555;">{{ $item->description }}</div>
        </td>
        <td>{{ $item->item_type }}</td>
        <td class="right">{{ $item->quantity }}</td>
        <td class="right">₹{{ number_format($item->unit_price,2) }}</td>
        <td class="right">{{ $item->tax_percent ?? 0 }}</td>
        <td class="right">₹{{ number_format($item->total_price,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  <tfoot>

      {{-- Event Duration --}}
      <tr class="total-row">
          <td colspan="6" class="right">
              Event Duration
          </td>
          <td class="right">
              {{ $quotation->total_days }}
              Day{{ $quotation->total_days > 1 ? 's' : '' }}
          </td>
      </tr>

      {{-- Subtotal --}}
      <tr class="total-row">
          <td colspan="6" class="right">Subtotal</td>
          <td class="right">₹{{ number_format($quotation->subtotal, 2) }}</td>
      </tr>

      {{-- Tax (only if > 0) --}}
      @if($quotation->tax_amount > 0)
      <tr class="total-row">
          <td colspan="6" class="right">Tax</td>
          <td class="right">₹{{ number_format($quotation->tax_amount, 2) }}</td>
      </tr>
      @endif

      {{-- Extra Charges --}}
      @if($quotation->extra_charge_type === 'delivery')
      <tr class="total-row">
          <td colspan="6" class="right">
              Delivery Charges
          </td>
          <td class="right">
              ₹{{ number_format($quotation->extra_charge_total, 2) }}
          </td>
      </tr>
      @endif

      @if(($quotation->travelling_charge ?? 0) > 0)
      <tr class="total-row">
          <td colspan="6" class="right">Travelling Charges</td>
          <td class="right">₹{{ number_format($quotation->travelling_charge, 2) }}</td>
      </tr>
      @endif

      @if($quotation->extra_charge_type === 'staff')
      <tr class="total-row">
          <td colspan="6" class="right">
              Support Staff
              ({{ $quotation->staff_count }} Staff
              × ₹{{ number_format($quotation->extra_charge_rate, 2) }}/day
              × {{ $quotation->total_days }} Day{{ $quotation->total_days > 1 ? 's' : '' }})
          </td>
          <td class="right">
              ₹{{ number_format($quotation->extra_charge_total, 2) }}
          </td>
      </tr>
      @endif

      {{-- Discount (only if > 0) --}}
      @if($quotation->discount_amount > 0)
      <tr class="total-row">
          <td colspan="6" class="right">Discount</td>
          <td class="right" style="color: #dc3545;">
              - ₹{{ number_format($quotation->discount_amount, 2) }}
          </td>
      </tr>
      @endif

      {{-- Grand Total --}}
      <tr class="total-row">
          <td colspan="6" class="right"><strong>Grand Total</strong></td>
          <td class="right"><strong>₹{{ number_format($quotation->total_amount, 2) }}</strong></td>
      </tr>

  </tfoot>

  </table>

  <div class="notes">
    <strong>Notes & Terms:</strong>
    <div>
    {!! $quotation->notes !!}
    </div>
  </div>

  {{-- ================= PAYMENT DETAILS (Quotation Payment Intent) ================= --}}
  @if($quotation->total_amount > 0)
  @php
      $paymentAmount = $quotation->total_amount;
      $upiId = config('payment.upi.id');
      $upiName = config('payment.upi.name');
      $upiUrl = "upi://pay?pa={$upiId}&pn=" . urlencode($upiName) . "&am={$paymentAmount}&cu=INR&tn=" . urlencode("Payment for {$quotation->code}");
      $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($upiUrl));
  @endphp

  <div class="payment-details">
      <h3>Payment Details</h3>
      <div class="payment-grid">
          <div class="bank-details">
              <strong>Bank Transfer Details:</strong><br>
              <table style="margin-top: 10px; border: none;">
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>Bank Name:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.name') }}</td>
                  </tr>
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>Account Name:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.account_name') }}</td>
                  </tr>
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>Account Number:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.account_number') }}</td>
                  </tr>
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>IFSC Code:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.ifsc_code') }}</td>
                  </tr>
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>Branch:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.branch') }}</td>
                  </tr>
                  <tr style="border: none;">
                      <td style="border: none; padding: 3px 10px 3px 0;"><strong>PAN Number:</strong></td>
                      <td style="border: none; padding: 3px 0;">{{ config('payment.bank.pan_number') }}</td>
                  </tr>
              </table>
          </div>
          <div class="qr-section">
              <strong>Scan to Pay via UPI</strong><br>
              <img src="data:image/png;base64,{{ $qrCode }}" alt="UPI QR Code"><br>
              <strong style="font-size: 14px; color: #f44336;">₹{{ number_format($paymentAmount, 2) }}</strong><br>
              <span style="font-size: 10px;">UPI ID: {{ $upiId }}</span>
          </div>
      </div>
  </div>
  @endif

  <div class="footer">
    This quotation is computer generated by Crewrent Enterprises.
  </div>
</body>
</html>