<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $order->order_code }} - {{ $order->settlement_status === 'settled' ? 'Invoice' : 'Order' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #222;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .company img{
            height: 100px !important;
            margin-bottom: 5px;
        }
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
        .client {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #e1e1e1;
        }
        th {
            background: #ff9800;
            color: #000;
        }
        .right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
        }
        .section-title {
            background: #ff9800;
            color: #000;
            font-weight: bold;
            font-size: 13px;
        }
        .subsection-title {
            background: #fff3e0;
            font-weight: bold;
        }
        .calculation-row td {
            font-size: 11px;
            color: #555;
            font-weight: normal;
        }
        .highlight-row {
            background: #fff8e1;
        }
        .final-amount-row {
            background: #ffe0b2;
            font-size: 14px;
        }
        .notes {
            margin-top: 20px;
            font-size: 11px;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            font-size: 10px;
            width: 100%;
            text-align: center;
            color: #888;
        }
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

{{-- ================= HEADER ================= --}}
<div class="header">
    <div class="company">
        <img src="{{ public_path('image/logo.png') }}" alt="Logo">
        <div><strong>Crewrent Enterprises</strong></div>
        <div style="font-size:11px; line-height:1.5;">
            The Avenue, IA Project Rd, Chimatpada,<br>
            Marol, Andheri East, Mumbai – 400059
        </div>
        <div>Phone: +91-9324465314 | Email: info@crewrent.in</div>
    </div>

      <!-- Bill To and Client Info Side by Side -->
  <div class="billing-section">
    <!-- BILL TO (Left) -->
    @if(!empty($order->bill_to))
    <div class="bill-to">
        <h3>Bill To</h3>
        {!! $order->bill_to !!}
    </div>
    @else
    <div class="bill-to"></div>
    @endif

    <div class="client">
        <h3>{{ $order->settlement_status === 'settled' ? 'Invoice' : 'Order Confirmed' }}</h3>
        <div><strong>{{ $order->order_code }}</strong></div>
        <div>Date: {{ optional($order->created_at)->format('d M Y') }}</div>
        <div>To: <strong>{{ $order->client_name }}</strong></div>
        <div>{{ $order->client_email }}</div>
        <div>{{ $order->client_phone }}</div>
    </div>
</div>

{{-- ================= ITEMS TABLE ================= --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Items</th>
            <th>Type</th>
            <th class="right">Qty</th>
            <th class="right">Rate/Item/Day</th>
            <th class="right">Days</th>
            <th class="right">Tax %</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>
                <strong>{{ $item->item_name }}</strong><br>
                <span style="font-size:11px; color:#555;">
                    {{ $item->description }}
                </span>
            </td>
            <td>{{ $item->item_type }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₹{{ number_format($item->unit_price,2) }}</td>
            <td class="right">{{ $order->total_days }}</td>
            <td class="right">{{ $item->tax_percent ?? 0 }}%</td>
            <td class="right">₹{{ number_format($item->total_price,2) }}</td>
        </tr>
        @endforeach
    </tbody>

    {{-- ================= PRICE BREAKUP ================= --}}
    <tfoot>
        <tr class="section-title">
            <td colspan="8">Price Breakup</td>
        </tr>

        {{-- Event Duration --}}
        <tr class="highlight-row">
            <td colspan="7" class="right"><strong>Event Duration</strong></td>
            <td class="right"><strong>{{ $order->total_days }} Day{{ $order->total_days > 1 ? 's' : '' }}</strong></td>
        </tr>

        {{-- Subtotal --}}
        <tr class="total-row">
            <td colspan="7" class="right">Item Subtotal (Before Tax)</td>
            <td class="right">₹{{ number_format($order->subtotal - $order->tax_amount, 2) }}</td>
        </tr>

        {{-- Tax --}}
        @if($order->tax_amount > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Tax Amount</td>
            <td class="right">₹{{ number_format($order->tax_amount,2) }}</td>
        </tr>
        <tr class="total-row highlight-row">
            <td colspan="7" class="right"><strong>Item Total (With Tax)</strong></td>
            <td class="right"><strong>₹{{ number_format($order->subtotal,2) }}</strong></td>
        </tr>
        @endif

        {{-- Additional Charges Section --}}
        @if($order->extra_charge_type === 'delivery' || $order->extra_charge_type === 'staff')
        <tr class="subsection-title">
            <td colspan="8">Additional Charges</td>
        </tr>
        @endif

        @if($order->extra_charge_type === 'delivery')
        <tr class="total-row">
            <td colspan="7" class="right">Delivery & Setup Charges</td>
            <td class="right">₹{{ number_format($order->extra_charge_total,2) }}</td>
        </tr>
        @endif

        @if($order->extra_charge_type === 'staff')
        <tr class="calculation-row">
            <td colspan="7" class="right">Support Staff: ₹{{ number_format($order->extra_charge_rate,2) }}/day × {{ $order->total_days }} days</td>
            <td class="right">₹{{ number_format($order->extra_charge_total,2) }}</td>
        </tr>
        @endif

        {{-- Discount --}}
        @if($order->discount_amount > 0)
        <tr class="subsection-title">
            <td colspan="8">Discount Applied</td>
        </tr>
        <tr class="total-row">
            <td colspan="7" class="right">Discount</td>
            <td class="right" style="color: #4caf50;">- ₹{{ number_format($order->discount_amount,2) }}</td>
        </tr>
        @endif

        {{-- Grand Total --}}
        <tr class="final-amount-row total-row">
            <td colspan="7" class="right"><strong>GRAND TOTAL</strong></td>
            <td class="right"><strong>₹{{ number_format($order->total_amount,2) }}</strong></td>
        </tr>

        {{-- ================= PAYMENT SUMMARY ================= --}}
        <tr class="section-title">
            <td colspan="8">Payment Summary</td>
        </tr>
     @if(($order->advance_paid ?? 0) > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Advance Paid</td>
            <td class="right" style="color: #4caf50;">- ₹{{ number_format($order->advance_paid ?? 0,2) }}</td>
        </tr>
    @endif
       @if(($order->security_deposit ?? 0) > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Security Deposit (Refundable)</td>
            <td class="right">₹{{ number_format($order->security_deposit ?? 0,2) }}</td>
        </tr>
    @endif

        <tr class="highlight-row total-row">
            <td colspan="7" class="right"><strong>Remaining Rent Payable</strong></td>
            <td class="right"><strong>₹{{ number_format($order->balance_amount ?? 0,2) }}</strong></td>
        </tr>

        {{-- ================= SETTLEMENT ================= --}}
        @if($order->settlement_status === 'settled')

        <tr class="section-title">
            <td colspan="8">Final Settlement Details</td>
        </tr>

        @if(($order->damage_charge ?? 0) > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Damage Charges</td>
            <td class="right" style="color: #f44336;">₹{{ number_format($order->damage_charge,2) }}</td>
        </tr>
        @endif

        @if(($order->late_fee ?? 0) > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Late Return Fee</td>
            <td class="right" style="color: #f44336;">₹{{ number_format($order->late_fee,2) }}</td>
        </tr>
        @endif

        @if((($order->damage_charge ?? 0) + ($order->late_fee ?? 0)) > 0)
        <tr class="calculation-row">
            <td colspan="7" class="right">Total Deductions: Damage (₹{{ number_format($order->damage_charge ?? 0,2) }}) + Late Fee (₹{{ number_format($order->late_fee ?? 0,2) }})</td>
            <td class="right">₹{{ number_format(($order->damage_charge ?? 0) + ($order->late_fee ?? 0),2) }}</td>
        </tr>
        @endif
     
        @if(($order->security_deposit ?? 0) > 0)
        <tr class="total-row">
            <td colspan="7" class="right">Security Deposit Adjusted</td>
            <td class="right" style="color: #4caf50;">- ₹{{ number_format($order->security_deposit ?? 0,2) }}</td>
        </tr>
        @endif

        @if((($order->damage_charge ?? 0) + ($order->late_fee ?? 0) + ($order->security_deposit ?? 0)) > 0)
        <tr class="calculation-row">
            <td colspan="7" class="right">Calculation: Remaining Rent (₹{{ number_format($order->balance_amount ?? 0,2) }}) + Deductions (₹{{ number_format(($order->damage_charge ?? 0) + ($order->late_fee ?? 0),2) }}) - Security Deposit (₹{{ number_format($order->security_deposit ?? 0,2) }})</td>
            <td class="right">₹{{ number_format(($order->balance_amount ?? 0) + ($order->damage_charge ?? 0) + ($order->late_fee ?? 0) - ($order->security_deposit ?? 0),2) }}</td>
        </tr>
        @endif

        @if(($order->final_payable ?? 0) > 0)
        <tr class="final-amount-row total-row">
            <td colspan="7" class="right"><strong>FINAL AMOUNT PAYABLE BY CLIENT</strong></td>
            <td class="right"><strong style="color: #f44336;">₹{{ number_format($order->final_payable,2) }}</strong></td>
        </tr>
        @elseif(($order->final_payable ?? 0) == 0 && ($order->refund_amount ?? 0) == 0)
        <tr class="final-amount-row total-row">
            <td colspan="7" class="right"><strong>SETTLEMENT STATUS</strong></td>
            <td class="right"><strong style="color: #4caf50;">FULLY SETTLED - NO DUES</strong></td>
        </tr>
        @endif

        @if(($order->refund_amount ?? 0) > 0)
        <tr class="final-amount-row total-row">
            <td colspan="7" class="right"><strong>REFUND AMOUNT TO CLIENT</strong></td>
            <td class="right"><strong style="color: #4caf50;">₹{{ number_format($order->refund_amount,2) }}</strong></td>
        </tr>
        @endif

        @endif

    </tfoot>
</table>

{{-- ================= PAYMENT DETAILS (For Settled Orders with Payment Due) ================= --}}
@if($order->settlement_status === 'settled' && ($order->final_payable ?? 0) > 0)
@php
    $paymentAmount = $order->final_payable;
    $upiId = config('payment.upi.id');
    $upiName = config('payment.upi.name');
    $upiUrl = "upi://pay?pa={$upiId}&pn=" . urlencode($upiName) . "&am={$paymentAmount}&cu=INR&tn=" . urlencode("Payment for {$order->order_code}");
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

{{-- ================= NOTES ================= --}}
@if($order->notes && !(($order->settlement_status === 'settled') && ($order->final_payable ?? 0) == 0 && ($order->refund_amount ?? 0) == 0))
<div class="notes">
    @if($order->settlement_status === 'settled')
    <strong>Invoice Notes:</strong>
    @else
    <strong>Terms & Conditions:</strong>
    @endif
    <div>{!! $order->notes !!}</div>
</div>
@endif

<div class="footer">
    {{ $order->settlement_status === 'settled'
        ? 'This invoice is computer generated by Crewrent Enterprises.'
        : 'This order is computer generated by Crewrent Enterprises.'
    }}
</div>

</body>
</html>