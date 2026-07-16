<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signed Agreement Confirmation</title>
<style>
  body { margin: 0; padding: 0; background: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
  .wrapper { max-width: 640px; margin: 0 auto; background: #fff; }

  /* ---- Header ---- */
  .email-header {
    background: linear-gradient(135deg, #004d40 0%, #00796b 100%);
    padding: 36px 40px; text-align: center;
  }
  .logo-circle {
    width: 80px; height: 80px; background: #fff; border-radius: 50%;
    margin: 0 auto 18px; display: flex; align-items: center; justify-content: center;
    border: 3px solid #ff9800; overflow: hidden;
  }
  .logo-circle img { width: 100%; height: 100%; object-fit: cover; }
  .email-header h1 { color: #fff; font-size: 22px; margin: 0 0 6px 0; font-weight: 700; }
  .email-header p { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }

  .accent-bar { height: 4px; background: #ff9800; }

  /* ---- Success Banner ---- */
  .success-banner {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
    padding: 22px 40px; text-align: center;
  }
  .checkmark { font-size: 52px; margin-bottom: 8px; }
  .success-banner h2 { color: #fff; font-size: 20px; margin: 0 0 4px 0; }
  .success-banner p { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }

  /* ---- Body ---- */
  .email-body { padding: 36px 40px; }
  .greeting { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 12px; }
  .intro-text { font-size: 14px; line-height: 1.75; color: #555; margin-bottom: 26px; }

  /* ---- Info Card ---- */
  .info-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f5 100%);
    border: 1.5px solid #c8e6c9; border-left: 5px solid #004d40;
    border-radius: 10px; padding: 20px 22px; margin-bottom: 26px;
  }
  .info-row { display: flex; margin-bottom: 9px; }
  .info-row:last-child { margin-bottom: 0; }
  .info-key { font-size: 11.5px; font-weight: 700; color: #004d40; text-transform: uppercase; letter-spacing: 0.5px; width: 180px; flex-shrink: 0; }
  .info-val { font-size: 13px; color: #222; font-weight: 600; }
  .amount-highlight { color: #004d40; font-size: 15px; font-weight: 800; }

  /* ---- Attachment Note ---- */
  .attachment-note {
    background: #fff3e0; border: 1px solid #ffe0b2; border-radius: 8px;
    padding: 16px 20px; margin-bottom: 24px; font-size: 13.5px; color: #e65100;
    text-align: center;
  }

  /* ---- Footer ---- */
  .email-footer { background: #004d40; padding: 24px 40px; text-align: center; }
  .email-footer p { color: rgba(255,255,255,0.75); font-size: 11.5px; margin: 4px 0; }
  .email-footer a { color: #ff9800; text-decoration: none; }
</style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="email-header">
    <div class="logo-circle">
      <img src="{{ asset('image/logo.png') }}" alt="Crewrent Logo">
    </div>
    <h1>Agreement Signed Successfully</h1>
    <p>Crewrent Enterprises</p>
  </div>
  <div class="accent-bar"></div>

  <!-- Success Banner -->
  <div class="success-banner">
    <div class="checkmark">✅</div>
    <h2>Your Agreement is Now Signed!</h2>
    <p>A copy of the signed agreement is attached to this email.</p>
  </div>

  <!-- Body -->
  <div class="email-body">
    <p class="greeting">Hello {{ $agreement->subscription->client_name }},</p>

    <p class="intro-text">
      Thank you for signing the <strong>Monthly Equipment Rental Agreement</strong>.
      Your digital signature has been recorded. Please find the signed agreement attached to this email for your records.
    </p>

    <!-- Info Card -->
    <div class="info-card">
      <div class="info-row">
        <div class="info-key">Subscription Code</div>
        <div class="info-val">{{ $agreement->subscription->subscription_code }}</div>
      </div>
      <div class="info-row">
        <div class="info-key">Agreement No</div>
        <div class="info-val">{{ $agreement->agreement_code }}</div>
      </div>
      <div class="info-row">
        <div class="info-key">Agreement Period</div>
        <div class="info-val">
          {{ $agreement->agreement_start_date->format('d M Y') }}
          &nbsp;→&nbsp;
          {{ $agreement->agreement_end_date->format('d M Y') }}
        </div>
      </div>
      <div class="info-row">
        <div class="info-key">Monthly Amount</div>
        <div class="info-val amount-highlight">₹{{ number_format($agreement->subscription->monthly_amount, 2) }}</div>
      </div>
      <div class="info-row">
        <div class="info-key">Signed On</div>
        <div class="info-val">{{ $agreement->signed_at->format('d M Y, h:i A') }}</div>
      </div>
    </div>

    <!-- Attachment Note -->
    <div class="attachment-note">
      📎 <strong>Signed Agreement PDF is attached</strong> to this email.<br>
      <small>Please save it for your records.</small>
    </div>

    <p style="font-size: 13.5px; color: #555; line-height: 1.7;">
      We will send you monthly invoices on day <strong>{{ $agreement->subscription->billing_day_of_month }}</strong> of each month.
      If you have any questions, feel free to reach out to us.
    </p>
    <p style="font-size: 13.5px; color: #555; line-height: 1.7; margin-top: 12px;">
      Thank you for trusting <strong>Crewrent Enterprises</strong>!
    </p>
  </div>

  <!-- Footer -->
  <div class="email-footer">
    <p><strong style="color:#fff;">Crewrent Enterprises</strong></p>
    <p>📧 <a href="mailto:info@crewrent.in">info@crewrent.in</a> &nbsp;|&nbsp; 📞 +91-9324465314</p>
    <p><a href="https://www.crewrent.in">www.crewrent.in</a></p>
    <p style="margin-top: 12px; font-size: 10.5px; opacity: 0.6;">
      This is an automated email. Please do not reply to this email directly.
    </p>
  </div>

</div>
</body>
</html>
