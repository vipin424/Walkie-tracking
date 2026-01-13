<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Order Reminder</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fa; line-height: 1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #f4f7fa;">
        <tr>
            <td style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #004d40 0%, #764ba2 100%); padding: 40px 30px; border-radius: 12px 12px 0 0; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                📋 Order Reminder
                            </h1>
                            <p style="margin: 10px 0 0; color: #e0e7ff; font-size: 14px;">
                                Upcoming Event Notification
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px;">
                                Hello <strong>Admin</strong>,
                            </p>
                            
                            <p style="margin: 0 0 30px; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                This is a friendly reminder that the following order is scheduled in <strong style="color: #dc2626;">2 days</strong>. Please ensure all items are prepared and ready.
                            </p>

                            <!-- Order Details Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border-left: 4px solid #004d40;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h2 style="margin: 0 0 20px; color: #111827; font-size: 18px; font-weight: 600;">
                                            Order Details
                                        </h2>
                                        
                                        <!-- Order Code -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Order Code:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #111827; font-size: 14px; font-weight: 600;">
                                                    {{ $order->order_code }}
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Client -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Client:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $order->client_name }}
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Start Date -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Start Date:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ \Carbon\Carbon::parse($order->event_from)->format('l, F j, Y') }}
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Event -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Event:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $order->event_name ?? '-' }}
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Location -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px; vertical-align: top;">
                                                    <strong>Location:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #111827; font-size: 14px;">
                                                    {{ $order->location ?? '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Call to Action -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ route('orders.show', $order->id) }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #004d40 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);">
                                            View Order Details
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer Message -->
                            <p style="margin: 30px 0 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Please prepare all items and coordinate with your team to ensure smooth execution.
                            </p>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td style="padding: 0 30px 40px;">
                            <p style="margin: 0; color: #374151; font-size: 15px;">
                                Best regards,<br>
                                <strong style="color: #004d40; font-size: 16px;">Crewrent Enterprises</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 30px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
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