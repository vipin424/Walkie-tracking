<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_code }} - Monthly Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        .company img { height: 100px !important; margin-bottom: 5px; }
        .client { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #e1e1e1; }
        th { background: #ff9800; color: #000; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; }
        .final-amount-row { background: #ffe0b2; font-size: 14px; }
        .billing-period { background: #fff3e0; padding: 15px; margin: 20px 0; border-left: 4px solid #ff9800; }
        .footer { position: fixed; bottom: 0px; font-size: 10px; width: 100%; text-align: center; color: #888; }
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
        <img src="{{ public_path('image/logo.png') }}" alt="Logo">
        <div><strong>Crewrent Enterprises</strong></div>
        <div style="font-size:11px; line-height:1.5;">
            The Avenue, IA Project Rd, Chimatpada,<br>
            Marol, Andheri East, Mumbai – 400059
        </div>
        <div>Phone: +91-9324465314 | Email: info@crewrent.in</div>
    </div>

    <div class="client">
        <h3>Monthly Invoice</h3>
        <div><strong>{{ $invoice->invoice_code }}</strong></div>
        <div>Date: {{ $invoice->created_at->format('d M Y') }}</div>
        @if($invoice->subscription->billing_details)
        <div style="margin-top: 10px;">
            <strong>Bill To:</strong><br>
            {!! nl2br(e($invoice->subscription->billing_details)) !!}
        </div>
        @else
        <div>To: <strong>{{ $invoice->subscription->client_name }}</strong></div>
        <div>{{ $invoice->subscription->client_email }}</div>
        <div>{{ $invoice->subscription->client_phone }}</div>
        @endif
    </div>
</div>

<div class="billing-period">
    <strong>Billing Period:</strong> {{ $invoice->billing_period_from->format('d M Y') }} to {{ $invoice->billing_period_to->format('d M Y') }}
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Item Description</th>
            <th>Type</th>
            <th class="right">Qty</th>
            <th class="right">Rate/Month</th>
            <th class="right">Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->subscription->items_json as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $item['name'] }}</strong><br><small>{{ $item['description'] ?? '' }}</small></td>
            <td>{{ $item['type'] ?? '-' }}</td>
            <td class="right">{{ $item['quantity'] }}</td>
            <td class="right">₹{{ number_format($item['rate'], 2) }}</td>
            <td class="right">₹{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="final-amount-row total-row">
            <td colspan="5" class="right"><strong>TOTAL AMOUNT</strong></td>
            <td class="right"><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>

{{-- ================= PAYMENT DETAILS ================= --}}
@if($invoice->status !== 'paid')
@php
    $paymentAmount = $invoice->amount;
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
            <strong>UPI Payment</strong><br><br>
            <div style="padding: 30px; border: 2px solid #ff9800; background: white; border-radius: 10px;">
                <strong style="font-size: 16px; color: #004d40;">Amount Payable</strong><br><br>
                <strong style="font-size: 24px; color: #f44336;">₹{{ number_format($paymentAmount, 2) }}</strong><br><br>
                <strong style="font-size: 12px;">UPI ID:</strong><br>
                <span style="font-size: 14px; color: #004d40;">{{ config('payment.upi.id') }}</span>
            </div>
        </div>
    </div>
</div>
@endif

@if($invoice->subscription->notes)
<div style="margin-top: 20px; font-size: 11px;">
    <strong>Notes:</strong>
    <div>{!! nl2br(e($invoice->subscription->notes)) !!}</div>
</div>
@endif

<div class="footer">
    This invoice is computer generated by Crewrent Enterprises.
</div>

</body>
</html>
