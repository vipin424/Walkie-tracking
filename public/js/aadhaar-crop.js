let cropper;
let activeInput = null;

document.querySelectorAll('.aadhaar-input').forEach(input => {
    input.addEventListener('change', function (e) {

        const file = e.target.files[0];
        if (!file) return;

        activeInput = input;

        const reader = new FileReader();
        reader.onload = function (event) {
            const image = document.getElementById('cropperImage');
            image.src = event.target.result;

            const modal = new bootstrap.Modal(document.getElementById('cropModal'));
            modal.show();

            if (cropper) cropper.destroy();

            cropper = new Cropper(image, {
                    viewMode: 1,              // 👈 VERY IMPORTANT
                    dragMode: 'move',
                    autoCropArea: 1,          // 👈 full image covered
                    responsive: true,
                    background: false,
                    modal: true,
                    guides: true,
                    center: true,
                    highlight: false,
                    zoomOnWheel: true,
            });
        };
        reader.readAsDataURL(file);
    });
});

document.getElementById('cropConfirmBtn').addEventListener('click', function () {

    cropper.getCroppedCanvas({
        width: 1200,
        height: 800,
        imageSmoothingQuality: 'high'
    }).toBlob(blob => {

        const file = new File([blob], 'aadhaar.jpg', { type: 'image/jpeg' });

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        activeInput.files = dataTransfer.files;

        bootstrap.Modal.getInstance(document.getElementById('cropModal')).hide();
    }, 'image/jpeg', 0.9);
});
