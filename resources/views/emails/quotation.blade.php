<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#f4f7fa; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f4f7fa; padding:20px 10px;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="max-width:600px; background-color:#ffffff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#004d40; padding:30px 20px; text-align:center;">
                        <img src="{{ asset('image/logo.png') }}"
                             alt="Crewrent Enterprises"
                             style="max-width:160px; width:100%; height:auto; margin-bottom:12px;">

                        <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:600;">
                            Crewrent Enterprises
                        </h1>

                        <p style="margin:6px 0 0; color:#b2dfdb; font-size:13px;">
                            Your Trusted Partner
                        </p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:30px 20px;">

                        <p style="margin:0 0 20px; color:#333; font-size:15px; line-height:1.6;">
                            Dear <strong>{{ $quotation->client_name }}</strong>,
                        </p>

                        <p style="margin:0 0 20px; color:#555; font-size:14px; line-height:1.7;">
                            Thank you for your interest in our services. We're pleased to share your quotation details with you.
                        </p>

                        <!-- Quotation Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                               style="background-color:#f8f9fc; border-radius:8px; margin-bottom:20px; border-left:4px solid #03a9f4;">
                            <tr>
                                <td style="padding:18px;">
                                    <p style="margin:0 0 6px; color:#888; font-size:12px; text-transform:uppercase;">
                                        Quotation Reference
                                    </p>
                                    <p style="margin:0; color:#004d40; font-size:18px; font-weight:600;">
                                        {{ $quotation->code }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        @if(!empty($messageText))
                        <div style="background-color:#fff8e1; border-left:4px solid #ffc107; border-radius:8px; padding:16px; margin-bottom:20px;">
                            <p style="margin:0; color:#5d4037; font-size:14px; line-height:1.6;">
                                {{ $messageText }}
                            </p>
                        </div>
                        @endif

                        <p style="margin:0 0 24px; color:#555; font-size:14px; line-height:1.7;">
                            Please review the attached quotation document. If you have any questions or require changes, feel free to contact us.
                        </p>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center" style="padding-bottom:24px;">
                                    <a href="mailto:info@crewrent.in"
                                       style="display:block; width:100%; max-width:260px; background-color:#ff9800;
                                       color:#ffffff; text-decoration:none; padding:14px 0; border-radius:6px;
                                       font-size:15px; font-weight:600; text-align:center;">
                                        Contact Us
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; color:#555; font-size:14px; line-height:1.6;">
                            We look forward to working with you!
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8f9fc; padding:24px 20px; border-top:1px solid #e5e7eb;">

                        <p style="margin:0 0 10px; color:#333; font-size:15px; font-weight:600;">
                            Best Regards,
                        </p>

                        <p style="margin:0 0 16px; color:#004d40; font-size:17px; font-weight:600;">
                            Crewrent Enterprises
                        </p>

                        <p style="margin:0 0 6px; font-size:13px;">
                            📞 <a href="tel:+919324465314" style="color:#555; text-decoration:none;">+91-9324465314</a>
                        </p>

                        <p style="margin:0 0 16px; font-size:13px;">
                            ✉️ <a href="mailto:info@crewrent.in" style="color:#555; text-decoration:none;">info@crewrent.in</a>
                        </p>

                        <div style="height:1px; background-color:#e5e7eb; margin:16px 0;"></div>

                        <p style="margin:0; color:#999; font-size:11px; line-height:1.6;">
                            This is an automated email. Please do not reply directly.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
