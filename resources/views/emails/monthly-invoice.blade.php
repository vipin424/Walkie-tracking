<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #004d40 0%, #00695c 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .invoice-details {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ff9800;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #ff9800;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #f57c00;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .highlight {
            color: #ff9800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Monthly Invoice</h1>
            <p style="margin: 10px 0 0 0;">Crewrent Enterprises</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $invoice->subscription->client_name }}</strong>,</p>
            
            @if($customMessage)
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                <strong style="color: #856404;">⚠️ Payment Reminder</strong>
                <p style="margin: 10px 0 0 0; color: #856404;">{{ $customMessage }}</p>
            </div>
            @else
            <p>We hope this email finds you well. Please find your monthly invoice details below:</p>
            @endif
            
            <div class="invoice-details">
                <h3 style="margin-top: 0; color: #004d40;">Invoice Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0;"><strong>Invoice Number:</strong></td>
                        <td style="padding: 8px 0;">{{ $invoice->invoice_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Item Type:</strong></td>
                        <td style="padding: 8px 0;">
                            @php
                                $types = collect($invoice->subscription->items_json)->pluck('type')->filter()->unique()->implode(', ');
                            @endphp
                            {{ $types ?: 'General Items' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Billing Period:</strong></td>
                        <td style="padding: 8px 0;">{{ $invoice->billing_period_from->format('d M Y') }} to {{ $invoice->billing_period_to->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Invoice Date:</strong></td>
                        <td style="padding: 8px 0;">{{ $invoice->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Amount:</strong></td>
                        <td style="padding: 8px 0; font-size: 18px; color: #ff9800;"><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
            
            <p>You can download your invoice PDF using the button below:</p>
            
            <center>
                <a href="{{ $downloadUrl }}" class="button">Download Invoice PDF</a>
            </center>
            
            <p style="font-size: 12px; color: #666; margin-top: 20px;">
                <strong>Note:</strong> This download link is valid for 30 days.
            </p>
            
            <p>If you have any questions or concerns regarding this invoice, please don't hesitate to contact us.</p>
            
            <p>Thank you for your continued business!</p>
            
            <p>Best regards,<br>
            <strong>Crewrent Enterprises</strong><br>
            Phone: +91-9324465314<br>
            Email: reelrententerprises@gmail.com</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Crewrent Enterprises. All rights reserved.</p>
            <p><a href="https://crewrent.in" style="color: #004d40; text-decoration: none;">www.crewrent.in</a></p>
        </div>
    </div>
</body>
</html>
