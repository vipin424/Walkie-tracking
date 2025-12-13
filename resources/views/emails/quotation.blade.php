<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #004d40; padding: 40px 30px; text-align: center;">
                            <!-- Logo -->
                            <img src="{{ asset('image/logo.png') }}" alt="Crewrent Enterprises" style="max-width: 180px; height: auto; margin-bottom: 16px; display: inline-block;">
                            
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600; letter-spacing: -0.5px;">
                                Crewrent Enterprises
                            </h1>
                            <p style="margin: 8px 0 0 0; color: #b2dfdb; font-size: 14px;">
                                Your Trusted Partner
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <p style="margin: 0 0 24px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Dear <strong>{{ $quotation->client_name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 24px 0; color: #555555; font-size: 15px; line-height: 1.7;">
                                Thank you for your interest in our services. We're pleased to share your quotation details with you.
                            </p>
                            
                            <!-- Quotation Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fc; border-radius: 8px; margin: 0 0 24px 0; border-left: 4px solid #03a9f4;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 8px 0; color: #888888; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Quotation Reference
                                        </p>
                                        <p style="margin: 0; color: #004d40; font-size: 20px; font-weight: 600;">
                                            {{ $quotation->code }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            @if(!empty($messageText))
                            <!-- Custom Message -->
                            <div style="background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 8px; padding: 20px 24px; margin: 0 0 24px 0;">
                                <p style="margin: 0; color: #5d4037; font-size: 15px; line-height: 1.7;">
                                    {{ $messageText }}
                                </p>
                            </div>
                            @endif
                            
                            <p style="margin: 0 0 24px 0; color: #555555; font-size: 15px; line-height: 1.7;">
                                Please review the attached quotation document. If you have any questions or would like to discuss any modifications, our team is ready to assist you.
                            </p>
                            
                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 32px 0;">
                                <tr>
                                    <td style="border-radius: 6px; background-color: #ff9800;">
                                        <a href="mailto:info@crewrent.com" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; letter-spacing: 0.3px;">
                                            Contact Us
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 0; color: #555555; font-size: 15px; line-height: 1.7;">
                                We look forward to working with you!
                            </p>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fc; padding: 32px 30px; border-top: 1px solid #e5e7eb;">
                            
                            <p style="margin: 0 0 16px 0; color: #333333; font-size: 16px; font-weight: 600;">
                                Best Regards,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #004d40; font-size: 18px; font-weight: 600;">
                                Crewrent Enterprises
                            </p>
                            
                            <!-- Contact Info -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="color: #888888; font-size: 14px;">📞</span>
                                        <a href="tel:+919324465314" style="color: #555555; text-decoration: none; font-size: 14px; margin-left: 8px;">
                                            +91-9324465314
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="color: #888888; font-size: 14px;">✉️</span>
                                        <a href="mailto:info@crewrent.com" style="color: #555555; text-decoration: none; font-size: 14px; margin-left: 8px;">
                                            info@crewrent.com
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Divider -->
                            <div style="height: 1px; background-color: #e5e7eb; margin: 24px 0;"></div>
                            
                            <p style="margin: 0; color: #999999; font-size: 12px; line-height: 1.6;">
                                This is an automated message. Please do not reply directly to this email. 
                                For any inquiries, please contact us using the information provided above.
                            </p>
                            
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>