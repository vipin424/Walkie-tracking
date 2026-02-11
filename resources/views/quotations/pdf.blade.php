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

      @if($quotation->extra_charge_type === 'staff')
      <tr class="total-row">
          <td colspan="6" class="right">
              Support Staff
              (₹{{ number_format($quotation->extra_charge_rate, 2) }}
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

  <div class="footer">
    This quotation is computer generated by Crewrent Enterprises.
  </div>
</body>
</html>