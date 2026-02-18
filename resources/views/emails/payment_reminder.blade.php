<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Reminder</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-container { width: 100% !important; margin: 0 !important; }
            .mobile-padding { padding: 20px !important; }
            .mobile-text { font-size: 14px !important; }
            .mobile-heading { font-size: 24px !important; }
            .button { padding: 12px 24px !important; font-size: 14px !important; }
            .detail-label { width: 100px !important; font-size: 13px !important; }
            .detail-value { font-size: 13px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fa; line-height: 1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f4f7fa;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="email-container" style="max-width: 600px; width: 100%; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td class="mobile-padding" style="background: linear-gradient(135deg, #004d40 0%, #00695c 100%); padding: 40px 30px; border-radius: 12px 12px 0 0; text-align: center;">
                            <h1 class="mobile-heading" style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                💰 Payment Reminder
                            </h1>
                            <p style="margin: 10px 0 0; color: #e0f2f1; font-size: 14px;">
                                Pending Payment Notification
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 30px;">
                            <p class="mobile-text" style="margin: 0 0 20px; color: #374151; font-size: 16px;">
                                Dear <strong>{{ $order->client_name }}</strong>,
                            </p>
                            
                            <p class="mobile-text" style="margin: 0 0 30px; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                This is a friendly reminder regarding your pending payment for the following order. Please make the payment at your earliest convenience.
                            </p>

                            <!-- Order Details Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border-left: 4px solid #ff9800;">
                                <tr>
                                    <td class="mobile-padding" style="padding: 24px;">
                                        <h2 style="margin: 0 0 20px; color: #111827; font-size: 18px; font-weight: 600;">
                                            Order Details
                                        </h2>
                                        
                                        <!-- Order Code -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td class="detail-label" style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Order Code:</strong>
                                                </td>
                                                <td class="detail-value" style="padding: 8px 0; color: #111827; font-size: 14px; font-weight: 600;">
                                                    {{ $order->order_code }}
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Event Period -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td class="detail-label" style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Event Period:</strong>
                                                </td>
                                                <td class="detail-value" style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $eventFrom }} to {{ $eventTo }}
                                                </td>
                                            </tr>
                                        </table>

                                        @if($eventTime)
                                        <!-- Event Time -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td class="detail-label" style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Event Time:</strong>
                                                </td>
                                                <td class="detail-value" style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $eventTime }}
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        @if($eventLocation)
                                        <!-- Location -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td class="detail-label" style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Location:</strong>
                                                </td>
                                                <td class="detail-value" style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $eventLocation }}
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Total Amount -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td class="detail-label" style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Total Amount:</strong>
                                                </td>
                                                <td class="detail-value" style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    ₹{{ number_format($order->total_amount, 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pending Amount -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-top: 20px; background-color: #fff3e0; border-radius: 8px; border-left: 4px solid #ff5722;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 5px; color: #6b7280; font-size: 14px;">Pending Amount</p>
                                        <p style="margin: 0; color: #ff5722; font-size: 32px; font-weight: 700;">
                                            ₹{{ number_format($order->final_payable, 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Payment Methods -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-top: 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0 0 15px; color: #6b7280; font-size: 14px;">Accept payments via:</p>
                                        <div style="display: inline-block;">
                                            <span style="display: inline-block; padding: 8px 15px; margin: 5px; background: #e3f2fd; border-radius: 20px; font-size: 13px; color: #1976d2;">💳 GPay</span>
                                            <span style="display: inline-block; padding: 8px 15px; margin: 5px; background: #e3f2fd; border-radius: 20px; font-size: 13px; color: #1976d2;">📱 PhonePe</span>
                                            <span style="display: inline-block; padding: 8px 15px; margin: 5px; background: #e3f2fd; border-radius: 20px; font-size: 13px; color: #1976d2;">💰 Paytm</span>
                                            <span style="display: inline-block; padding: 8px 15px; margin: 5px; background: #e3f2fd; border-radius: 20px; font-size: 13px; color: #1976d2;">🏦 Bank Transfer</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer Message -->
                            <p class="mobile-text" style="margin: 30px 0 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                If you have already made the payment, please ignore this reminder or contact us with your transaction details.
                            </p>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td class="mobile-padding" style="padding: 0 30px 40px;">
                            <p class="mobile-text" style="margin: 0; color: #374151; font-size: 15px;">
                                Best regards,<br>
                                <strong style="color: #004d40; font-size: 16px;">Crewrent Enterprises</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="mobile-padding" style="background-color: #f9fafb; padding: 24px 30px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center; line-height: 1.5;">
                                This is an automated reminder from Crewrent Enterprises<br>
                                © {{ date('Y') }} Crewrent Enterprises. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
