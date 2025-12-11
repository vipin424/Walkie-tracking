@php
    $items = old('items') ?: ($quotation ? $quotation->items->toArray() : []);
@endphp

<div class="card p-3">
    <div class="row">
        <div class="col-md-6">
            <label>Client Name</label>
            <input name="client_name" class="form-control" value="{{ old('client_name', $quotation->client_name ?? '') }}" required>
        </div>
        <div class="col-md-3">
            <label>Client Email</label>
            <input name="client_email" class="form-control" value="{{ old('client_email', $quotation->client_email ?? '') }}">
        </div>
        <div class="col-md-3">
            <label>Client Phone</label>
            <input name="client_phone" class="form-control" value="{{ old('client_phone', $quotation->client_phone ?? '') }}">
        </div>
    </div>

    <div class="mt-3">
        <label>Event From</label>
        <input type="date" name="event_from" class="form-control" value="{{ old('event_from', isset($quotation->event_from) ? $quotation->event_from->format('Y-m-d') : '') }}">
    </div>

    <div class="mt-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control">{{ old('notes', $quotation->notes ?? '') }}</textarea>
    </div>

    <hr>

    <h5>Items</h5>
    <table class="table table-sm" id="items-table">
        <thead>
            <tr>
                <th>Item</th><th>Type</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Tax %</th><th>Total</th><th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($items))
                @foreach($items as $i => $it)
                <tr class="item-row">
                    <td><input name="items[{{ $i }}][item_name]" class="form-control item-name" value="{{ $it['item_name'] ?? '' }}"></td>
                    <td><input name="items[{{ $i }}][item_type]" class="form-control" value="{{ $it['item_type'] ?? '' }}"></td>
                    <td><input name="items[{{ $i }}][description]" class="form-control" value="{{ $it['description'] ?? '' }}"></td>
                    <td><input name="items[{{ $i }}][quantity]" class="form-control qty" value="{{ $it['quantity'] ?? 1 }}"></td>
                    <td><input name="items[{{ $i }}][unit_price]" class="form-control unit" value="{{ $it['unit_price'] ?? 0 }}"></td>
                    <td><input name="items[{{ $i }}][tax_percent]" class="form-control tax" value="{{ $it['tax_percent'] ?? 0 }}"></td>
                    <td class="line_total text-right">0.00</td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
                </tr>
                @endforeach
            @else
                <tr class="item-row">
                    <td><input name="items[0][item_name]" class="form-control item-name"></td>
                    <td><input name="items[0][item_type]" class="form-control"></td>
                    <td><input name="items[0][description]" class="form-control"></td>
                    <td><input name="items[0][quantity]" class="form-control qty" value="1"></td>
                    <td><input name="items[0][unit_price]" class="form-control unit" value="0"></td>
                    <td><input name="items[0][tax_percent]" class="form-control tax" value="0"></td>
                    <td class="line_total text-right">0.00</td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
                </tr>
            @endif
        </tbody>
    </table>

    <button type="button" id="add-row" class="btn btn-sm btn-success">Add Item</button>

    <div class="row mt-3">
        <div class="col-md-6 offset-md-6">
            <div class="d-flex justify-content-between">
                <div>Subtotal:</div>
                <div>₹ <span id="subtotal">0.00</span></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Tax (calculated):</div>
                <div>₹ <span id="tax_amount">0.00</span></div>
            </div>
            <div class="d-flex justify-content-between">
                <div>Discount:</div>
                <div><input type="number" step="0.01" name="discount_amount" id="discount" class="form-control" value="{{ old('discount_amount', $quotation->discount_amount ?? 0) }}"></div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <div><strong>Total</strong></div>
                <div><strong>₹ <span id="total_amount">0.00</span></strong></div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="button" id="generate-pdf" class="btn btn-outline-primary">Generate PDF</button>
        <button type="button" id="send-email" class="btn btn-outline-success">Send Email</button>
        <button type="button" id="send-whatsapp" class="btn btn-outline-info">Send WhatsApp</button>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // add logic to calculate totals, add/remove rows, and handle generate/send actions
    function recalcRow(row){
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const unit = parseFloat(row.querySelector('.unit').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const total = qty * unit;
        const taxAmount = total * (tax/100);
        row.querySelector('.line_total').innerText = (total + taxAmount).toFixed(2);
    }

    function recalcAll(){
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(r=>{
            recalcRow(r);
            subtotal += parseFloat(r.querySelector('.line_total').innerText) || 0;
        });
        const discount = parseFloat(document.querySelector('#discount').value) || 0;
        // tax_amount here is sum of line-level tax already counted in subtotal; show 0 for clarity or compute separately if needed
        const tax_amount = 0; // line tax included in line_total
        const total = subtotal - discount;
        document.querySelector('#subtotal').innerText = subtotal.toFixed(2);
        document.querySelector('#tax_amount').innerText = tax_amount.toFixed(2);
        document.querySelector('#total_amount').innerText = total.toFixed(2);
    }

    // initial calc
    recalcAll();

    // live update events
    document.addEventListener('input', function(e){
        if (e.target.matches('.qty') || e.target.matches('.unit') || e.target.matches('.tax') || e.target.matches('#discount')) {
            recalcAll();
        }
    });

    // add row
    document.getElementById('add-row').addEventListener('click', function(){
        const tbody = document.querySelector('#items-table tbody');
        const index = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.classList.add('item-row');
        tr.innerHTML = `
            <td><input name="items[${index}][item_name]" class="form-control item-name"></td>
            <td><input name="items[${index}][item_type]" class="form-control"></td>
            <td><input name="items[${index}][description]" class="form-control"></td>
            <td><input name="items[${index}][quantity]" class="form-control qty" value="1"></td>
            <td><input name="items[${index}][unit_price]" class="form-control unit" value="0"></td>
            <td><input name="items[${index}][tax_percent]" class="form-control tax" value="0"></td>
            <td class="line_total text-right">0.00</td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
        `;
        tbody.appendChild(tr);
        recalcAll();
    });

    // remove row
    document.addEventListener('click', function(e){
        if (e.target.matches('.remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
            // re-index names
            document.querySelectorAll('#items-table tbody tr').forEach((tr, i) => {
                tr.querySelectorAll('input, textarea').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/items\[\d+\]/, `items[${i}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
            recalcAll();
        }
    });

    // Generate PDF button -> submit form to generate route
    document.getElementById('generate-pdf').addEventListener('click', function(){
        const form = document.getElementById('quotation-form');
        // create a temporary form to post to generate-pdf after saving if necessary
        // We'll first submit the create form normally and then admin can click Generate PDF on show page.
        alert('Please Save the Quotation first. After Save, open the Quotation and click "Generate PDF" there.');
    });

    // Send Email -> open modal (handled in partials modals)
    document.getElementById('send-email').addEventListener('click', function(){
        alert('Please Save the Quotation first. After Save, open the Quotation and click Send Email there.');
    });

    document.getElementById('send-whatsapp').addEventListener('click', function(){
        alert('Please Save the Quotation first. After Save, open the Quotation and click Send WhatsApp there.');
    });
});
</script>
@endpush
