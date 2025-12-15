<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="emailSendForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Quotation via Email</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Recipient Email</label>
                        <input type="email" name="to_email" id="emailField" class="form-control"
                               placeholder="Enter client email" required>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" id="emailMessageField" class="form-control" rows="4">
Hello, 
Please find attached the quotation for your request.
                        </textarea>
                    </div>

                    <p class="text-warning small">
                        * You must save the quotation before sending by Email.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="emailSendBtn" class="btn btn-success">Send Email</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="whatsappSendForm" target="_blank">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send WhatsApp Message</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Client WhatsApp Number</label>
                        <input type="text" name="to_phone" id="waPhoneField" class="form-control"
                               placeholder="e.g. 9876543210" required>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" id="waMessageField" class="form-control" rows="4">
Hello, 
Here is your quotation. Please download from the link below and confirm.
                        </textarea>
                    </div>

                    <p class="text-warning small">
                        * You must save the quotation before sending by WhatsApp.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="whatsappSendBtn" class="btn btn-success">Send WhatsApp</button>
                </div>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

    // When clicking "Send Email" from create/edit page
    document.getElementById('emailSendBtn').addEventListener('click', function(){
        alert("Please SAVE the quotation first. After saving, the Email will be sent from the Quotation View page.");
    });

    // When clicking "Send WhatsApp" from create/edit page
    document.getElementById('whatsappSendBtn').addEventListener('click', function(){
        alert("Please SAVE the quotation first. After saving, the WhatsApp link will be generated.");
    });

});
</script>
@endpush
