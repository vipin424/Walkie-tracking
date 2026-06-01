<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Combined Dues Statement - {{ $orders->first()->client_name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .company img { height: 80px; margin-bottom: 5px; }
        .client-info { text-align: right; }
        .statement-title {
            background: #1a1f36;
            color: #fff;
            padding: 10px 15px;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .order-block { margin-bottom: 30px; border: 1px solid #e0e0e0; border-radius: 4px; }
        .order-header {
            background: #f5f5f5;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #e0e0e0;
            display: table;
            width: 100%;
        }
        .order-header-left  { display: table-cell; }
        .order-header-right { display: table-cell; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px 10px; border: 1px solid #e1e1e1; font-size: 11px; }
        th { background: #ff9800; color: #000; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; background: #fff8e1; }
        .due-row td { font-weight: bold; background: #ffe0b2; font-size: 13px; }
        .grand-summary {
            margin-top: 20px;
            border: 2px solid #1a1f36;
            padding: 15px;
            background: #f5f5f5;
        }
        .grand-summary table { margin-top: 8px; }
        .grand-summary .grand-total td {
            font-size: 15px;
            font-weight: bold;
            background: #1a1f36;
            color: #fff;
        }
        .payment-section {
            margin-top: 20px;
            border: 2px solid #ff9800;
            padding: 15px;
            background: #fff8e1;
        }
        .payment-section h3 { margin: 0 0 10px 0; color: #e65100; font-size: 13px; }
        .payment-grid { display: table; width: 100%; }
        .bank-details { display: table-cell; width: 58%; padding-right: 20px; vertical-align: top; }
        .bank-details table { margin-top: 8px; }
        .bank-details td { border: none; padding: 3px 10px 3px 0; font-size: 11px; }
        .qr-section { display: table-cell; width: 42%; text-align: center; vertical-align: top; }
        .qr-section img { width: 130px; height: 130px; }
        .footer { position: fixed; bottom: 0; font-size: 10px; width: 100%; text-align: center; color: #888; }
        .badge-pending  { color: #e65100; font-weight: bold; }
        .badge-partial  { color: #1565c0; font-weight: bold; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div class="company">
        <img src="{{ $company?->logo_absolute_path ?? public_path('image/logo.png') }}" alt="Logo">
        <div><strong>{{ $company?->name ?? 'Crewrent Enterprises' }}</strong></div>
        <div style="font-size:11px; line-height:1.5;">
            {!! nl2br(e($company?->address ?? "The Avenue, IA Project Rd, Chimatpada,\nMarol, Andheri East, Mumbai – 400059")) !!}
        </div>
        <div>Phone: {{ $company?->phone ?? '+91-9324465314' }} | Email: {{ $company?->email ?? 'info@crewrent.in' }}</div>
    </div>
    <div class="client-info">
        <h3 style="margin:0 0 6px 0;">Dues Statement</h3>
        <div>Date: {{ now()->format('d M Y') }}</div>
        <div>Client: <strong>{{ $orders->first()->client_name }}</strong></div>
        <div>Phone: {{ $orders->first()->client_phone }}</div>
        @if($orders->first()->client_email)
        <div>Email: {{ $orders->first()->client_email }}</div>
        @endif
        <div style="margin-top:6px;">
            Total Orders: <strong>{{ $orders->count() }}</strong>
        </div>
    </div>
</div>

<div class="statement-title">
    Combined Dues Statement — {{ $orders->first()->client_name }}
</div>

{{-- EACH ORDER BLOCK --}}
@foreach($orders as $order)
<div class="order-block">
    <div class="order-header">
        <div class="order-header-left">
            Order: {{ $order->order_code }} &nbsp;|&nbsp;
            Event: {{ \Carbon\Carbon::parse($order->event_from)->format('d M Y') }} → {{ \Carbon\Carbon::parse($order->event_to)->format('d M Y') }}
            ({{ $order->total_days }} Day{{ $order->total_days > 1 ? 's' : '' }})
        </div>
        <!-- <div class="order-header-right">
            Status:
            <span class="badge-{{ $order->payment_status }}">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div> -->
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Type</th>
                <th class="right">Qty</th>
                <th class="right">Rate/Day</th>
                <th class="right">Days</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $item->item_name }}</strong></td>
                <td>{{ $item->item_type }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ $order->total_days }}</td>
                <td class="right">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            {{-- Item Subtotal (before extra charges) --}}
            <!-- <tr>
                <td colspan="6" class="right">Item Subtotal (Before Tax)</td>
                <td class="right">₹{{ number_format($order->subtotal - $order->tax_amount, 2) }}</td>
            </tr>
            @if($order->tax_amount > 0)
            <tr>
                <td colspan="6" class="right">Tax Amount</td>
                <td class="right">₹{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif -->

            {{-- Delivery Charges --}}
            @if($order->extra_charge_type === 'delivery' && $order->extra_charge_total > 0)
            <tr>
                <td colspan="6" class="right">Delivery &amp; Up/Down Charges</td>
                <td class="right">₹{{ number_format($order->extra_charge_total, 2) }}</td>
            </tr>
            @endif

            {{-- Support Staff Charges --}}
            @if($order->extra_charge_type === 'staff' && $order->extra_charge_total > 0)
            <tr>
                <td colspan="6" class="right">
                    Support Staff: ₹{{ number_format($order->extra_charge_rate, 2) }}/day × {{ $order->total_days }} day{{ $order->total_days > 1 ? 's' : '' }}
                </td>
                <td class="right">₹{{ number_format($order->extra_charge_total, 2) }}</td>
            </tr>
            @endif

            {{-- Discount --}}
            @if($order->discount_amount > 0)
            <tr>
                <td colspan="6" class="right">Discount</td>
                <td class="right" style="color:#388e3c;">- ₹{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif

            <tr class="total-row">
                <td colspan="6" class="right">Grand Total</td>
                <td class="right">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
            @if($order->advance_paid > 0)
            <tr>
                <td colspan="6" class="right">Advance Paid</td>
                <td class="right" style="color:#388e3c;">- ₹{{ number_format($order->advance_paid, 2) }}</td>
            </tr>
            @endif
            @if(($order->damage_charge ?? 0) > 0)
            <tr>
                <td colspan="6" class="right">Damage Charges</td>
                <td class="right" style="color:#c62828;">+ ₹{{ number_format($order->damage_charge, 2) }}</td>
            </tr>
            @endif
            @if(($order->late_fee ?? 0) > 0)
            <tr>
                <td colspan="6" class="right">Late Fee</td>
                <td class="right" style="color:#c62828;">+ ₹{{ number_format($order->late_fee, 2) }}</td>
            </tr>
            @endif
            @if(($order->security_deposit ?? 0) > 0)
            <tr>
                <td colspan="6" class="right">Security Deposit Adjusted</td>
                <td class="right" style="color:#388e3c;">- ₹{{ number_format($order->security_deposit, 2) }}</td>
            </tr>
            @endif
            <tr class="due-row">
                <td colspan="6" class="right">AMOUNT DUE</td>
                <td class="right" style="color:#e65100;">₹{{ number_format($order->final_payable, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endforeach

{{-- GRAND SUMMARY --}}
<div class="grand-summary">
    <strong style="font-size:13px;">Grand Summary</strong>
    <table>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_code }}</td>
                <td>{{ \Carbon\Carbon::parse($order->event_from)->format('d M Y') }} → {{ \Carbon\Carbon::parse($order->event_to)->format('d M Y') }}</td>
                <td class="right">Total: ₹{{ number_format($order->total_amount, 2) }}</td>
                <td class="right" style="color:#e65100; font-weight:bold;">Due: ₹{{ number_format($order->final_payable, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="right">TOTAL OUTSTANDING DUES</td>
                <td class="right">₹{{ number_format($orders->sum('final_payable'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- PAYMENT DETAILS --}}
@php
    $totalDue = $orders->sum('final_payable');
    $upiId    = config('payment.upi.id');
    $upiName  = config('payment.upi.name');
    $upiUrl   = "upi://pay?pa={$upiId}&pn=" . urlencode($upiName) . "&am={$totalDue}&cu=INR&tn=" . urlencode('Dues - ' . $orders->first()->client_name);
    $qrCode   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($upiUrl));
@endphp
<div class="payment-section">
    <h3>💳 Payment Details — Total Due: ₹{{ number_format($totalDue, 2) }}</h3>
    <div class="payment-grid">
        <div class="bank-details">
            <strong>Bank Transfer:</strong>
            <table>
                <tr><td><strong>Bank Name</strong></td><td>{{ config('payment.bank.name') }}</td></tr>
                <tr><td><strong>Account Name</strong></td><td>{{ config('payment.bank.account_name') }}</td></tr>
                <tr><td><strong>Account No.</strong></td><td>{{ config('payment.bank.account_number') }}</td></tr>
                <tr><td><strong>IFSC Code</strong></td><td>{{ config('payment.bank.ifsc_code') }}</td></tr>
                <tr><td><strong>Branch</strong></td><td>{{ config('payment.bank.branch') }}</td></tr>
                <tr><td><strong>PAN Number</strong></td><td>{{ config('payment.bank.pan_number') }}</td></tr>
            </table>
        </div>
        <div class="qr-section">
            <strong>Scan to Pay via UPI</strong><br><br>
            <img src="data:image/png;base64,{{ $qrCode }}" alt="UPI QR"><br>
            <strong style="font-size:13px; color:#e65100;">₹{{ number_format($totalDue, 2) }}</strong><br>
            <span style="font-size:10px;">UPI ID: {{ $upiId }}</span>
        </div>
    </div>
</div>

<div class="footer">
    This statement is computer generated by {{ $company?->name ?? 'Crewrent Enterprises' }}.
</div>

</body>
</html>
