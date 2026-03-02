<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Equipment Rental Agreement - {{ $agreement->agreement_code }}</title>

    <style>
        /* ===============================
           PAGE SETUP (MOST IMPORTANT)
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
        
        .pdf-header {
            background-color: #004d40;
            color: #ffffff;
            padding: 15px 5px;
            text-align: center;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            display: inline-block;
        }
        
        .pdf-header h1 {
            font-size: 20px;
            margin: 10px 0 8px 0;
            font-weight: 600;
            color: #ffffff;
        }
        
        .pdf-header p {
            font-size: 13px;
            margin-bottom: 10px;
            color: #ffffff;
        }
        
        .agreement-number {
            background-color: #ffffff;
            color: #004d40;
            padding: 10px 20px;
            margin-top: 10px;
            display: inline-block;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        
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
        
        .agreement-terms p strong {
            color: #004d40;
        }
        
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
        
        .signature-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
        }
        
        .signature-box {
            margin-top: 10px;
        }
        
        .signature-image {
            max-height: 100px;
            margin: 15px 0;
            border: 2px solid #004d40;
            padding: 10px;
            background-color: #ffffff;
        }
        
        .signature-info {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }
        
        .signature-name {
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
            font-size: 12px;
        }
        
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
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Header Section -->
    <div class="pdf-header">
        @php
            $logoPath = public_path('image/logo.png');
            $logoExists = file_exists($logoPath);
        @endphp
        
        @if($logoExists)
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Crewrent Logo">
        @endif
        
        <h1>Equipment Rental Agreement</h1>
        <p>Crewrent Enterprises</p>
        <div class="agreement-number">
            Agreement No: {{ $agreement->agreement_code }}
        </div>
    </div>

    <!-- Content Section -->
    <div class="pdf-content">

        <!-- Client Information -->
        <div class="section-title">Client Information</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Order Code:</span>
                <span class="info-value">{{ $agreement->order->order_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Client Name:</span>
                <span class="info-value">{{ $agreement->order->client_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone Number:</span>
                <span class="info-value">{{ $agreement->order->client_phone }}</span>
            </div>
            @if($agreement->order->client_email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $agreement->order->client_email }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Event Date:</span>
                <span class="info-value">
                    {{ $agreement->order->event_from->format('d M Y') }}
                    to
                    {{ $agreement->order->event_to->format('d M Y') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Agreement Date:</span>
                <span class="info-value">{{ $agreement->signed_at->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Rental Items -->
        <div class="section-title">Rental Equipment Details</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agreement->order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->item_name }}</strong></td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                    <td>₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Terms & Conditions -->
        <div class="section-title">Terms & Conditions</div>
        <div class="agreement-terms">
            <p>
                This Equipment Rental Agreement ("Agreement") is entered into on this day between 
                <strong>Crewrent Enterprises</strong> ("Company"), a registered business entity, and 
                <strong>{{ $agreement->order->client_name }}</strong> ("Client"), for the rental of equipment as specified in the order details above.
            </p>
            
            <p>
                <strong>1. Equipment Receipt:</strong> The Client hereby confirms and acknowledges that all equipment 
                listed in Order No. {{ $agreement->order->order_code }} has been received in good working condition, properly inspected, 
                and is suitable and fit for the intended purpose and use.
            </p>
            
            <p>
                <strong>2. Responsibility & Liability:</strong> The Client agrees to assume full and complete 
                responsibility for the safety, security, and proper use of all rented equipment throughout the 
                entire rental period. This includes but is not limited to protection against loss, theft, physical 
                damage, water damage, electrical damage, misuse, or negligence.
            </p>
            
            <p>
                <strong>3. Security Deposit:</strong> A refundable security deposit of 
                ₹{{ number_format($agreement->order->security_deposit ?? 0, 2) }} has been collected. 
                Any costs incurred due to damage, loss, late return, cleaning, or repair shall be deducted 
                from this deposit. If the total deductions exceed the security deposit amount, the Client agrees 
                to pay the remaining balance immediately upon notification.
            </p>
            
            <p>
                <strong>4. Return Policy:</strong> All equipment must be returned to the Company on or before 
                {{ \Carbon\Carbon::parse($agreement->order->event_to)->format('F d, Y') }} at the agreed time 
                in the same condition as received, normal wear and tear excepted. 
                Late returns will attract additional rental charges calculated on a per-day basis as per the 
                Company's standard rates.
            </p>
            
            <p>
                <strong>5. Prohibited Use:</strong> The Client shall not sub-rent, sell, pledge, or otherwise 
                dispose of the equipment. The equipment shall only be used for lawful purposes and in accordance 
                with the manufacturer's guidelines and safety instructions.
            </p>
            
            <p>
                <strong>6. Governing Law:</strong> This Agreement shall be governed by and construed in accordance 
                with the laws of India. Any disputes arising from this Agreement shall be subject to the exclusive 
                jurisdiction of the courts located in the Company's registered office jurisdiction.
            </p>
        </div>

        <!-- Declaration -->
        <div class="declaration-box">
            <p>
                <strong>Client Declaration:</strong> I hereby acknowledge that I have read, understood, and agree to abide by all the terms and conditions mentioned in this rental agreement. I confirm that all the information provided above is accurate and I accept full responsibility for the rented equipment until its safe return to Crewrent Enterprises.
            </p>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="section-title">Digital Signature</div>
            <div class="signature-box">
                <div class="signature-name">{{ $agreement->order->client_name }}</div>
                @if($agreement->signature_image)
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/'.$agreement->signature_image))) }}" 
                         class="signature-image" 
                         alt="Client Signature">
                @endif
                <div class="signature-info">
                    <strong>Signed on:</strong> {{ $agreement->signed_at->format('d M Y, h:i A') }}<br>
                    <strong>Signature ID:</strong> {{ $agreement->agreement_code }}
                </div>
            </div>
        </div>

    </div>

    <!-- Footer Section -->
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