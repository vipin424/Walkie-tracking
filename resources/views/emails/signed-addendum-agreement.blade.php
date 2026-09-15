<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Signed Addendum Agreement</title></head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f4f4f4; margin:0; padding:20px;">
<div style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

    <!-- Header -->
    <div style="background:#004d40; padding:24px 30px; text-align:center;">
        <h2 style="color:#fff; margin:0; font-size:20px;">✅ Addendum Agreement Signed</h2>
        <p style="color:rgba(255,255,255,0.8); margin:6px 0 0 0; font-size:13px;">Crewrent Enterprises</p>
    </div>

    <!-- Body -->
    <div style="padding:30px;">
        <p>Dear <strong>{{ $addendum->subscription->client_name }}</strong>,</p>

        <p>The addendum agreement for subscription <strong>{{ $addendum->subscription->subscription_code }}</strong>
        has been <strong style="color:#2e7d32;">successfully signed</strong>.</p>

        <!-- Summary Box -->
        <div style="background:#e8f5e9; border-left:4px solid #2e7d32; padding:16px 20px; margin:20px 0; border-radius:4px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr><td style="padding:4px 0; color:#555; width:200px;">Addendum Code:</td><td><strong>{{ $addendum->addendum_code }}</strong></td></tr>
                <tr><td style="padding:4px 0; color:#555;">Signed On:</td><td><strong>{{ $addendum->signed_at->format('d M Y, h:i A') }}</strong></td></tr>
                <tr><td style="padding:4px 0; color:#555;">Effective Date:</td><td><strong>{{ $addendum->effective_date->format('d M Y') }}</strong></td></tr>
                <tr><td style="padding:4px 0; color:#555;">Pro-Rated Charge (this cycle):</td><td><strong style="color:#e65100;">₹{{ number_format($addendum->pro_rated_amount, 2) }}</strong></td></tr>
                <tr><td style="padding:4px 0; color:#555;">New Monthly Amount:</td><td><strong style="color:#004d40;">₹{{ number_format($addendum->new_monthly_amount, 2) }}</strong></td></tr>
            </table>
        </div>

        <!-- New Items Table -->
        <h4 style="color:#004d40; margin:20px 0 10px 0;">Added Items:</h4>
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#004d40; color:#fff;">
                    <th style="padding:8px; text-align:left;">Item</th>
                    <th style="padding:8px; text-align:center;">Qty</th>
                    <th style="padding:8px; text-align:right;">Rate/Month</th>
                    <th style="padding:8px; text-align:right;">Pro-Rated Charge</th>
                </tr>
            </thead>
            <tbody>
                @foreach($addendum->new_items_json as $item)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:8px;">{{ $item['name'] }}</td>
                    <td style="padding:8px; text-align:center;">{{ $item['quantity'] }}</td>
                    <td style="padding:8px; text-align:right;">₹{{ number_format($item['rate'], 2) }}</td>
                    <td style="padding:8px; text-align:right; color:#e65100;"><strong>₹{{ number_format($item['pro_rated_amount'], 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top:24px; color:#555;">
            The signed addendum PDF is attached to this email for your records.
        </p>

        <p style="font-size:12px; color:#888;">For queries, please contact us at <a href="mailto:info@crewrent.in">info@crewrent.in</a>.</p>
    </div>

    <div style="background:#f0f0f0; padding:16px 30px; text-align:center; font-size:11px; color:#888;">
        Crewrent Enterprises | info@crewrent.in | +91-9324465314
    </div>
</div>
</body>
</html>
