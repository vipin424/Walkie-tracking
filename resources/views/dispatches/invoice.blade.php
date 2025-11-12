<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $dispatch->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        h1, h2, h3, h4 { margin: 0; padding: 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 20px; }
        .footer { border-top: 1px solid #aaa; padding-top: 10px; font-size: 12px; text-align: center; color: #777; }
        .summary td { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header" style="text-align: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <img src="{{ public_path('image/logo.png') }}" alt="Company Logo" style="width: 100px; height: auto; margin-bottom: 8px;">
        <h2 style="margin: 0; font-size: 22px; color: #222;">CREWRENT ENTERPRISES</h2>
        <p style="margin: 5px 0 0; font-size: 14px;">
            <strong>Invoice for Dispatch:</strong> {{ $dispatch->code }}
        </p>
    </div>


    <table>
        <tr>
            <td><strong>Client Name:</strong> {{ $dispatch->client->name }}</td>
            <td><strong>Contact:</strong> {{ $dispatch->client->contact_number }}</td>
        </tr>
        <tr>
            <td><strong>Company:</strong> {{ $dispatch->client->company_name ?? '-' }}</td>
            <td><strong>Dispatch Date:</strong> {{ $dispatch->dispatch_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>Expected Return:</strong> {{ optional($dispatch->expected_return_date)->format('d M Y') }}</td>
            <td><strong>Status:</strong> {{ $dispatch->status }}</td>
        </tr>

        <tr>
            <td><strong>Total Rental Days:</strong> {{ $totalDays }}</td>
            <td><strong>Final Return Date:</strong> 
                {{ $finalReturnDate ? $finalReturnDate->format('d M Y') : '-' }}
            </td>
        </tr>

    </table>

    <h4>Dispatch Items</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Type</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Qty</th>
                <th>Rate/Day</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatch->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item_type }}</td>
                <td>{{ $item->brand ?? '-' }}</td>
                <td>{{ $item->model ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->rate_per_day, 2) }}</td>
                <td>₹{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="text-right">Total Days:</td>
            <td class="text-right">{{ $totalDays }} days</td>
        </tr>
        <tr>
            <td class="text-right">Sub Total:</td>
            <td class="text-right">₹{{ number_format($dispatch->items->sum('total_amount'), 2) }}</td>
        </tr>
        <tr>
            <td class="text-right">Advance Paid:</td>
            <td class="text-right">₹{{ number_format($dispatch->payment->advance_amount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right">Net Payable:</td>
            <td class="text-right">₹{{ number_format(($dispatch->payment->total_amount ?? 0) - ($dispatch->payment->advance_amount ?? 0), 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for choosing CrewRent Enterprises!</p>
        <p>Generated on {{ now()->format('d M Y h:i A') }}</p>
    </div>

</body>
</html>
