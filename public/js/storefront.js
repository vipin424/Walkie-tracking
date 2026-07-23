document.addEventListener('DOMContentLoaded', function() {
    
    const rentalStartDateInput = document.getElementById('rentalStartDate');
    const rentalEndDateInput = document.getElementById('rentalEndDate');
    const rentPrices = document.querySelectorAll('.dynamic-price');
    
    const dateActionBtn = document.getElementById('dateActionBtn');
    
    // Default 1 day rent
    let totalDays = 1;

    // Load dates from localStorage if they exist
    if (rentalStartDateInput && rentalEndDateInput) {
        const savedStart = localStorage.getItem('crewrent_start_date');
        const savedEnd = localStorage.getItem('crewrent_end_date');
        
        if (savedStart) rentalStartDateInput.value = savedStart;
        if (savedEnd) rentalEndDateInput.value = savedEnd;
    }

    function checkDateStatus() {
        if (rentalStartDateInput && rentalEndDateInput) {
            if (rentalStartDateInput.value && rentalEndDateInput.value) {
                if (dateActionBtn) dateActionBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Selected';
                // Save to local storage for persistence across pages
                localStorage.setItem('crewrent_start_date', rentalStartDateInput.value);
                localStorage.setItem('crewrent_end_date', rentalEndDateInput.value);
            } else {
                if (dateActionBtn) dateActionBtn.innerHTML = 'Select';
            }
        }
    }

    function calculateDays() {
        if (!rentalStartDateInput.value || !rentalEndDateInput.value) return 1;

        const start = new Date(rentalStartDateInput.value);
        const end = new Date(rentalEndDateInput.value);

        // Calculate difference in time
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Minimum 1 day rent even for same day start/end
        // +1 because if start = 15th, end = 16th, that's 2 days of rent.
        return diffDays + 1; 
    }

    function updatePrices() {
        checkDateStatus();
        totalDays = calculateDays();
        
        rentPrices.forEach(element => {
            const basePrice = parseFloat(element.dataset.basePrice);
            const newPrice = basePrice * totalDays;
            
            // Format number to IN locale
            element.innerHTML = '₹' + newPrice.toLocaleString('en-IN') + 
                ' <span class="price-duration" style="font-size: 0.85rem; font-weight: 500; color: var(--muted-foreground);">/ ' + totalDays + ' day' + (totalDays > 1 ? 's' : '') + '</span>';
        });
    }

    // Flatpickr instances are initialized in the blade layout.
    // We can listen to change events on the hidden inputs Flatpickr creates.
    if(rentalStartDateInput && rentalEndDateInput) {
        rentalStartDateInput.addEventListener('change', updatePrices);
        rentalEndDateInput.addEventListener('change', updatePrices);
        checkDateStatus();
    }
    
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
    
});
