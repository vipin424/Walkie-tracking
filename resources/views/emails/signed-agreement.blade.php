<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Signed Agreement - {{ $agreement->agreement_code }}</title>
    <style>
        /* Reset styles */
        body, table, td, p, a { 
            -webkit-text-size-adjust: 100%; 
            -ms-text-size-adjust: 100%; 
        }
        table, td { 
            mso-table-lspace: 0pt; 
            mso-table-rspace: 0pt; 
        }
        img { 
            -ms-interpolation-mode: bicubic; 
            border: 0; 
            height: auto; 
            line-height: 100%; 
            outline: none; 
            text-decoration: none; 
        }
        
        /* Mobile-specific styles */
        @media only screen and (max-width: 600px) {
            .main-table {
                width: 100% !important;
                min-width: 100% !important;
            }
            .mobile-padding {
                padding: 20px 15px !important;
            }
            .mobile-font-16 {
                font-size: 16px !important;
            }
            .mobile-font-14 {
                font-size: 14px !important;
            }
            .mobile-font-13 {
                font-size: 13px !important;
            }
            .info-label-mobile {
                display: block !important;
                width: 100% !important;
                margin-bottom: 5px !important;
            }
            .info-value-mobile {
                display: block !important;
                width: 100% !important;
            }
            .logo-mobile {
                width: 80px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table class="main-table" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 600px;">
                    
                    <!-- Header -->
                    <tr>
                        <td class="mobile-padding" style="background-color: #004d40; padding: 40px 30px; text-align: center;">
                            <img src="{{ asset('image/logo.png') }}" alt="Crewrent Logo" class="logo-mobile" style="width: 100px; height: auto; margin-bottom: 15px; display: inline-block;">
                            <h1 class="mobile-font-16" style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; line-height: 1.3;">Agreement Signed Successfully</h1>
                            <p class="mobile-font-13" style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px; opacity: 0.9; line-height: 1.4;">Thank you for your trust in Crewrent Enterprises</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 30px;">
                            
                            <!-- Greeting -->
                            <p class="mobile-font-14" style="font-size: 16px; color: #333; margin: 0 0 20px 0; line-height: 1.6;">
                                Dear <strong>{{ $agreement->order->client_name }}</strong>,
                            </p>
                            
                            <!-- Success Message -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 20px; border-radius: 4px;">
                                        <p class="mobile-font-13" style="margin: 0; color: #2e7d32; font-size: 15px; line-height: 1.6;">
                                            ✓ Your equipment rental agreement has been successfully signed and verified. We have received your digital signature and the agreement is now active.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Main Message -->
                            <p class="mobile-font-13" style="font-size: 15px; color: #555; margin: 0 0 20px 0; line-height: 1.8;">
                                We are pleased to confirm that your rental agreement has been processed successfully. Please find the <strong>signed agreement document attached</strong> to this email for your records.
                            </p>
                            
                            <!-- Agreement Details Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f5f5f5; border-radius: 6px; margin-bottom: 25px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 class="mobile-font-14" style="margin: 0 0 15px 0; color: #004d40; font-size: 16px; border-bottom: 2px solid #004d40; padding-bottom: 10px;">Agreement Details</h3>
                                        
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span class="info-label-mobile" style="font-weight: bold; color: #555; font-size: 14px; display: inline-block; vertical-align: top; width: 160px;">Agreement Code:</span>
                                                    <span class="info-value-mobile" style="color: #222; font-size: 14px; display: inline-block;">{{ $agreement->agreement_code }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span class="info-label-mobile" style="font-weight: bold; color: #555; font-size: 14px; display: inline-block; vertical-align: top; width: 160px;">Order Code:</span>
                                                    <span class="info-value-mobile" style="color: #222; font-size: 14px; display: inline-block;">{{ $agreement->order->order_code }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span class="info-label-mobile" style="font-weight: bold; color: #555; font-size: 14px; display: inline-block; vertical-align: top; width: 160px;">Client Name:</span>
                                                    <span class="info-value-mobile" style="color: #222; font-size: 14px; display: inline-block;">{{ $agreement->order->client_name }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span class="info-label-mobile" style="font-weight: bold; color: #555; font-size: 14px; display: inline-block; vertical-align: top; width: 160px;">Event Dates:</span>
                                                    <span class="info-value-mobile" style="color: #222; font-size: 14px; display: inline-block;">{{ $agreement->order->event_from->format('d M Y') }} to {{ $agreement->order->event_to->format('d M Y') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span class="info-label-mobile" style="font-weight: bold; color: #555; font-size: 14px; display: inline-block; vertical-align: top; width: 160px;">Signed On:</span>
                                                    <span class="info-value-mobile" style="color: #222; font-size: 14px; display: inline-block;">{{ $agreement->signed_at->format('d M Y, h:i A') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Important Notice -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background-color: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; border-radius: 4px;">
                                        <p class="mobile-font-13" style="margin: 0; color: #e65100; font-size: 13px; line-height: 1.6;">
                                            <strong>📌 Important:</strong> Please keep this signed agreement for your records. You may need to present it during equipment pickup and return.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Next Steps -->
                            <h3 class="mobile-font-14" style="color: #004d40; font-size: 16px; margin: 0 0 15px 0;">What's Next?</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 0 0 8px 20px; color: #555; font-size: 14px; line-height: 1.8; position: relative;">
                                                    <span style="position: absolute; left: 0;">•</span>
                                                    Equipment will be ready for pickup/delivery as per the scheduled dates
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0 0 8px 20px; color: #555; font-size: 14px; line-height: 1.8; position: relative;">
                                                    <span style="position: absolute; left: 0;">•</span>
                                                    Our team will contact you 24 hours before the event for final confirmation
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0 0 8px 20px; color: #555; font-size: 14px; line-height: 1.8; position: relative;">
                                                    <span style="position: absolute; left: 0;">•</span>
                                                    Please ensure the equipment is returned in good condition by the agreed date
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0 0 0 20px; color: #555; font-size: 14px; line-height: 1.8; position: relative;">
                                                    <span style="position: absolute; left: 0;">•</span>
                                                    Your security deposit will be refunded after successful equipment return and inspection
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Contact Section -->
                            <p class="mobile-font-13" style="font-size: 15px; color: #555; margin: 0 0 15px 0; line-height: 1.6;">
                                If you have any questions or need assistance, please don't hesitate to reach out to us:
                            </p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 15px; background-color: #f5f5f5; border-radius: 6px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding-bottom: 8px;">
                                                    <p class="mobile-font-13" style="margin: 0; color: #555; font-size: 14px; line-height: 1.6;">
                                                        📞 <strong>Phone:</strong> <a href="tel:+919324465314" style="color: #004d40; text-decoration: none;">+91-9324465314</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 8px;">
                                                    <p class="mobile-font-13" style="margin: 0; color: #555; font-size: 14px; line-height: 1.6;">
                                                        ✉️ <strong>Email:</strong> <a href="mailto:info@crewrent.in" style="color: #004d40; text-decoration: none; word-break: break-all;">info@crewrent.in</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="mobile-font-13" style="margin: 0; color: #555; font-size: 14px; line-height: 1.6;">
                                                        🌐 <strong>Website:</strong> <a href="https://www.crewrent.in" style="color: #004d40; text-decoration: none; word-break: break-all;">www.crewrent.in</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Closing -->
                            <p class="mobile-font-13" style="font-size: 15px; color: #555; margin: 0 0 10px 0; line-height: 1.6;">
                                We look forward to serving you and making your event a grand success!
                            </p>
                            
                            <p class="mobile-font-13" style="font-size: 15px; color: #333; margin: 20px 0 0 0; line-height: 1.6;">
                                Warm regards,<br>
                                <strong style="color: #004d40; font-size: 16px;">Crewrent Enterprises Team</strong>
                            </p>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="mobile-padding" style="background-color: #004d40; padding: 25px 30px; text-align: center;">
                            <p class="mobile-font-13" style="margin: 0 0 10px 0; color: #ffffff; font-size: 12px; line-height: 1.6;">
                                This is an automated email. Please do not reply to this email address.
                            </p>
                            <p style="margin: 0; color: #ffffff; font-size: 11px; opacity: 0.8; line-height: 1.4;">
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