<div class="modal fade" id="convertModal">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('quotations.convertToOrder', $quotation) }}">

        @csrf

        <div class="modal-content">

            <div class="modal-header">
                <h5>Convert Quotation to Order</h5>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label>Total Amount</label>
                    <input type="text"
                           class="form-control"
                           value="₹{{ number_format($quotation->total_amount,2) }}"
                           disabled>
                </div>

                <div class="mb-3">
                    <label>Advance Paid *</label>
                    <input type="number"
                           name="advance_paid"
                           class="form-control"
                           required
                           min="0"
                           max="{{ $quotation->total_amount }}">
                </div>

                <small class="text-muted">
                    Balance will be calculated automatically.
                </small>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success">
                    Confirm & Create Order
                </button>
            </div>

        </div>
    </form>
  </div>
</div>
