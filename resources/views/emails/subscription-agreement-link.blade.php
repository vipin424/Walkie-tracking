<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Rental Agreement – Signing Required</title>
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

  /* ---- Orange accent bar ---- */
  .accent-bar { height: 4px; background: #ff9800; }

  /* ---- Body ---- */
  .email-body { padding: 36px 40px; }

  .greeting { font-size: 17px; font-weight: 600; color: #1a1a1a; margin-bottom: 14px; }

  .intro-text { font-size: 14px; line-height: 1.75; color: #555; margin-bottom: 28px; }

  /* ---- Info Card ---- */
  .info-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f5 100%);
    border: 1.5px solid #c8e6c9; border-left: 5px solid #004d40;
    border-radius: 10px; padding: 22px 24px; margin-bottom: 28px;
  }
  .info-row { display: flex; margin-bottom: 10px; }
  .info-row:last-child { margin-bottom: 0; }
  .info-key { font-size: 12px; font-weight: 700; color: #004d40; text-transform: uppercase; letter-spacing: 0.5px; width: 180px; flex-shrink: 0; }
  .info-val { font-size: 13px; color: #222; font-weight: 600; }

  .amount-highlight { color: #004d40; font-size: 16px; font-weight: 800; }

  /* ---- CTA Button ---- */
  .btn-section { text-align: center; margin-bottom: 28px; }
  .sign-btn {
    display: inline-block; background: linear-gradient(135deg, #004d40 0%, #00796b 100%);
    color: #fff !important; text-decoration: none; padding: 16px 40px;
    border-radius: 8px; font-size: 16px; font-weight: 700; letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(0,77,64,0.35);
  }

  /* ---- Link fallback ---- */
  .link-fallback { background: #f5f5f5; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; }
  .link-fallback p { font-size: 12px; color: #777; margin: 0 0 6px 0; }
  .link-fallback a { font-size: 12px; color: #004d40; word-break: break-all; }

  /* ---- Notice ---- */
  .notice-box {
    background: #fff8e1; border: 1px solid #ffe082; border-radius: 6px;
    padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #6d4c00;
  }

  /* ---- Footer ---- */
  .email-footer {
    background: #004d40; padding: 24px 40px; text-align: center;
  }
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
    <h1>Monthly Rental Agreement</h1>
    <p>Signing Required | Crewrent Enterprises</p>
  </div>
  <div class="accent-bar"></div>

  <!-- Body -->
  <div class="email-body">
    <p class="greeting">Hello {{ $subscription->client_name }},</p>

    <p class="intro-text">
      Please find below your <strong>Monthly Equipment Rental Agreement</strong> for review.
      Kindly go through all the terms and click the button below to digitally sign the agreement.
    </p>

    <!-- Info Card -->
    <div class="info-card">
      <div class="info-row">
        <div class="info-key">Subscription Code</div>
        <div class="info-val">{{ $subscription->subscription_code }}</div>
      </div>
      @if($subscription->agreement)
      <div class="info-row">
        <div class="info-key">Agreement Period</div>
        <div class="info-val">
          {{ $subscription->agreement->agreement_start_date->format('d M Y') }}
          &nbsp;→&nbsp;
          {{ $subscription->agreement->agreement_end_date->format('d M Y') }}
        </div>
      </div>
      @endif
      <div class="info-row">
        <div class="info-key">Monthly Amount</div>
        <div class="info-val amount-highlight">₹{{ number_format($subscription->monthly_amount, 2) }}</div>
      </div>
      <div class="info-row">
        <div class="info-key">Billing Day</div>
        <div class="info-val">{{ $subscription->billing_day_of_month }}<sup>th</sup> of every month</div>
      </div>
    </div>

    <!-- CTA -->
    <div class="btn-section">
      <a href="{{ $link }}" class="sign-btn">✍️ &nbsp; Click Here to Sign Agreement</a>
    </div>

    <!-- Link fallback -->
    <div class="link-fallback">
      <p>If the button doesn't work, copy and paste this link in your browser:</p>
      <a href="{{ $link }}">{{ $link }}</a>
    </div>

    <!-- Notice -->
    <div class="notice-box">
      ⏰ <strong>Note:</strong> This signing link is valid for <strong>90 days</strong>.
      Please sign at your earliest convenience.
      If you have any questions, please contact us.
    </div>

    <p style="font-size: 13.5px; color: #555; line-height: 1.7;">
      Thank you for choosing Crewrent Enterprises. We look forward to a long and smooth partnership!
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
