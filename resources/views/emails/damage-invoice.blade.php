<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Damage Invoice – {{ $report->report_code }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
        .email-wrapper { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #c62828, #b71c1c); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; opacity: .85; font-size: 13px; }
        .warning-band { background: #fff3f3; border-left: 4px solid #c62828; padding: 14px 20px; margin: 20px 20px 0; border-radius: 4px; font-size: 13px; }
        .body-content { padding: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-table th { background: #f5f5f5; text-align: left; padding: 10px 12px; font-size: 12px; color: #777; text-transform: uppercase; border-bottom: 1px solid #eee; }
        .info-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .amount-box { text-align: center; background: #fff3f3; border: 2px solid #c62828; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .amount-box .label { font-size: 12px; color: #777; text-transform: uppercase; margin-bottom: 6px; }
        .amount-box .amount { font-size: 32px; font-weight: bold; color: #c62828; }
        .download-btn { display: block; text-align: center; margin: 24px 0; }
        .download-btn a { background: #c62828; color: white; padding: 14px 32px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 15px; }
        .custom-message { background: #f9f9f9; border-left: 3px solid #c62828; padding: 14px; margin: 16px 0; font-size: 13px; color: #555; border-radius: 4px; }
        .footer { background: #f5f5f5; padding: 16px; text-align: center; font-size: 11px; color: #999; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>⚠ Damage / Replacement Invoice</h1>
        <p>Crewrent Enterprises – Equipment Rental Services</p>
    </div>

    <div class="warning-band">
        <strong>Important Notice:</strong> Physical damage charges have been raised on rented equipment.
    </div>

    <div class="body-content">
        <p>Dear <strong>{{ $report->client_name }}</strong>,</p>

        @if($customMessage)
        <div class="custom-message">{{ $customMessage }}</div>
        @else
        <p>
            We have raised a damage/replacement invoice for the rental equipment in your possession.
            The details are mentioned below. Kindly clear the dues at the earliest.
        </p>
        @endif

        <table class="info-table">
            <tr>
                <th>Invoice No.</th>
                <th>Damage Date</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><strong>{{ $report->report_code }}</strong></td>
                <td>{{ $report->damage_date->format('d M Y') }}</td>
                <td><strong style="color:#c62828;">{{ ucfirst($report->status) }}</strong></td>
            </tr>
            @if($report->walkie_model || $report->walkie_unit_id)
            <tr>
                <th colspan="3">Equipment</th>
            </tr>
            <tr>
                <td colspan="3">
                    {{ $report->walkie_model }} {{ $report->walkie_unit_id ? '(Unit: '.$report->walkie_unit_id.')' : '' }}
                    {{ $report->walkie_serial ? '– S/N: '.$report->walkie_serial : '' }}
                </td>
            </tr>
            @endif
            <tr>
                <th colspan="3">Damage Description</th>
            </tr>
            <tr>
                <td colspan="3">{{ $report->damage_description }}</td>
            </tr>
        </table>

        <div class="amount-box">
            <div class="label">Total Amount Payable</div>
            <div class="amount">₹{{ number_format($report->replacement_amount, 2) }}</div>
        </div>

        <div class="download-btn">
            <a href="{{ $downloadUrl }}" target="_blank">📄 Download Invoice PDF</a>
        </div>

        <p style="font-size:12px; color:#777; text-align:center;">
            This download link is valid for 30 days.
        </p>
    </div>

    <div class="footer">
        Crewrent Enterprises | The Avenue, IA Project Rd, Marol, Andheri East, Mumbai – 400059<br>
        Phone: +91-9324465314 | Email: info@crewrent.in
    </div>
</div>
</body>
</html>
