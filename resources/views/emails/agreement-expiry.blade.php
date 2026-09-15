<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Agreement Expiry Reminder</title></head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f4f4f4; margin:0; padding:20px;">
<div style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

    <div style="background:#f57c00; padding:24px 30px; text-align:center;">
        <h2 style="color:#fff; margin:0; font-size:20px;">⚠️ Expiry Reminder: {{ $docType }}</h2>
        <p style="color:rgba(255,255,255,0.9); margin:6px 0 0 0; font-size:13px;">Crewrent Enterprises</p>
    </div>

    <div style="padding:30px;">
        <p>Dear <strong>{{ $clientName }}</strong>,</p>

        <p>This is a polite reminder that your <strong>{{ $docType }}</strong> for subscription 
        <strong>{{ $subscriptionCode }}</strong> is set to expire in <strong>10 days</strong>.</p>

        <div style="background:#fff8e1; border-left:4px solid #f57c00; padding:16px 20px; margin:20px 0; border-radius:4px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr><td style="padding:4px 0; color:#555; width:150px;">Document Code:</td><td><strong>{{ $docCode }}</strong></td></tr>
                <tr><td style="padding:4px 0; color:#555;">Expiry Date:</td><td><strong style="color:#d32f2f;">{{ $endDate }}</strong></td></tr>
            </table>
        </div>

        <p>Please let us know if you wish to <strong>extend</strong> this agreement or <strong>terminate</strong> it and arrange for the return of the equipment.</p>

        <p style="margin-top:24px;">Please reply to this email or contact our support team at your earliest convenience to ensure uninterrupted service.</p>

    </div>

    <div style="background:#f0f0f0; padding:16px 30px; text-align:center; font-size:11px; color:#888;">
        Crewrent Enterprises | info@crewrent.in | +91-9324465314
    </div>
</div>
</body>
</html>
