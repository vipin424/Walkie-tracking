<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Addendum Agreement - {{ $addendum->addendum_code }}</title>
    <style>
        @page { margin: 110px 35px 110px 35px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.6; }

        .pdf-header { background-color: #004d40; color: #ffffff; padding: 12px 8px; text-align: center; }
        .logo { width: 65px; height: 65px; display: inline-block; vertical-align: middle; margin-right: 12px; }
        .header-text { display: inline-block; vertical-align: middle; text-align: left; }
        .pdf-header h1 { font-size: 16px; margin: 0 0 3px 0; font-weight: 700; color: #fff; }
        .pdf-header p { font-size: 11px; margin: 0 0 5px 0; color: rgba(255,255,255,0.85); }
        .addendum-number { background: #fff; color: #004d40; padding: 4px 14px; display: inline-block; border-radius: 20px; font-weight: bold; font-size: 11px; }

        .pdf-content { background: #fff; padding: 18px; }
        .section-title { font-size: 14px; font-weight: bold; color: #004d40; margin: 16px 0 10px 0; padding-bottom: 6px; border-bottom: 2px solid #004d40; }

        .info-box { background: #f5f5f5; border-left: 4px solid #004d40; padding: 14px 18px; margin-bottom: 18px; }
        .info-row { margin-bottom: 8px; }
        .info-label { font-weight: bold; color: #555; display: inline-block; width: 160px; }
        .info-value { color: #222; display: inline-block; }

        .pro-notice { background: #fff8e1; border-left: 4px solid #e65100; padding: 10px 14px; margin-bottom: 16px; font-size: 11px; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.items th { background: #004d40; color: #fff; padding: 8px; font-size: 10px; }
        table.items td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        table.items tr:nth-child(even) { background: #f9f9f9; }
        table.items tfoot td { background: #fff3e0; font-weight: bold; border-top: 2px solid #e65100; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .orange { color: #e65100; font-weight: bold; }

        .terms { border: 1px solid #ddd; padding: 14px; margin-bottom: 18px; font-size: 10px; line-height: 1.8; }
        .terms p { margin-bottom: 10px; }
        .terms p strong { color: #004d40; }

        .declaration { background: #fffef0; border: 2px solid #004d40; padding: 12px; margin: 18px 0; }
        .declaration p { font-style: italic; font-size: 10px; line-height: 1.8; color: #444; }

        .signature-section { background: #f5f5f5; padding: 10px; margin-top: 16px; page-break-inside: avoid; }
        .signature-image { max-height: 90px; margin: 8px 0; border: 2px solid #004d40; padding: 8px; background: #fff; }
        .signature-info { font-size: 10px; color: #666; margin-top: 6px; }
        .signature-name { font-weight: bold; color: #222; font-size: 12px; margin-bottom: 4px; }

        .pdf-footer { position: fixed; bottom: -110px; left: -35px; right: -35px; background: #004d40; color: #fff; padding: 12px; text-align: center; height: 110px; }
        .footer-content { font-size: 9px; line-height: 1.8; }
        .footer-divider { height: 1px; background: rgba(255,255,255,0.3); margin: 8px 0; }
    </style>
</head>
<body>

<div class="pdf-header">
    @php $logoPath = public_path('image/logo.png'); @endphp
    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo">
    @endif
    <span class="header-text">
        <h1>Subscription Addendum Agreement</h1>
        <p>Crewrent Enterprises — Amendment to Monthly Equipment Rental</p>
        <div class="addendum-number">Addendum No: {{ $addendum->addendum_code }}</div>
    </span>
</div>

<div class="pdf-content">

    <!-- Subscription & Addendum Details -->
    <div class="section-title">Addendum & Subscription Details</div>
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Subscription Code:</span>
            <span class="info-value">{{ $addendum->subscription->subscription_code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Addendum Code:</span>
            <span class="info-value">{{ $addendum->addendum_code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Effective Date:</span>
            <span class="info-value">{{ $addendum->effective_date->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Addendum End Date:</span>
            <span class="info-value">{{ $addendum->agreement_end_date ? $addendum->agreement_end_date->format('d M Y') : 'Ongoing (Until Sub Ends)' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Billing Day:</span>
            <span class="info-value">{{ $addendum->billing_day_of_month }}th of every month</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pro-Rated Until:</span>
            <span class="info-value">{{ $addendum->pro_rated_until->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pro-Rated Charge:</span>
            <span class="info-value orange">Rs. {{ number_format($addendum->pro_rated_amount, 2) }} (for {{ $addendum->pro_rated_days }} days)</span>
        </div>
        <div class="info-row">
            <span class="info-label">New Monthly Amount:</span>
            <span class="info-value" style="color:#004d40; font-weight:bold; font-size:13px;">Rs. {{ number_format($addendum->new_monthly_amount, 2) }}</span>
        </div>
    </div>

    <!-- Client Info -->
    <div class="section-title">Client Information</div>
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Client Name:</span>
            <span class="info-value">{{ $addendum->subscription->client_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-value">{{ $addendum->subscription->client_phone }}</span>
        </div>
        @if($addendum->subscription->client_email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $addendum->subscription->client_email }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Agreement Date:</span>
            <span class="info-value">{{ $addendum->signed_at ? $addendum->signed_at->format('d M Y') : now()->format('d M Y') }}</span>
        </div>
    </div>

    <!-- New Items -->
    <div class="section-title">Newly Added Equipment</div>
    <div class="pro-notice">
        ⏱ The items below are charged on a <strong>pro-rated basis for {{ $addendum->pro_rated_days }} days</strong>
        ({{ $addendum->effective_date->format('d M') }} – {{ $addendum->pro_rated_until->format('d M Y') }}).
        Full monthly rate applies from the next billing cycle.
    </div>
    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Type</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Rate/Month</th>
                <th class="text-right">Pro-Rated ({{ $addendum->pro_rated_days }}/30 days)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($addendum->new_items_json as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $item['name'] }}</strong>
                    @if(!empty($item['description']))<br><small>{{ $item['description'] }}</small>@endif
                </td>
                <td>{{ $item['type'] ?? '-' }}</td>
                <td class="text-center">{{ $item['quantity'] }}</td>
                <td class="text-right">Rs. {{ number_format($item['rate'], 2) }}</td>
                <td class="text-right orange">Rs. {{ number_format($item['pro_rated_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right" style="padding:8px;">Total Pro-Rated Charge (this billing cycle):</td>
                <td class="text-right orange" style="padding:8px; font-size:12px;">Rs. {{ number_format($addendum->pro_rated_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Terms -->
    <div class="terms">
        <div class="section-title" style="margin-top:0;">Terms of Addendum</div>
        <p>This Addendum Agreement is entered into between <strong>Crewrent Enterprises</strong> ("Company") and
        <strong>{{ $addendum->subscription->client_name }}</strong> ("Client"), effective from
        <strong>{{ $addendum->effective_date->format('d F Y') }}</strong>, as an amendment to subscription
        <strong>{{ $addendum->subscription->subscription_code }}</strong>.</p>

        <p><strong>1. Additional Equipment:</strong> The Client agrees to rent the additional equipment listed above
        under the same terms and conditions as the original Monthly Equipment Rental Agreement.</p>

        <p><strong>2. Pro-Rated Billing:</strong> A pro-rated charge of
        <strong>Rs. {{ number_format($addendum->pro_rated_amount, 2) }}</strong> will be included in the next billing
        cycle (due by {{ $addendum->pro_rated_until->format('d M Y') }}) for {{ $addendum->pro_rated_days }} days of usage.</p>

        <p><strong>3. Revised Monthly Amount:</strong> From the billing cycle following {{ $addendum->pro_rated_until->format('d M Y') }},
        the total monthly rental amount shall be <strong>Rs. {{ number_format($addendum->new_monthly_amount, 2) }}</strong>.</p>

        <p><strong>4. Equipment Responsibility:</strong> The Client assumes full responsibility for the newly added equipment —
        including loss, theft, damage, or misuse — from the effective date of this addendum.</p>

        <p><strong>5. Original Agreement Terms:</strong> All other terms and conditions of the original agreement remain unchanged and in full force.</p>
    </div>

    <!-- Declaration -->
    <div class="declaration">
        <p><strong>Client Declaration:</strong> I hereby acknowledge that I have read, understood, and agree to the terms
        of this Addendum Agreement. I confirm the addition of the above equipment to my subscription and accept the
        revised billing amounts as stated herein.</p>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="section-title">Digital Signature</div>
        <div class="signature-name">{{ $addendum->subscription->client_name }}</div>
        @if($addendum->signature_image)
            @php $sigPath = public_path('storage/' . $addendum->signature_image); @endphp
            @if(file_exists($sigPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}"
                     class="signature-image" alt="Client Signature">
            @endif
        @endif
        <div class="signature-info">
            <strong>Signed on:</strong>
            {{ $addendum->signed_at ? $addendum->signed_at->format('d M Y, h:i A') : 'Not yet signed' }}<br>
            <strong>Addendum ID:</strong> {{ $addendum->addendum_code }}
        </div>
    </div>

</div>

<div class="pdf-footer">
    <div class="footer-content">
        <strong>This is a digitally signed addendum generated by Crewrent Enterprises</strong>
        <div class="footer-divider"></div>
        Document authenticity can be verified using Addendum Code: {{ $addendum->addendum_code }}
    </div>
    <div style="font-size:9px; color:rgba(255,255,255,0.8); margin-top:6px;">
        For queries: info@crewrent.in | +91-9324465314 | www.crewrent.in
    </div>
</div>

</body>
</html>
