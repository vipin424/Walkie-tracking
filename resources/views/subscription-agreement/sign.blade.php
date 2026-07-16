<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Equipment Rental Agreement | {{ $agreement->subscription->subscription_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <style>
        :root {
            --primary: #004d40;
            --primary-light: #00796b;
            --accent: #ff9800;
        }

        * { box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.03) 35px, rgba(255,255,255,.03) 70px);
            pointer-events: none; z-index: 0;
        }

        .agreement-container { max-width: 920px; margin: 0 auto; position: relative; z-index: 1; }

        .agreement-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35); overflow: hidden;
        }

        .agreement-card::before {
            content: 'CREWRENT';
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px; font-weight: 900;
            color: rgba(0, 77, 64, 0.03); z-index: 0;
            white-space: nowrap; pointer-events: none;
        }

        /* ---- Header ---- */
        .header-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; padding: 40px 30px; text-align: center; position: relative; z-index: 1;
        }
        .header-section::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: 4px; background: var(--accent);
        }
        .company-logo {
            width: 110px; height: 110px; background: white; border-radius: 50%;
            margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2); overflow: hidden;
            border: 4px solid var(--accent);
        }
        .company-logo img { width: 120%; height: 120%; object-fit: cover; }
        .agreement-title { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; }
        .company-name { font-size: 15px; opacity: 0.9; font-weight: 300; }
        .agreement-badge {
            display: inline-block; margin-top: 14px;
            background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35);
            padding: 6px 20px; border-radius: 20px; font-size: 13px; font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ---- Sign Banner ---- */
        .sign-banner {
            position: sticky; top: 0;
            background: linear-gradient(135deg, var(--accent) 0%, #e65100 100%);
            color: white; padding: 12px 20px; text-align: center;
            font-weight: 600; font-size: 14px; z-index: 999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer; transition: all 0.3s;
        }
        .sign-banner:hover { background: linear-gradient(135deg, #e65100 0%, var(--accent) 100%); }
        .sign-banner.hidden { display: none; }

        /* ---- Content ---- */
        .content-section { padding: 40px; position: relative; z-index: 1; }

        /* ---- Period Highlight Banner ---- */
        .period-banner {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border: 2px solid #c8e6c9; border-radius: 12px;
            padding: 20px 24px; margin-bottom: 28px;
            display: flex; flex-wrap: wrap; gap: 20px; align-items: center;
        }
        .period-item { flex: 1; min-width: 160px; }
        .period-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--primary); font-weight: 600; margin-bottom: 4px; }
        .period-value { font-size: 16px; font-weight: 700; color: #1b5e20; }
        .period-amount { background: var(--primary); color: white; padding: 12px 20px; border-radius: 8px; text-align: center; min-width: 160px; }
        .period-amount .amount-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
        .period-amount .amount-value { font-size: 22px; font-weight: 800; margin-top: 2px; }

        /* ---- Info Cards ---- */
        .info-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px; margin-bottom: 28px;
        }
        .info-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            padding: 18px; border-radius: 10px; border-left: 4px solid var(--accent);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: transform 0.2s;
        }
        .info-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .info-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--primary); font-weight: 600; margin-bottom: 5px; }
        .info-value { font-size: 14px; color: #222; font-weight: 600; }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--accent) 50%, transparent 100%);
            margin: 28px 0;
        }

        /* ---- Items Table ---- */
        .items-section {
            background: white; padding: 24px; border-radius: 12px;
            border: 2px solid #e1e8ed; margin-bottom: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .items-title { font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .items-title::before { content: '📦'; font-size: 22px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; padding: 12px; text-align: left; font-size: 12px;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .items-table td { padding: 12px; border-bottom: 1px solid #e1e8ed; font-size: 13px; color: #444; }
        .items-table tr:hover { background: #f8f9fc; }
        .items-table tr:last-child td { border-bottom: none; }
        .items-table tfoot td { font-weight: 700; background: #f8f9fc; font-size: 14px; }
        .text-right { text-align: right; }

        /* ---- Terms ---- */
        .agreement-terms {
            background: #f8f9fc; padding: 28px; border-radius: 12px;
            margin-bottom: 28px; border: 1px solid #e1e8ed;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        }
        .terms-title { font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
        .terms-title::before { content: '📋'; font-size: 22px; }
        .agreement-terms p { font-size: 13.5px; line-height: 1.8; color: #444; margin-bottom: 14px; text-align: justify; }
        .agreement-terms strong { color: var(--primary); font-weight: 600; }

        /* ---- Signature ---- */
        .signature-section {
            background: white; padding: 24px; border-radius: 12px;
            border: 2px solid #e1e8ed; margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .signature-label { font-size: 15px; font-weight: 600; color: var(--primary); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .signature-label::before { content: '✍️'; font-size: 20px; }
        .signature-canvas-wrapper {
            position: relative; border: 2px dashed var(--primary); border-radius: 8px;
            background: #fafbfc; height: 200px; margin-bottom: 12px; overflow: hidden;
        }
        .signature-canvas-wrapper::before {
            content: 'Sign here'; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%); color: #ccc; font-size: 18px;
            pointer-events: none; z-index: 0;
        }
        .signature-canvas-wrapper.has-signature::before { display: none; }
        #signatureCanvas { position: relative; z-index: 1; width: 100%; height: 200px; cursor: crosshair; }
        .btn-clear {
            background: #f44336; color: white; border: none; padding: 8px 20px;
            border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s;
        }
        .btn-clear:hover { background: #d32f2f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(244,67,54,0.3); }

        /* ---- Consent ---- */
        .consent-checkbox { background: #f8f9fc; padding: 18px; border-radius: 8px; border: 2px solid #e1e8ed; margin-bottom: 22px; }
        .consent-checkbox input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; margin-right: 10px; accent-color: var(--primary); }
        .consent-checkbox label { font-size: 13.5px; color: #444; cursor: pointer; display: flex; align-items: flex-start; gap: 10px; }

        /* ---- Submit Button ---- */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; border: none; padding: 16px; border-radius: 8px;
            font-size: 16px; font-weight: 700; width: 100%; cursor: pointer;
            transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px;
            position: relative; overflow: hidden;
        }
        .btn-submit::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-submit:hover:not(:disabled)::before { left: 100%; }
        .btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,77,64,0.4); }
        .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ---- Alert ---- */
        .alert-warning-custom {
            background: #fff3cd; border-left: 4px solid var(--accent);
            padding: 14px; border-radius: 8px; margin-bottom: 20px;
            display: none; animation: slideIn 0.3s ease;
        }
        .alert-warning-custom.show { display: block; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* ---- Expiry Notice ---- */
        .expiry-notice {
            text-align: center; margin-top: 22px; padding: 14px;
            background: rgba(255,255,255,0.95); border-radius: 8px;
            font-size: 13px; color: #666; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            body { padding: 8px 0; }
            .content-section { padding: 20px; }
            .info-grid { grid-template-columns: 1fr; }
            .agreement-title { font-size: 22px; }
            .period-banner { flex-direction: column; }
            .items-section { padding: 14px; }
            .items-table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>

{{-- Sticky sign banner --}}
<div class="sign-banner" id="signBanner" onclick="scrollToSignature()">
    ✍️ Scroll down to sign the agreement &nbsp;|&nbsp; नीचे स्क्रॉल करें और साइन करें
</div>

<div class="agreement-container">
    <div class="agreement-card" style="position:relative;">

        {{-- Header --}}
        <div class="header-section">
            <div class="company-logo">
                <img src="{{ asset('image/logo.png') }}" alt="Crewrent Logo">
            </div>
            <h1 class="agreement-title">Monthly Equipment Rental Agreement</h1>
            <div class="company-name">Crewrent Enterprises</div>
            <div class="agreement-badge">Agreement No: {{ $agreement->agreement_code }}</div>
        </div>

        {{-- Content --}}
        <div class="content-section">

            {{-- Signature-missing alert --}}
            <div class="alert-warning-custom" id="signatureAlert">
                ⚠️ Please provide your signature before submitting the agreement.
            </div>

            {{-- Agreement Period + Monthly Amount Banner --}}
            <div class="period-banner">
                <div class="period-item">
                    <div class="period-label">Subscription Code</div>
                    <div class="period-value">{{ $agreement->subscription->subscription_code }}</div>
                </div>
                <div class="period-item">
                    <div class="period-label">Agreement Start</div>
                    <div class="period-value">{{ $agreement->agreement_start_date->format('d M Y') }}</div>
                </div>
                <div class="period-item">
                    <div class="period-label">Agreement End</div>
                    <div class="period-value">{{ $agreement->agreement_end_date->format('d M Y') }}</div>
                </div>
                <div class="period-amount">
                    <div class="amount-label">Monthly Amount</div>
                    <div class="amount-value">₹{{ number_format($agreement->subscription->monthly_amount, 2) }}</div>
                </div>
                @if($agreement->security_deposit > 0)
                <div class="period-amount" style="background:#ff9800; color:white;">
                    <div class="amount-label">Security Deposit (Refundable)</div>
                    <div class="amount-value">₹{{ number_format($agreement->security_deposit, 2) }}</div>
                </div>
                @endif
            </div>

            {{-- Client Info Cards --}}
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Client Name</div>
                    <div class="info-value">{{ $agreement->subscription->client_name }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $agreement->subscription->client_phone }}</div>
                </div>
                @if($agreement->subscription->client_email)
                <div class="info-card">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $agreement->subscription->client_email }}</div>
                </div>
                @endif
                <div class="info-card">
                    <div class="info-label">Billing Day</div>
                    <div class="info-value">{{ $agreement->subscription->billing_day_of_month }}<sup>th</sup> of every month</div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Items Table --}}
            <div class="items-section">
                <div class="items-title">Rental Equipment Details</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th style="width:70px" class="text-right">Qty</th>
                            <th style="width:130px" class="text-right">Rate / Month</th>
                            <th style="width:130px" class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agreement->subscription->items_json as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item['name'] }}</strong></td>
                            <td style="color:#888">{{ $item['type'] ?? '-' }}</td>
                            <td class="text-right">{{ $item['quantity'] }}</td>
                            <td class="text-right">₹{{ number_format($item['rate'], 2) }}</td>
                            <td class="text-right"><strong>₹{{ number_format($item['quantity'] * $item['rate'], 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right" style="padding: 14px 12px;">Total Monthly Amount:</td>
                            <td class="text-right" style="padding: 14px 12px; color: #004d40; font-size: 16px;">
                                ₹{{ number_format($agreement->subscription->monthly_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="divider"></div>

            {{-- Terms & Conditions --}}
            <div class="agreement-terms">
                <div class="terms-title">Terms &amp; Conditions</div>

                <p>
                    This Monthly Equipment Rental Agreement ("Agreement") is entered into between
                    <strong>Crewrent Enterprises</strong> ("Company") and
                    <strong>{{ $agreement->subscription->client_name }}</strong> ("Client"),
                    effective from <strong>{{ $agreement->agreement_start_date->format('d F Y') }}</strong>
                    to <strong>{{ $agreement->agreement_end_date->format('d F Y') }}</strong>,
                    for the monthly rental of equipment as listed in Subscription
                    <strong>{{ $agreement->subscription->subscription_code }}</strong>.
                </p>

                <p>
                    <strong>1. Rental Period &amp; Renewal:</strong> This agreement is valid from
                    {{ $agreement->agreement_start_date->format('d M Y') }} to
                    {{ $agreement->agreement_end_date->format('d M Y') }}. The Company will issue
                    a monthly invoice on day <strong>{{ $agreement->subscription->billing_day_of_month }}</strong>
                    of each month. Continuation beyond the agreement period requires a fresh signed agreement.
                </p>

                <p>
                    <strong>2. Monthly Payment &amp; Deposit:</strong> The Client agrees to pay a monthly rental fee of
                    <strong>₹{{ number_format($agreement->subscription->monthly_amount, 2) }}</strong>
                    by the billing date each month. Late payments may attract penalty charges as notified by the Company.
                    @if($agreement->security_deposit > 0)
                    A refundable security deposit of <strong>₹{{ number_format($agreement->security_deposit, 2) }}</strong>
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
                    in good working condition, and all outstanding dues must be cleared.
                </p>

                <p>
                    <strong>7. Governing Law:</strong> This Agreement shall be governed by and construed in
                    accordance with the laws of India. Any disputes arising from this Agreement shall be subject
                    to the exclusive jurisdiction of the courts located in the Company's registered office jurisdiction.
                </p>
            </div>

            {{-- Signature Form --}}
            <form method="POST"
                  action="{{ route('subscription-agreement.submit', $agreement->agreement_code) }}"
                  id="agreementForm">
                @csrf

                <div class="signature-section" id="signatureArea">
                    <div class="signature-label">Client Digital Signature</div>
                    <div class="signature-canvas-wrapper" id="canvasWrapper">
                        <canvas id="signatureCanvas"></canvas>
                    </div>
                    <button type="button" class="btn-clear" id="clearSignature">
                        🗑️ Clear Signature
                    </button>
                    <input type="hidden" name="signature" id="signatureInput">
                </div>

                <div class="consent-checkbox">
                    <label>
                        <input type="checkbox" id="consentCheckbox" required>
                        <span>
                            I hereby confirm that I have carefully read, fully understood, and voluntarily agree
                            to all the terms and conditions stated in this Monthly Equipment Rental Agreement.
                            I acknowledge the monthly payment obligations and my responsibilities as outlined above.
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    ✓ Accept &amp; Sign Agreement
                </button>
            </form>

        </div>
    </div>

    <div class="expiry-notice">
        🕐 This agreement link is valid until
        <strong>{{ \Carbon\Carbon::parse($agreement->expires_at)->format('F d, Y \a\t h:i A') }}</strong>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('agreement_signed'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Agreement Signed Successfully!',
        html: 'Thank you <strong>{{ $agreement->subscription->client_name }}</strong>.<br>Your agreement has been recorded and a signed copy has been sent to your email.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#004d40'
    });
});
</script>
@endif

<script>
let signaturePad;

document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signatureCanvas');
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255,255,255,0)',
        penColor: '#1a237e',
        minWidth: 1.5,
        maxWidth: 3,
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const wrapper = document.getElementById('canvasWrapper');
        canvas.width = wrapper.offsetWidth * ratio;
        canvas.height = wrapper.offsetHeight * ratio;
        canvas.style.width = wrapper.offsetWidth + 'px';
        canvas.style.height = wrapper.offsetHeight + 'px';
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    signaturePad.addEventListener('endStroke', function () {
        document.getElementById('canvasWrapper').classList.add('has-signature');
        checkFormValidity();
    });

    document.getElementById('clearSignature').addEventListener('click', function () {
        signaturePad.clear();
        document.getElementById('canvasWrapper').classList.remove('has-signature');
        checkFormValidity();
    });

    document.getElementById('consentCheckbox').addEventListener('change', checkFormValidity);

    function checkFormValidity() {
        const hasSignature = !signaturePad.isEmpty();
        const hasConsent = document.getElementById('consentCheckbox').checked;
        document.getElementById('submitBtn').disabled = !(hasSignature && hasConsent);
    }

    // Hide sign banner after scrolling to signature
    const signBanner = document.getElementById('signBanner');
    const sigArea = document.getElementById('signatureArea');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) signBanner.classList.add('hidden'); });
    }, { threshold: 0.3 });
    observer.observe(sigArea);

    document.getElementById('agreementForm').addEventListener('submit', function (e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            document.getElementById('signatureAlert').classList.add('show');
            document.getElementById('signatureArea').scrollIntoView({ behavior: 'smooth' });
            return;
        }
        document.getElementById('signatureInput').value = signaturePad.toDataURL('image/png');
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = '⏳ Processing...';
    });
});

function scrollToSignature() {
    document.getElementById('signatureArea').scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>
