<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment Rental Agreement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #004d40;
            --secondary-color: #ff9800;
            --primary-light: #00796b;
            --primary-dark: #00251a;
        }
        
        * { box-sizing: border-box; }
        
        body { 
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.03) 35px, rgba(255,255,255,.03) 70px);
            pointer-events: none;
            z-index: 0;
        }
        
        .agreement-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .agreement-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }
        
        .agreement-card::before {
            content: 'CREWRENT';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(0, 77, 64, 0.03);
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
        }
        
        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-color);
        }
       
        .company-logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            overflow: hidden;
            border: 4px solid var(--secondary-color);
        } 
        
        .company-logo img {
            width: 120%;
            height: 120%;
            object-fit: cover;
        }
        
        .logo-placeholder {
            font-size: 48px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .agreement-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }
        
        .company-name {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 300;
        }
        
        .content-section {
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .info-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--secondary-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        .info-value {
            font-size: 15px;
            color: #222;
            font-weight: 600;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--secondary-color) 50%, transparent 100%);
            margin: 32px 0;
        }
        
        .agreement-terms {
            background: #f8f9fc;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 32px;
            border: 1px solid #e1e8ed;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        }
        
        .terms-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .terms-title::before {
            content: '📋';
            font-size: 24px;
        }
        
        .agreement-terms p {
            font-size: 14px;
            line-height: 1.8;
            color: #444;
            margin-bottom: 16px;
            text-align: justify;
        }
        
        .agreement-terms strong {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .signature-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 2px solid #e1e8ed;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .signature-label {
            font-size: 15px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .signature-label::before {
            content: '✍️';
            font-size: 20px;
        }
        
        .signature-canvas-wrapper {
            position: relative;
            border: 2px dashed var(--primary-color);
            border-radius: 8px;
            background: #fafbfc;
            height: 200px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        
        .signature-canvas-wrapper::before {
            content: 'Sign here';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ccc;
            font-size: 18px;
            pointer-events: none;
            z-index: 0;
        }
        
        .signature-canvas-wrapper.has-signature::before {
            display: none;
        }
        
        #signatureCanvas {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 200px;
            cursor: crosshair;
        }
        
        .btn-clear {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-clear:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244,67,54,0.3);
        }
        
        .consent-checkbox {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e1e8ed;
            margin-bottom: 24px;
        }
        
        .consent-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-right: 12px;
            accent-color: var(--primary-color);
        }
        
        .consent-checkbox label {
            font-size: 14px;
            color: #444;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-submit:hover:not(:disabled)::before {
            left: 100%;
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 77, 64, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .expiry-notice {
            text-align: center;
            margin-top: 24px;
            padding: 16px;
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            font-size: 13px;
            color: #666;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid var(--secondary-color);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: none;
            animation: slideIn 0.3s ease;
        }
        
        .alert-warning.show {
            display: block;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .amount-highlight {
            color: var(--secondary-color);
            font-weight: 700;
        }
        
        @media print {
            body { background: white; }
            body::before { display: none; }
            .btn-clear, .btn-submit { display: none; }
            .agreement-card { box-shadow: none; }
        }
        
        @media (max-width: 768px) {
            .content-section { padding: 24px; }
            .info-grid { grid-template-columns: 1fr; gap: 16px; }
            .agreement-title { font-size: 24px; }
            .company-logo { width: 100px; height: 100px; }
        }
    </style>
</head>
<body>

<div class="agreement-container">
    <div class="agreement-card">
        
        <!-- Header -->
        <div class="header-section">
            <div class="company-logo">
                {{-- Dynamic logo image --}}
                <img src="{{ asset('image/logo.png') }}" alt="Company Logo">
                <!-- @if(isset($agreement->order->company_logo) && $agreement->order->company_logo)
                    <img src="{{ asset($agreement->order->company_logo) }}" alt="Company Logo">
                @else
                   <div class="logo-placeholder">CR</div> -->
                <!-- @endif --> 
            </div>
            <h1 class="agreement-title">Equipment Rental Agreement</h1>
            <div class="company-name">Crewrent Enterprises</div>
        </div>
        
        <!-- Content -->
        <div class="content-section">
            
            <!-- Alert for missing signature -->
            <div class="alert-warning" id="signatureAlert">
                ⚠️ Please provide your signature before submitting the agreement.
            </div>
            
            <!-- Order Information Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Order Number</div>
                    <div class="info-value">{{ $agreement->order->order_code }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Client Name</div>
                    <div class="info-value">{{ $agreement->order->client_name }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $agreement->order->client_phone }}</div>
                </div>
            @if($agreement->order->client_email)
                <div class="info-card">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $agreement->order->client_email }}</div>
                </div>
            @endif
                <div class="info-card">
                    <div class="info-label">Event Duration</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($agreement->order->event_from)->format('M d') }} 
                        to 
                        {{ \Carbon\Carbon::parse($agreement->order->event_to)->format('M d, Y') }}
                        ({{ $agreement->order->total_days }} day{{ $agreement->order->total_days > 1 ? 's' : '' }})
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-label">Total Amount</div>
                    <div class="info-value amount-highlight">₹{{ number_format($agreement->order->total_amount, 2) }}</div>
                </div>
            @if($agreement->order->advance_paid)
                <div class="info-card">
                    <div class="info-label">Advance Paid</div>
                    <div class="info-value amount-highlight">₹{{ number_format($agreement->order->advance_paid ?? 0, 2) }}</div>
                </div>
            @endif
            @if($agreement->order->security_deposit)
                <div class="info-card">
                    <div class="info-label">Security Deposit</div>
                    <div class="info-value amount-highlight">₹{{ number_format($agreement->order->security_deposit ?? 0, 2) }}</div>
                </div>
            @endif
            </div>
            
            <div class="divider"></div>
            
            <!-- Agreement Terms -->
            <div class="agreement-terms">
                <div class="terms-title">Terms & Conditions</div>
                
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
            
            <!-- Signature Section -->
            <form method="POST" 
                  action="{{ route('agreement.submit', $agreement->agreement_code) }}" 
                  id="agreementForm">
                @csrf
                
                <div class="signature-section">
                    <div class="signature-label">Client Signature</div>
                    <div class="signature-canvas-wrapper" id="canvasWrapper">
                        <canvas id="signatureCanvas"></canvas>
                    </div>
                    <button type="button" class="btn-clear" id="clearSignature">
                        🗑️ Clear Signature
                    </button>
                    <input type="hidden" name="signature" id="signatureInput">
                </div>
                
                <!-- Consent Checkbox -->
                <div class="consent-checkbox">
                    <label>
                        <input type="checkbox" id="consentCheckbox" required>
                        <span>
                            I hereby confirm that I have carefully read, fully understood, and voluntarily agree 
                            to all the terms and conditions stated in this Equipment Rental Agreement. I acknowledge 
                            my responsibilities and liabilities as outlined above.
                        </span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    ✓ Accept & Sign Agreement
                </button>
            </form>
            
        </div>
    </div>
    
    <!-- Expiry Notice -->
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
        text: 'Thank you. Your agreement has been recorded.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#198754'
    }).then(() => {
        window.location.href = "https://crewrent.in";
    });
});
</script>
@endif

<script>
    // Initialize signature pad with proper canvas sizing
    const canvas = document.getElementById('signatureCanvas');
    const wrapper = document.getElementById('canvasWrapper');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(250, 251, 252, 1)',
        penColor: '#004d40',
        minWidth: 1.5,
        maxWidth: 3,
        throttle: 0,
        velocityFilterWeight: 0.7
    });
    
    // Resize canvas to match wrapper
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = wrapper.offsetWidth * ratio;
        canvas.height = wrapper.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }
    
    // Initial resize
    resizeCanvas();
    
    // Resize on window resize
    window.addEventListener('resize', resizeCanvas);
    
    // Track signature state
    signaturePad.addEventListener('beginStroke', () => {
        wrapper.classList.add('has-signature');
        document.getElementById('signatureAlert').classList.remove('show');
    });
    
    // Clear signature
    document.getElementById('clearSignature').addEventListener('click', () => {
        signaturePad.clear();
        wrapper.classList.remove('has-signature');
    });
    
    // Form submission
    document.getElementById('agreementForm').addEventListener('submit', function(e) {
        const consentChecked = document.getElementById('consentCheckbox').checked;
        const hasSignature = !signaturePad.isEmpty();
        
        if (!hasSignature) {
            e.preventDefault();
            document.getElementById('signatureAlert').classList.add('show');
            document.getElementById('signatureAlert').textContent = 
                '⚠️ Please provide your signature before submitting the agreement.';
            wrapper.style.borderColor = '#f44336';
            setTimeout(() => {
                wrapper.style.borderColor = '#004d40';
            }, 2000);
            return;
        }
        
        if (!consentChecked) {
            e.preventDefault();
            document.getElementById('signatureAlert').classList.add('show');
            document.getElementById('signatureAlert').textContent = 
                '⚠️ Please check the consent box to proceed.';
            return;
        }
        
        // Get signature data
        const signatureData = signaturePad.toDataURL('image/png');
        document.getElementById('signatureInput').value = signatureData;
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.textContent = '⏳ Submitting Agreement...';
        submitBtn.disabled = true;
        
        // Form will submit normally to Laravel backend
    });
    
    // Enable/disable submit button based on consent
    document.getElementById('consentCheckbox').addEventListener('change', function() {
        const submitBtn = document.getElementById('submitBtn');
        if (!this.checked) {
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
        }
    });
    
    // Disable submit button initially
    document.getElementById('submitBtn').disabled = true;
</script>

</body>
</html>