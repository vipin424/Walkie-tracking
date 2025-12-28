<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Agreement Signing Required</title>
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
            .mobile-header-padding {
                padding: 20px 15px !important;
            }
            .mobile-font-16 {
                font-size: 18px !important;
            }
            .mobile-font-14 {
                font-size: 14px !important;
            }
            .mobile-font-13 {
                font-size: 13px !important;
            }
            .mobile-font-12 {
                font-size: 12px !important;
            }
            .logo-mobile {
                height: 50px !important;
            }
            .button-mobile {
                padding: 14px 30px !important;
                font-size: 16px !important;
                display: block !important;
            }
            .info-box-mobile {
                padding: 15px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f7fb;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f6f7fb; padding: 20px 0;">
    <tr>
        <td align="center">
            
            <!-- Main Container -->
            <table class="main-table" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 600px;">
                
                <!-- Header -->
                <tr>
                    <td class="mobile-header-padding" style="padding: 30px 20px; text-align: center; background-color: #ff9800;">
                        <img src="{{ asset('image/logo.png') }}" alt="Crewrent" class="logo-mobile" style="height: 60px; margin-bottom: 10px; display: inline-block;">
                        <h2 class="mobile-font-16" style="color: #ffffff; margin: 10px 0 0; font-size: 22px; font-weight: 600; line-height: 1.3;">
                            Agreement Signing Required
                        </h2>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="mobile-padding" style="padding: 30px; color: #333;">
                        
                        <!-- Greeting -->
                        <p class="mobile-font-14" style="font-size: 16px; margin: 0 0 15px 0; line-height: 1.6;">
                            Hello <strong>{{ $order->client_name }}</strong>,
                        </p>

                        <p class="mobile-font-13" style="font-size: 15px; margin: 0 0 15px 0; line-height: 1.6;">
                            Thank you for confirming your order with <strong>Crewrent Enterprises</strong>.
                        </p>

                        <p class="mobile-font-13" style="font-size: 15px; margin: 0 0 20px 0; line-height: 1.6;">
                            To proceed further, please review and sign the agreement for your order:
                        </p>

                        <!-- Order Info Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                            <tr>
                                <td class="info-box-mobile" style="background-color: #f9f9f9; padding: 20px; border-radius: 6px; border-left: 4px solid #ff9800;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="padding-bottom: 10px;">
                                                <p class="mobile-font-13" style="margin: 0; font-size: 14px; line-height: 1.6;">
                                                    <strong style="color: #555;">Order Code:</strong><br>
                                                    <span style="color: #222; font-size: 15px;">{{ $order->order_code }}</span>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="mobile-font-13" style="margin: 0; font-size: 14px; line-height: 1.6;">
                                                    <strong style="color: #555;">Event Dates:</strong><br>
                                                    <span style="color: #222; font-size: 15px;">
                                                        {{ optional($order->event_from)->format('d M Y') }}
                                                        to
                                                        {{ optional($order->event_to)->format('d M Y') }}
                                                    </span>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $link }}" class="button-mobile" style="display: inline-block; background-color: #198754; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; line-height: 1.4;">
                                        📝 Sign Agreement Now
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Validity Warning -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                            <tr>
                                <td style="background-color: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; border-radius: 4px;">
                                    <p class="mobile-font-12" style="margin: 0; color: #e65100; font-size: 13px; line-height: 1.6;">
                                        ⏱ <strong>Important:</strong> This link is valid for <strong>48 hours</strong> only. Please sign the agreement at your earliest convenience.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Alternative Link -->
                        <p class="mobile-font-12" style="font-size: 13px; color: #777; margin: 0 0 10px 0; line-height: 1.6;">
                            If the button above doesn't work, copy and paste this link in your browser:
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                            <tr>
                                <td style="background-color: #f5f5f5; padding: 12px; border-radius: 4px; word-break: break-all;">
                                    <p class="mobile-font-12" style="margin: 0; font-size: 12px; color: #555; line-height: 1.6;">
                                        {{ $link }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Divider -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                            <tr>
                                <td style="border-top: 1px solid #e0e0e0;"></td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p class="mobile-font-13" style="font-size: 14px; margin: 0 0 5px 0; line-height: 1.6; color: #555;">
                            <strong style="color: #333;">Need Help?</strong>
                        </p>
                        <p class="mobile-font-12" style="font-size: 13px; margin: 0; line-height: 1.8; color: #555;">
                            📞 <a href="tel:+919324465314" style="color: #ff9800; text-decoration: none;">+91-9324465314</a><br>
                            ✉️ <a href="mailto:info@crewrent.in" style="color: #ff9800; text-decoration: none; word-break: break-all;">info@crewrent.in</a><br>
                            🌐 <a href="https://www.crewrent.in" style="color: #ff9800; text-decoration: none; word-break: break-all;">www.crewrent.in</a>
                        </p>

                        <!-- Closing -->
                        <p class="mobile-font-13" style="font-size: 14px; margin: 25px 0 0 0; line-height: 1.6; color: #333;">
                            Best regards,<br>
                            <strong style="color: #ff9800;">Crewrent Enterprises Team</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="mobile-padding" style="background-color: #f1f1f1; padding: 20px 15px; text-align: center;">
                        <p class="mobile-font-12" style="margin: 0 0 5px 0; font-size: 12px; color: #666; line-height: 1.6;">
                            This is an automated email. Please do not reply to this email address.
                        </p>
                        <p style="margin: 0; font-size: 11px; color: #999; line-height: 1.4;">
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