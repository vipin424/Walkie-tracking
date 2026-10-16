<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $monthlyQuotation->code }} - Monthly Rate Quotation</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 20px; }

    /* ─── Company Header ─── */
    .header-section { display: table; width: 100%; margin-bottom: 20px; }
    .company-block  { display: table-cell; width: 60%; vertical-align: top; }
    .company-block img { height: 70px; width: 70px; object-fit: contain; }
    .company-block .co-name  { font-size: 16px; font-weight: bold; color: #333; margin-top: 5px; }
    .company-block .co-addr  { font-size: 10px; color: #555; line-height: 1.6; margin-top: 4px; }
    .title-block { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
    .title-block h1 { font-size: 26px; color: #ff9800; margin: 0 0 6px 0; letter-spacing: 2px; }
    .title-block .code { font-size: 14px; font-weight: bold; color: #333; }
    .title-block .meta { font-size: 11px; color: #555; margin-top: 3px; }

    /* ─── Divider ─── */
    .divider { border: none; border-top: 2px solid #ff9800; margin: 12px 0; }

    /* ─── Bill To + Validity Section ─── */
    .bill-section { display: table; width: 100%; margin-bottom: 16px; }
    .bill-to      { display: table-cell; width: 55%; vertical-align: top; }
    .bill-to h3   { margin: 0 0 6px 0; font-size: 12px; color: #ff9800; text-transform: uppercase; letter-spacing: 1px; }
    .bill-to p    { margin: 0; font-size: 11px; line-height: 1.7; }
    .validity-box { display: table-cell; width: 45%; vertical-align: top; }
    .validity-table { width: 100%; border-collapse: collapse; }
    .validity-table td { padding: 4px 8px; font-size: 11px; }
    .validity-table td:first-child { color: #888; width: 50%; }
    .validity-table td:last-child { font-weight: bold; color: #333; text-align: right; }

    /* ─── Items Table ─── */
    table.items { width: 100%; border-collapse: collapse; margin-top: 12px; margin-bottom: 0; }
    table.items thead tr { background: #ff9800; color: #fff; }
    table.items th { padding: 8px 10px; text-align: left; font-size: 11px; }
    table.items td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
    table.items tbody tr:nth-child(even) { background: #fafafa; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    /* ─── Totals ─── */
    .totals-section { display: table; width: 100%; margin-top: 0; }
    .totals-spacer  { display: table-cell; width: 55%; }
    .totals-box     { display: table-cell; width: 45%; }
    table.totals { width: 100%; border-collapse: collapse; border: 1px solid #f0f0f0; }
    table.totals td { padding: 6px 10px; font-size: 11px; }
    table.totals tr:not(:last-child) td { border-bottom: 1px solid #f0f0f0; }
    table.totals td:first-child { color: #555; }
    table.totals td:last-child  { text-align: right; font-weight: bold; }
    table.totals .grand-row td  { background: #ff9800; color: #fff; font-size: 13px; padding: 8px 10px; }

    /* ─── Terms Section ─── */
    .terms-section { margin-top: 20px; }
    .terms-title   { font-size: 11px; font-weight: bold; color: #ff9800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .terms-row     { display: table; width: 100%; margin-bottom: 6px; }
    .terms-label   { display: table-cell; width: 25%; font-size: 10px; color: #888; font-weight: bold; }
    .terms-value   { display: table-cell; font-size: 10px; color: #333; }

    /* ─── Notes ─── */
    .notes-box { margin-top: 16px; border: 1px solid #f0f0f0; padding: 10px; border-radius: 4px; }
    .notes-box .notes-head { font-weight: bold; color: #ff9800; font-size: 11px; margin-bottom: 5px; }
    .notes-box p { margin: 0; font-size: 10px; color: #555; white-space: pre-wrap; line-height: 1.7; }

    /* ─── Payment Details ─── */
    .payment-section { margin-top: 20px; border: 2px solid #ff9800; padding: 12px; background: #fff8e1; page-break-inside: avoid; }
    .payment-section h3 { margin: 0 0 8px 0; color: #ff9800; font-size: 12px; }
    .payment-grid    { display: table; width: 100%; }
    .bank-details    { display: table-cell; width: 60%; vertical-align: top; padding-right: 15px; }
    .qr-section      { display: table-cell; width: 40%; text-align: center; vertical-align: top; }
    .qr-section img  { width: 130px; height: 130px; }
    table.bank-table { border: none; margin-top: 5px; }
    table.bank-table td { border: none; padding: 3px 8px 3px 0; font-size: 10px; }
    table.bank-table td:first-child { color: #888; font-weight: bold; width: 45%; }

    /* ─── Footer ─── */
    .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
  </style>
</head>
<body>

  {{-- ══════════ HEADER ══════════ --}}
  <div class="header-section">
    <div class="company-block">
      <img src="{{ public_path('image/logo.png') }}" alt="Logo" width="150" height="150">
      <div class="co-name">Crewrent Enterprises</div>
      <div class="co-addr">
        The Avenue, IA Project Rd, Chimatpada,<br>
        Marol, Andheri East, Mumbai, Maharashtra – 400059<br>
        Ph: +91-9324465314 | Email: info@crewrent.in
      </div>
    </div>
    <div class="title-block">
      <h1>QUOTATION</h1>
      <div class="code">{{ $monthlyQuotation->code }}</div>
      <div class="meta">Date: {{ $monthlyQuotation->created_at->format('d M Y') }}</div>
      <div class="meta">Valid Till: {{ $monthlyQuotation->valid_till }}</div>
    </div>
  </div>
  <hr class="divider">

  {{-- ══════════ BILL TO + QUOTATION META ══════════ --}}
  <div class="bill-section">
    <div class="bill-to">
      <h3>Bill To</h3>
      <p>{!! $monthlyQuotation->bill_to ?: e($monthlyQuotation->client_name) !!}</p>
    </div>
    <div class="validity-box">
      <table class="validity-table">
        @if($monthlyQuotation->payment_terms)
        <tr>
          <td>Payment Terms:</td>
          <td>{{ $monthlyQuotation->payment_terms }}</td>
        </tr>
        @endif
        @if($monthlyQuotation->delivery_timeline)
        <tr>
          <td>Delivery:</td>
          <td>{{ $monthlyQuotation->delivery_timeline }}</td>
        </tr>
        @endif
        <tr>
          <td>Validity:</td>
          <td>{{ $monthlyQuotation->validity_days }} Days</td>
        </tr>
        <tr>
          <td>Valid Till:</td>
          <td>{{ $monthlyQuotation->valid_till }}</td>
        </tr>
        <tr>
          <td>Subject:</td>
          <td>Rate Contract — Monthly Walkie Rental</td>
        </tr>
      </table>
    </div>
  </div>

  {{-- ══════════ ITEMS TABLE ══════════ --}}
  <table class="items">
    <thead>
      <tr>
        <th style="width:30px;">S.No</th>
        <th>Description</th>
        <th class="text-center" style="width:50px;">UOM</th>
        <th class="text-right" style="width:50px;">Qty</th>
        <th class="text-right" style="width:90px;">Rate / Month</th>
        <th class="text-right" style="width:90px;">Amount / Month</th>
      </tr>
    </thead>
    <tbody>
      @foreach($monthlyQuotation->items as $i => $item)
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>
          <strong>{{ $item->item_name }}</strong>
          @if($item->description)
            <br><span style="color:#777; font-size:10px;">{{ $item->description }}</span>
          @endif
        </td>
        <td class="text-center">{{ $item->uom }}</td>
        <td class="text-right">{{ $item->quantity }}</td>
        <td class="text-right">₹{{ number_format($item->rate, 2) }}</td>
        <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- ══════════ TOTALS ══════════ --}}
  <div class="totals-section">
    <div class="totals-spacer"></div>
    <div class="totals-box">
      <table class="totals">
        <tr>
          <td>Subtotal</td>
          <td>₹{{ number_format($monthlyQuotation->subtotal, 2) }}</td>
        </tr>
        @if($monthlyQuotation->gst_percent > 0)
        <tr>
          <td>GST ({{ $monthlyQuotation->gst_percent }}%)</td>
          <td>₹{{ number_format($monthlyQuotation->gst_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-row">
          <td><strong>Total / Month</strong></td>
          <td><strong>₹{{ number_format($monthlyQuotation->total_amount, 2) }}</strong></td>
        </tr>
      </table>
    </div>
  </div>

  {{-- ══════════ NOTES / T&C ══════════ --}}
  @if($monthlyQuotation->notes)
  <div class="notes-box">
    <div class="notes-head">Notes &amp; Terms &amp; Conditions:</div>
    <p>{{ $monthlyQuotation->notes }}</p>
  </div>
  @endif

  <div class="footer">
    This is a computer generated quotation by Crewrent Enterprises. | {{ $monthlyQuotation->code }}
  </div>

</body>
</html>
