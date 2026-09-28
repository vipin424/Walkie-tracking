<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $report->report_code }} – Damage Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 20px; }
        .header { display: table; width: 100%; margin-bottom: 20px; }
        .company { display: table-cell; width: 55%; vertical-align: top; }
        .company img { height: 90px; margin-bottom: 6px; }
        .invoice-meta { display: table-cell; width: 45%; text-align: right; vertical-align: top; }
        .invoice-meta h2 { margin: 0 0 5px 0; font-size: 20px; color: #c62828; letter-spacing: 1px; }
        .invoice-meta .code { font-size: 14px; font-weight: bold; color: #333; }
        .divider { border-top: 2px solid #c62828; margin: 15px 0; }
        .alert-box {
            background: #fff3f3;
            border-left: 4px solid #c62828;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .two-col { display: table; width: 100%; margin-bottom: 20px; }
        .col-left  { display: table-cell; width: 55%; vertical-align: top; padding-right: 15px; }
        .col-right { display: table-cell; width: 45%; vertical-align: top; }
        .info-block { margin-bottom: 14px; }
        .info-block label { font-size: 10px; color: #777; display: block; margin-bottom: 2px; text-transform: uppercase; letter-spacing: .5px; }
        .info-block p { margin: 0; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 9px 10px; border: 1px solid #e0e0e0; }
        thead th { background: #c62828; color: #fff; font-size: 11px; text-transform: uppercase; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; background: #fbe9e7; font-size: 14px; }
        .photos-section { margin-top: 20px; }
        .photos-section h4 { color: #c62828; font-size: 13px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .photos-grid { display: table; width: 100%; }
        .photo-cell { display: table-cell; padding: 5px; vertical-align: top; width: 25%; }
        .photo-cell img { width: 100%; border: 1px solid #ddd; border-radius: 4px; }
        .payment-box { margin-top: 25px; border: 2px solid #c62828; padding: 15px; background: #fff8f8; }
        .payment-box h3 { margin: 0 0 12px; color: #c62828; font-size: 13px; }
        .pay-grid { display: table; width: 100%; }
        .bank-col { display: table-cell; width: 60%; vertical-align: top; padding-right: 20px; }
        .upi-col  { display: table-cell; width: 40%; text-align: center; vertical-align: top; }
        .bank-table td { border: none; padding: 3px 10px 3px 0; font-size: 11px; }
        .notes-box { margin-top: 15px; font-size: 11px; color: #555; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

{{-- ============ HEADER ============ --}}
<div class="header">
    <div class="company">
        <img src="{{ public_path('image/logo.png') }}" alt="Crewrent Logo">
        <div><strong>Crewrent Enterprises</strong></div>
        <div style="font-size:11px; line-height:1.6; color:#555;">
            The Avenue, IA Project Rd, Chimatpada,<br>
            Marol, Andheri East, Mumbai – 400059<br>
            Phone: +91-9324465314 | Email: info@crewrent.in
        </div>
    </div>

    <div class="invoice-meta">
        <h2>⚠ DAMAGE INVOICE</h2>
        <div class="code">{{ $report->report_code }}</div>
        <div style="margin-top:8px; color:#555; font-size:11px;">Date: {{ $report->created_at->format('d M Y') }}</div>
        <div style="margin-top:12px; text-align:right;">
            <strong>Bill To:</strong><br>
            @if($report->billing_details)
                {!! nl2br(e($report->billing_details)) !!}
            @else
                {{ $report->client_name }}<br>
                {{ $report->client_email }}<br>
                {{ $report->client_phone }}
            @endif
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ============ DAMAGE ALERT ============ --}}
<div class="alert-box">
    <strong>⚠ Physical Damage / Replacement Charges Notice</strong><br>
    This invoice has been raised for physical damage caused to rental equipment.
    The amount below reflects replacement / repair charges as per the rental agreement.
</div>

{{-- ============ TWO COLUMNS ============ --}}
<div class="two-col">
    {{-- LEFT: Walkie Details + Damage Info --}}
    <div class="col-left">
        <div class="info-block">
            <label>Damage Report Date</label>
            <p>{{ $report->damage_date->format('d M Y') }}</p>
        </div>
        @if($report->subscription)
        <div class="info-block">
            <label>Related Subscription</label>
            <p>{{ $report->subscription->subscription_code }}</p>
        </div>
        @endif
        <div class="info-block">
            <label>Equipment / Damage Description</label>
            <p style="font-weight:400; line-height:1.5;">{!! nl2br(e($report->damage_description)) !!}</p>
        </div>
    </div>

    {{-- RIGHT: Walkie Details --}}
    <div class="col-right">
        @if($report->walkie_units && count($report->walkie_units) > 0)
        <div style="background:#fff3f3; border:1px solid #ffcdd2; padding:12px; border-radius:4px;">
            <strong style="color:#c62828; font-size:11px; text-transform:uppercase;">Damaged Equipment</strong>
            <table style="margin-top:8px; width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="color:#777; font-size:10px; text-align:left; padding:0 0 4px 0; border:none; background:transparent; font-weight:normal;">Model</th>
                        <th style="color:#777; font-size:10px; text-align:left; padding:0 0 4px 0; border:none; background:transparent; font-weight:normal;">Serial</th>
                        <th style="color:#777; font-size:10px; text-align:left; padding:0 0 4px 0; border:none; background:transparent; font-weight:normal;">Unit ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->walkie_units as $unit)
                    <tr style="font-size:11px; font-weight:600; color:#333;">
                        <td style="text-align:left; padding:3px 0; border:none;">{{ $unit['model'] ?: '—' }}</td>
                        <td style="text-align:left; padding:3px 0; border:none;">{{ $unit['serial'] ?: '—' }}</td>
                        <td style="text-align:left; padding:3px 0; border:none;">{{ $unit['unit_id'] ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ============ CHARGES TABLE ============ --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Equipment</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>
                <strong>Physical Damage / Replacement Charges</strong><br>
                <small style="color:#555; white-space:pre-line;">{{ $report->damage_description }}</small>
            </td>
            <td>
                @if($report->walkie_units && count($report->walkie_units) > 0)
                    {{ count($report->walkie_units) }} Unit(s)
                @else
                    Walkie Talkie
                @endif
            </td>
            <td class="right">₹{{ number_format($report->replacement_amount, 2) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="right"><strong>TOTAL PAYABLE</strong></td>
            <td class="right"><strong>₹{{ number_format($report->replacement_amount, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>

{{-- ============ DAMAGE PHOTOS ============ --}}
@if($report->damage_photos && count($report->damage_photos) > 0)
<div class="photos-section">
    <h4>📷 Damage Photographs</h4>
    <div class="photos-grid">
        @foreach($report->damage_photos as $photo)
        <div class="photo-cell">
            <img src="{{ storage_path('app/public/' . $photo) }}" alt="Damage Photo">
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ============ PAYMENT DETAILS ============ --}}
@if($report->status !== 'paid')
<div class="payment-box">
    <h3>Payment Details</h3>
    <div class="pay-grid">
        <div class="bank-col">
            <strong>Bank Transfer:</strong>
            <table class="bank-table" style="margin-top:8px;">
                <tr><td><strong>Bank Name:</strong></td><td>{{ config('payment.bank.name') }}</td></tr>
                <tr><td><strong>Account Name:</strong></td><td>{{ config('payment.bank.account_name') }}</td></tr>
                <tr><td><strong>Account Number:</strong></td><td>{{ config('payment.bank.account_number') }}</td></tr>
                <tr><td><strong>IFSC Code:</strong></td><td>{{ config('payment.bank.ifsc_code') }}</td></tr>
                <tr><td><strong>Branch:</strong></td><td>{{ config('payment.bank.branch') }}</td></tr>
                <tr><td><strong>PAN Number:</strong></td><td>{{ config('payment.bank.pan_number') }}</td></tr>
            </table>
        </div>
        <div class="upi-col">
            <strong>UPI Payment</strong><br><br>
            <div style="padding:20px; border:2px solid #c62828; background:white; border-radius:8px;">
                <strong style="font-size:13px; color:#004d40;">Amount Payable</strong><br><br>
                <strong style="font-size:22px; color:#c62828;">₹{{ number_format($report->replacement_amount, 2) }}</strong><br><br>
                <strong style="font-size:11px;">UPI ID:</strong><br>
                <span style="font-size:12px; color:#004d40;">{{ config('payment.upi.id') }}</span>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ============ NOTES ============ --}}
@if($report->notes)
<div class="notes-box">
    <strong>Notes:</strong>
    <div style="margin-top:4px;">{!! nl2br(e($report->notes)) !!}</div>
</div>
@endif

<div class="footer">
    This invoice is computer-generated by Crewrent Enterprises. &nbsp;|&nbsp;
    {{ $report->report_code }} &nbsp;|&nbsp; Damage / Replacement Invoice
</div>

</body>
</html>
