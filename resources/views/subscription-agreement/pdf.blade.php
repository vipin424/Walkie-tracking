<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Equipment Rental Agreement - {{ $agreement->agreement_code }}</title>

    <style>
        /* ===============================
           PAGE SETUP
        ================================ */
        @page {
            margin: 120px 35px 120px 35px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* ===== HEADER — compact ===== */
        .pdf-header {
            background-color: #004d40;
            color: #ffffff;
            padding: 10px 5px 12px 5px;
            text-align: center;
        }

        .logo {
            width: 70px;
            height: 70px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 14px;
        }

        .header-text {
            display: inline-block;
            vertical-align: middle;
            text-align: left;
        }

        .pdf-header h1 {
            font-size: 17px;
            margin: 0 0 3px 0;
            font-weight: 700;
            color: #ffffff;
        }

        .pdf-header p {
            font-size: 12px;
            margin: 0 0 6px 0;
            color: rgba(255,255,255,0.88);
        }

        .agreement-number {
            background-color: #ffffff;
            color: #004d40;
            padding: 5px 16px;
            display: inline-block;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
        }

        /* ===== CONTENT ===== */
        .pdf-content {
            background-color: #ffffff;
            padding: 15px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #004d40;
            margin-bottom: 10px;
            margin-top: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #004d40;
        }

        /* ===== PERIOD HIGHLIGHT BOX ===== */
        .period-box {
            background-color: #e8f5e9;
            border-left: 5px solid #004d40;
            border: 1px solid #c8e6c9;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .period-row {
            margin-bottom: 8px;
        }

        .period-row:last-child {
            margin-bottom: 0;
        }

        .period-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 160px;
        }

        .period-value {
            color: #1b5e20;
            font-weight: bold;
            display: inline-block;
        }

        .period-amount {
            color: #004d40;
            font-size: 14px;
            font-weight: bold;
        }

        /* ===== INFO BOX ===== */
        .info-box {
            background-color: #f5f5f5;
            border-left: 4px solid #004d40;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            margin-bottom: 12px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 140px;
        }

        .info-value {
            color: #222;
            display: inline-block;
        }

        /* ===== TERMS ===== */
        .agreement-terms {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 25px;
        }

        .agreement-terms p {
            margin-bottom: 18px;
            text-align: justify;
            line-height: 1.8;
            color: #333;
        }

        .agreement-terms p:last-child {
            margin-bottom: 0;
        }

        .agreement-terms p strong {
            color: #004d40;
        }

        /* ===== DECLARATION ===== */
        .declaration-box {
            background-color: #fffef0;
            border: 2px solid #004d40;
            padding: 15px;
            margin: 25px 0;
        }

        .declaration-box p {
            font-style: italic;
            color: #444;
            font-size: 10px;
            line-height: 1.8;
        }

        /* ===== SIGNATURE ===== */
        .signature-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            page-break-inside: avoid;
        }

        .signature-box {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .signature-inner {
            page-break-inside: avoid;
        }

        .signature-image {
            max-height: 100px;
            margin: 10px 0;
            border: 2px solid #004d40;
            padding: 10px;
            background-color: #ffffff;
        }

        .signature-info {
            font-size: 10px;
            color: #666;
            margin-top: 8px;
        }

        .signature-name {
            font-weight: bold;
            color: #222;
            margin-bottom: 4px;
            font-size: 12px;
        }

        /* ===== FOOTER (same as order agreement — fixed bottom) ===== */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #004d40;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            height: 100px;
        }

        .footer-content {
            font-size: 10px;
            line-height: 1.8;
            color: #ffffff;
        }

        .footer-divider {
            height: 1px;
            background-color: #ffffff;
            opacity: 0.3;
            margin: 15px 0;
        }

        .contact-info {
            margin-top: 10px;
            font-size: 9px;
            color: #ffffff;
        }

        /* ===== ITEMS TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table th {
            background-color: #004d40;
            color: #ffffff;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table tfoot td {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #004d40;
            font-size: 11px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>

<body>

<div class="container">

    {{-- ===== HEADER (exactly like order agreement) ===== --}}
    <div class="pdf-header">
        @php
            $logoPath = public_path('image/logo.png');
            $logoExists = file_exists($logoPath);
        @endphp

        @if($logoExists)
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                 class="logo" alt="Crewrent Logo">
        @endif
        <span class="header-text">
            <h1>Monthly Equipment Rental Agreement</h1>
            <p>Crewrent Enterprises</p>
            <div class="agreement-number">Agreement No: {{ $agreement->agreement_code }}</div>
        </span>
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="pdf-content">

        {{-- Agreement Period & Subscription Details --}}
        <div class="section-title">Agreement Period &amp; Subscription Details</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Subscription Code:</span>
                <span class="info-value">{{ $agreement->subscription->subscription_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Agreement Start:</span>
                <span class="info-value">{{ $agreement->agreement_start_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Agreement End:</span>
                <span class="info-value">{{ $agreement->agreement_end_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monthly Amount:</span>
                <span class="info-value" style="color:#004d40; font-weight:bold; font-size:13px;">
                    Rs. {{ number_format($agreement->subscription->monthly_amount, 2) }}
                </span>
            </div>
            @if($agreement->security_deposit > 0)
            <div class="info-row">
                <span class="info-label">Security Deposit:</span>
                <span class="info-value" style="color:#004d40; font-weight:bold;">
                    Rs. {{ number_format($agreement->security_deposit, 2) }} (Refundable)
                </span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Billing Day:</span>
                <span class="info-value">{{ $agreement->subscription->billing_day_of_month }}th of every month</span>
            </div>
        </div>

        {{-- Client Information --}}
        <div class="section-title">Client Information</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Client Name:</span>
                <span class="info-value">{{ $agreement->subscription->client_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone Number:</span>
                <span class="info-value">{{ $agreement->subscription->client_phone }}</span>
            </div>
            @if($agreement->subscription->client_email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $agreement->subscription->client_email }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Agreement Date:</span>
                <span class="info-value">
                    {{ $agreement->signed_at ? $agreement->signed_at->format('d M Y') : now()->format('d M Y') }}
                </span>
            </div>
        </div>

        {{-- Rental Equipment Details --}}
        <div class="section-title">Rental Equipment Details</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:25px">#</th>
                    <th>Item Name</th>
                    <th>Type</th>
                    <th style="width:35px">Qty</th>
                    <th style="text-align:right; width:110px">Rate / Month</th>
                    <th style="text-align:right; width:110px">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agreement->subscription->items_json as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item['name'] }}</strong></td>
                    <td>{{ $item['type'] ?? '-' }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td style="text-align:right">Rs. {{ number_format($item['rate'], 2) }}</td>
                    <td style="text-align:right"><strong>Rs. {{ number_format($item['quantity'] * $item['rate'], 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="padding:10px; text-align:right; font-weight:bold;">Total Monthly Amount:</td>
                    <td style="padding:10px; text-align:right; color:#004d40; font-size:12px; font-weight:bold;">
                        Rs. {{ number_format($agreement->subscription->monthly_amount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>


        {{-- Terms & Conditions --}}
        <div class="agreement-terms">
            <div style="page-break-inside: avoid;">
                <div class="section-title" style="margin-top: 0; padding-top: 0; border-bottom: 2px solid #004d40;">Terms &amp; Conditions</div>
                <p style="margin-top: 15px;">
                    This Monthly Equipment Rental Agreement ("Agreement") is entered into between
                    <strong>Crewrent Enterprises</strong> ("Company"), a registered business entity, and
                    <strong>{{ $agreement->subscription->client_name }}</strong> ("Client"), effective from
                    <strong>{{ $agreement->agreement_start_date->format('d F Y') }}</strong> to
                    <strong>{{ $agreement->agreement_end_date->format('d F Y') }}</strong>,
                    for the monthly rental of equipment listed in Subscription
                    <strong>{{ $agreement->subscription->subscription_code }}</strong>.
                </p>
            </div>

            <p>
                <strong>1. Rental Period &amp; Renewal:</strong> This agreement is valid from
                {{ $agreement->agreement_start_date->format('d M Y') }} to
                {{ $agreement->agreement_end_date->format('d M Y') }}.
                A monthly invoice will be issued on day <strong>{{ $agreement->subscription->billing_day_of_month }}</strong>
                of each month. Continuation beyond the agreement period requires a fresh signed agreement.
            </p>

            <p>
                <strong>2. Monthly Payment &amp; Deposit:</strong> The Client agrees to pay a monthly rental fee of
                <strong>Rs. {{ number_format($agreement->subscription->monthly_amount, 2) }}</strong>
                by the billing date each month. Late payments may attract penalty charges as notified by the Company.
                @if($agreement->security_deposit > 0)
                A refundable security deposit of <strong>Rs. {{ number_format($agreement->security_deposit, 2) }}</strong>
                has been collected. Any costs incurred due to damage, loss, or late return shall be deducted from this deposit.
                @endif
            </p>

            <p>
                <strong>3. Equipment Responsibility:</strong> The Client assumes full responsibility for
                the safety, security, and proper use of all rented equipment throughout the subscription period.
                This includes protection against loss, theft, physical damage, water damage, electrical damage,
                misuse, or negligence.
            </p>

            <p>
                <strong>4. Maintenance &amp; Repairs:</strong> Normal maintenance is the Company's responsibility.
                Damage caused by misuse, negligence, or accidents shall be repaired at the Client's expense.
                The Client must immediately report any malfunction or damage to the Company.
            </p>

            <p>
                <strong>5. Prohibited Use:</strong> The Client shall not sub-rent, sell, pledge, or otherwise
                dispose of the equipment. Equipment must only be used for lawful purposes in accordance with
                manufacturer's guidelines and safety instructions.
            </p>

            <p>
                <strong>6. Termination:</strong> Either party may terminate this agreement with
                <strong>30 days' written notice</strong>. Upon termination, all equipment must be returned
                in good working condition and all outstanding dues must be cleared.
            </p>

            <p>
                <strong>7. Governing Law:</strong> This Agreement shall be governed by and construed in
                accordance with the laws of India. Any disputes arising from this Agreement shall be subject
                to the exclusive jurisdiction of the courts located in the Company's registered office jurisdiction.
            </p>
        </div>

        {{-- Declaration --}}
        <div class="declaration-box">
            <p>
                <strong>Client Declaration:</strong> I hereby acknowledge that I have read, understood, and agree
                to abide by all the terms and conditions mentioned in this Monthly Equipment Rental Agreement.
                I confirm that all information provided is accurate and I accept the monthly payment obligations
                and full responsibility for the rented equipment during the agreement period.
            </p>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="section-title">Digital Signature</div>
            <div class="signature-box">
                <div class="signature-inner">
                    <div class="signature-name">{{ $agreement->subscription->client_name }}</div>
                    @if($agreement->signature_image)
                        @php $sigPath = public_path('storage/' . $agreement->signature_image); @endphp
                        @if(file_exists($sigPath))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}"
                                 class="signature-image"
                                 alt="Client Signature">
                        @endif
                    @endif
                    <div class="signature-info">
                        <strong>Signed on:</strong>
                        {{ $agreement->signed_at ? $agreement->signed_at->format('d M Y, h:i A') : 'Not yet signed' }}<br>
                        <strong>Signature ID:</strong> {{ $agreement->agreement_code }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== FOOTER (exactly like order agreement) ===== --}}
    <div class="pdf-footer">
        <div class="footer-content">
            <strong>This is a digitally signed agreement generated by Crewrent Enterprises</strong>
            <div class="footer-divider"></div>
            Document authenticity can be verified using Agreement Code: {{ $agreement->agreement_code }}
        </div>
        <div class="contact-info">
            For queries, contact us at: info@crewrent.in | +91-9324465314<br>
            Visit us at: www.crewrent.in
        </div>
    </div>

</div>

</body>
</html>
