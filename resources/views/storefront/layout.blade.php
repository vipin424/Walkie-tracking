<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Cameras, Drones & More | CrewRent</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    
    <!-- Flatpickr for Date Selection -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    @yield('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar-container">
            <!-- Left: Logo -->
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="logo-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                CrewRent
            </a>
            
            <!-- Center: Date Picker (SharePal Style) -->
            <div class="header-date-picker d-none d-md-flex">
                <div class="location-picker">
                    <i class="bi bi-geo-alt"></i>
                    <span>Mumbai</span>
                    <i class="bi bi-chevron-down" style="font-size: 0.7rem; margin-left: 5px;"></i>
                </div>
                <div class="date-divider"></div>
                <div class="date-input-group">
                    <i class="bi bi-calendar-event"></i>
                    <input type="text" id="rentalStartDate" placeholder="Rental Start">
                </div>
                <div class="date-input-group">
                    <i class="bi bi-calendar-check"></i>
                    <input type="text" id="rentalEndDate" placeholder="Rental End">
                </div>
                <button class="btn btn-edit-date" id="dateActionBtn">
                    Select
                </button>
            </div>

            <!-- Right: Actions -->
            <div class="nav-actions">
                <button class="btn-action-icon">
                    <i class="bi bi-search"></i>
                </button>
                
                <button class="btn-action-icon position-relative" id="cartBtn">
                    <i class="bi bi-cart3"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </button>
                
                <button class="btn-login">
                    <i class="bi bi-person-circle"></i> <span>Hi, Login</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Floating Actions -->
    <div class="floating-actions">
        <a href="tel:+919876543210" class="float-btn float-call">
            <i class="bi bi-telephone-fill"></i>
        </a>
        <a href="https://wa.me/919876543210" class="float-btn float-whatsapp">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <!-- Footer -->
    <footer style="background: var(--dark); color: var(--white); padding: 4rem 0 2rem; margin-top: 4rem;">
        <div class="container">
            <div class="grid grid-cols-4 gap-8 mb-12">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1rem;">CrewRent.</h3>
                    <p style="color: var(--dark-muted); font-size: 0.9rem;">India's leading tech rental platform. Rent Cameras, Drones, Consoles, and more.</p>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 1rem;">Categories</h4>
                    <div class="flex flex-col gap-2" style="color: var(--dark-muted); font-size: 0.9rem;">
                        <a href="#">Cameras</a>
                        <a href="#">Drones</a>
                        <a href="#">Gaming</a>
                        <a href="#">Audio</a>
                    </div>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 1rem;">Company</h4>
                    <div class="flex flex-col gap-2" style="color: var(--dark-muted); font-size: 0.9rem;">
                        <a href="#">About Us</a>
                        <a href="#">Contact</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                </div>
            </div>
            <div style="border-top: 1px solid var(--dark-muted); padding-top: 2rem; text-align: center; color: var(--dark-muted); font-size: 0.85rem;">
                &copy; {{ date('Y') }} CrewRent Enterprises. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/storefront.js') }}"></script>
    <script>
        // Init Datepicker (Event Start/End)
        flatpickr("#rentalStartDate", {
            minDate: "today",
            dateFormat: "d M Y",
        });
        flatpickr("#rentalEndDate", {
            minDate: "today",
            dateFormat: "d M Y",
        });
    </script>
    @yield('scripts')
</body>
</html>
