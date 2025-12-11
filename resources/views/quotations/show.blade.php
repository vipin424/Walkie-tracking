@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Quotation: {{ $quotation->code }}</h3>

    <div class="mb-3">
        <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-warning">Edit</a>

        <form action="{{ route('quotations.generatePdf', $quotation) }}" method="POST" style="display:inline">
            @csrf
            <button class="btn btn-primary">Generate PDF</button>
        </form>

        <!-- Send Email form -->
        <button class="btn btn-success" data-toggle="modal" data-target="#emailModal">Send Email</button>

        <!-- Send WhatsApp form -->
        <button class="btn btn-info" data-toggle="modal" data-target="#waModal">Send WhatsApp</button>

        @if($quotation->pdf_path)
            <a href="{{ Storage::url(str_replace('storage/','public/',$quotation->pdf_path)) }}" target="_blank" class="btn btn-outline-secondary">View PDF</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Client</h5>
            <p>{{ $quotation->client_name }} <br> {{ $quotation->client_phone }} <br> {{ $quotation->client_email }}</p>

            <h5>Items</h5>
            <table class="table">
                <thead><tr><th>#</th><th>Item</th><th>Type</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($quotation->items as $i => $it)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $it->item_name }} <div class="text-muted">{{ $it->description }}</div></td>
                        <td>{{ $it->item_type }}</td>
                        <td>{{ $it->quantity }}</td>
                        <td>₹{{ number_format($it->unit_price,2) }}</td>
                        <td>₹{{ number_format($it->total_price,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-right">
                <div>Subtotal: ₹{{ number_format($quotation->subtotal,2) }}</div>
                <div>Tax: ₹{{ number_format($quotation->tax_amount,2) }}</div>
                <div>Discount: ₹{{ number_format($quotation->discount_amount,2) }}</div>
                <h4>Total: ₹{{ number_format($quotation->total_amount,2) }}</h4>
            </div>

            <h5>Notes</h5>
            <p>{{ $quotation->notes }}</p>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1">
      <div class="modal-dialog">
        <form action="{{ route('quotations.sendEmail', $quotation) }}" method="POST">
            @csrf
            <div class="modal-content">
              <div class="modal-header"><h5>Send Email</h5></div>
              <div class="modal-body">
                <div class="form-group">
                    <label>To Email</label>
                    <input name="to_email" class="form-control" value="{{ $quotation->client_email }}">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control">Hello {{ $quotation->client_name }}, Please find attached the quotation {{ $quotation->code }}.</textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                <button class="btn btn-success">Send</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <!-- WhatsApp Modal -->
    <div class="modal fade" id="waModal" tabindex="-1">
      <div class="modal-dialog">
        <form action="{{ route('quotations.sendWhatsapp', $quotation) }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content">
              <div class="modal-header"><h5>Send WhatsApp</h5></div>
              <div class="modal-body">
                <div class="form-group">
                    <label>To Phone</label>
                    <input name="to_phone" class="form-control" value="{{ $quotation->client_phone }}">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control">Hello {{ $quotation->client_name }}, Here is the quotation {{ $quotation->code }}. Please download from the link and reply to confirm.</textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                <button class="btn btn-info">Open WhatsApp</button>
              </div>
            </div>
        </form>
      </div>
    </div>

</div>
@endsection
