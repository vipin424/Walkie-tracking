<div class="modal fade" id="sendAgreementMailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('orders.sendAgreementEmail', $order) }}">
      @csrf

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            📧 Send Agreement Email
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          {{-- TO --}}
          <div class="mb-3">
            <label class="form-label">To (Client Email)</label>
            <input type="email"
                   name="to_email"
                   class="form-control"
                   value="{{ $order->client_email ?? '' }}"
                   required>
          </div>

          {{-- CC --}}
          <div class="mb-3">
            <label class="form-label">
              CC Emails <small class="text-muted">(comma separated)</small>
            </label>
            <input type="text"
                   name="cc_emails"
                   class="form-control"
                   placeholder="manager@company.com, accounts@company.com">
          </div>

          {{-- MESSAGE --}}
          <div class="mb-3">
            <label class="form-label">Message (optional)</label>
            <textarea name="message"
                      class="form-control"
                      rows="3"
                      placeholder="Additional note for recipients..."></textarea>
          </div>

          <div class="alert alert-info small mb-0">
            Agreement link will be included automatically.
          </div>

        </div>

        <div class="modal-footer">
          <button type="button"
                  class="btn btn-outline-secondary"
                  data-bs-dismiss="modal">
            Cancel
          </button>

          <button type="submit" class="btn btn-success">
            <i class="bi bi-send me-1"></i>Send Email
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
