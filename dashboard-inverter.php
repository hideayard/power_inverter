<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geran Komuniti Iskandar Puteri Rendah Karbon 5.0 Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    <link rel="shortcut icon" href="assets/images/favicon.png">

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Load Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Load SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="assets/js/auth.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Theme initialization script to prevent flash - default to light -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme') || 'light';
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                    document.documentElement.classList.remove('light-mode');
                } else {
                    document.documentElement.classList.add('light-mode');
                    document.documentElement.classList.remove('dark-mode');
                }
            } catch (e) {
                console.error('Theme initialization error:', e);
            }
        })();
    </script>
</head>

<body class="min-h-screen p-4 sm:p-8 transition-colors duration-300">
    <!-- Theme Toggle Button -->
    <div class="fixed top-4 left-4 z-50">
        <button id="theme-toggle" class="flex items-center gap-2 px-3 py-2 rounded-full bg-white dark:bg-[#2C5282] border border-[#FDA300] transition-all duration-200 shadow-lg theme-toggle">
            <i id="theme-icon-dark" class="fas fa-moon text-[#FDA300] hidden dark:inline-block"></i>
            <i id="theme-icon-light" class="fas fa-sun text-[#FDA300] light:inline-block hidden"></i>
            <span id="theme-text" class="text-sm font-medium text-[#2C5282] dark:text-white light:text-[#2C5282] hidden md:inline">
                <!-- Text will be updated by JavaScript -->
            </span>
        </button>
    </div>

    <!-- Google-style User Profile Dropdown -->
    <div class="fixed top-4 right-4 z-50">
        <div class="relative">
            <!-- User Avatar Button -->
            <button id="user-menu-button" class="flex items-center gap-2 px-3 py-2 rounded-full bg-white dark:bg-[#2C5282] border border-[#FDA300] transition-all duration-200 shadow-lg">
                <!-- Avatar with initials -->
                <div id="user-avatar" class="w-8 h-8 bg-gradient-to-r from-[#2C5282] to-[#FDA300] rounded-full flex items-center justify-center text-white font-bold text-sm">
                    <!-- Initials will be added by JavaScript -->
                </div>
                <!-- User Name -->
                <span id="user-name" class="text-sm font-medium text-[#2C5282] dark:text-white light:text-[#2C5282] hidden md:inline">Loading...</span>
                <!-- Chevron Icon -->
                <i class="fas fa-chevron-down text-[#2C5282] dark:text-white light:text-[#2C5282] text-xs"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="absolute right-0 mt-2 w-64 rounded-lg shadow-2xl hidden overflow-hidden dropdown-menu">
                <!-- User Info Section -->
                <div class="p-4 border-b border-[#EDF2F7] dark:border-[#315492] dropdown-header">
                    <div class="flex items-center gap-3">
                        <div id="dropdown-avatar" class="w-10 h-10 bg-gradient-to-r from-[#2C5282] to-[#FDA300] rounded-full flex items-center justify-center text-white font-bold">
                            <!-- Initials will be added by JavaScript -->
                        </div>
                        <div>
                            <h3 id="dropdown-name" class="font-semibold text-[#2C5282] dark:text-white light:text-[#2C5282]">Loading...</h3>
                            <p id="dropdown-email" class="text-sm text-[#315492] dark:text-[#EDF2F7] light:text-[#315492] truncate">user@example.com</p>
                            <p id="dropdown-role" class="text-xs text-[#FDA300] font-medium mt-1">Role: Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2 dropdown-menu-items">
                    <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-[#2C5282] dark:text-white light:text-[#2C5282] hover:bg-[#FFF6E1] dark:hover:bg-[#315492] transition-colors">
                        <i class="fas fa-user w-5 text-center text-[#2C5282] dark:text-white light:text-[#2C5282]"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="settings.php" class="flex items-center gap-3 px-4 py-3 text-[#2C5282] dark:text-white light:text-[#2C5282] hover:bg-[#FFF6E1] dark:hover:bg-[#315492] transition-colors">
                        <i class="fas fa-cog w-5 text-center text-[#2C5282] dark:text-white light:text-[#2C5282]"></i>
                        <span>Settings</span>
                    </a>
                    <a href="help.php" class="flex items-center gap-3 px-4 py-3 text-[#2C5282] dark:text-white light:text-[#2C5282] hover:bg-[#FFF6E1] dark:hover:bg-[#315492] transition-colors">
                        <i class="fas fa-question-circle w-5 text-center text-[#2C5282] dark:text-white light:text-[#2C5282]"></i>
                        <span>Help & Support</span>
                    </a>
                    <div class="border-t border-[#EDF2F7] dark:border-[#315492] my-2"></div>
                    <button id="logout-button" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 w-full text-left transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center text-red-500"></i>
                        <span>Sign Out</span>
                    </button>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-[#EDF2F7] dark:border-[#315492] bg-[#FFF6E1] dark:bg-[#1A2B3E] dropdown-footer">
                    <p class="text-xs text-[#2C5282] dark:text-[#EDF2F7]">© 2024 Energy Monitoring System</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Header & Status Bar -->
        <header class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <div class="w-full">
                    <h1 class="text-3xl md:text-4xl font-light tracking-tight text-center sm:text-left border-b border-[#FDA300] pb-4 page-title">
                        PROJEK PASAR MALAM HIJAU <span class="font-semibold text-[#FDA300]">DIKUASAKAN POWER INVERTER 2.0</span>
                    </h1>

                    <p class="text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] text-center sm:text-left mt-2 text-sm uppercase tracking-wider">
                        <i class="fas fa-leaf text-[#FDA300] mr-2"></i>dibiyai oleh Geran Komuniti Iskandar Puteri Rendah Karbon 5.0
                    </p>
                    
                    <!-- Header image container - max height 600px, full image visible -->
                    <div class="mt-6 w-full">
                        <div class="relative w-full rounded-xl overflow-hidden shadow-2xl header-image-container" style="max-height: 600px;">
                            <img src="assets/images/pasarmalamhijau.png" alt="Pasar Malam Hijau" class="w-full h-auto object-contain">
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Bar -->
            <div id="status-bar" class="mt-6 flex flex-col sm:flex-row justify-between text-sm p-4 rounded-xl status-bar">
                <p id="auth-status" class="text-[#2C5282] dark:text-[#EDF2F7] font-semibold flex items-center gap-2">
                    <i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]"></span>
                </p>
                <p id="user-id-display" class="text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] truncate mt-1 sm:mt-0 flex items-center gap-2">
                    <i class="fas fa-microchip text-[#FDA300]"></i>Active Nodes: <span id="active-nodes-count" class="font-semibold">5</span>
                </p>
                <p id="last-updated" class="text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] mt-1 sm:mt-0 flex items-center gap-2">
                    <i class="fas fa-clock text-[#FDA300]"></i>Last Updated: <span id="last-updated-time">7:41:05 PM</span>
                </p>
            </div>
        </header>

        <!-- Business Metrics Section -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <!-- Total CO₂ Reduction -->
            <div class="md:col-span-2 metric-card p-6 rounded-2xl" id="total-carbon-card">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-medium text-[#2C5282] dark:text-white light:text-[#2C5282]">Total CO₂ Reduction</h2>
                    <i class="fas fa-leaf text-[#FDA300] text-2xl"></i>
                </div>
                <div class="text-5xl md:text-6xl font-light tracking-tight text-[#2C5282] dark:text-white light:text-[#2C5282] mt-2">
                    8.212<span class="text-2xl text-[#FDA300] ml-2">kg</span>
                </div>
                <p class="text-sm text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] mt-2 flex items-center gap-1">
                    <i class="fas fa-arrow-down text-[#FDA300]"></i>
                    <span>12.3% reduction from last month</span>
                </p>
            </div>

            <!-- CO₂ Reduction Card -->
            <div class="metric-card p-6 rounded-2xl" id="carbon-card">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-medium text-[#2C5282] dark:text-white light:text-[#2C5282]">CO₂ Reduction</h2>
                    <i class="fas fa-chart-line text-[#FDA300] text-xl"></i>
                </div>
                <div class="text-3xl md:text-4xl font-light tracking-tight text-[#2C5282] dark:text-white light:text-[#2C5282]" id="carbon-reduction-value">
                    3,657.85<span class="text-lg text-[#FDA300] ml-1">kg</span>
                </div>
                <p class="text-xs text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] mt-2">Lifetime savings</p>
            </div>

        </section>

        <!-- Two Column Layout for Map and Table -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- GPS Location (Map View) -->
            <div id="map-container" class="content-card p-6 rounded-2xl relative">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-medium text-[#2C5282] dark:text-white light:text-[#2C5282]">
                        <i class="fas fa-map-marker-alt text-[#FDA300] mr-2"></i>
                        Inverter Locations (Johor, Malaysia)
                    </h2>
                    <button id="map-toggle" class="map-toggle-btn">
                        <svg id="maximize-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5h-4m4 0v-4m0 4l-5-5"></path>
                        </svg>
                        <svg id="minimize-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span id="map-toggle-text">Maximize</span>
                    </button>
                </div>
                <div id="map" class="w-full rounded-xl overflow-hidden"></div>
                <p class="text-sm text-[#2C5282] dark:text-[#EDF2F7] light:text-[#2C5282] mt-3 flex items-center gap-2" id="coordinates-text">
                    <i class="fas fa-info-circle text-[#FDA300]"></i>
                    Click on any inverter marker to view details
                </p>
            </div>

            <!-- Nodes Table -->
            <div class="content-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-medium text-[#2C5282] dark:text-white light:text-[#2C5282]">
                        <i class="fas fa-microchip text-[#FDA300] mr-2"></i>
                        All Nodes Overview
                    </h2>
                    <div class="px-3 py-1 bg-[#FFF6E1] dark:bg-[#315492] rounded-full text-sm text-[#2C5282] dark:text-white">
                        <span id="total-nodes">5</span> Active
                    </div>
                </div>
                <div class="node-table-container">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-lg">ID</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Power (W)</th>
                                <th class="px-4 py-3">Voltage (V)</th>
                                <th class="px-4 py-3 rounded-tr-lg">Current (A)</th>
                            </tr>
                        </thead>
                        <tbody id="nodes-table-body">
                            <!-- Table rows will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Function to update theme toggle text
        function updateThemeToggleText() {
            const themeText = document.getElementById('theme-text');
            const htmlElement = document.documentElement;
            
            if (htmlElement.classList.contains('dark-mode')) {
                themeText.textContent = 'Dark Mode';
            } else {
                themeText.textContent = 'Light Mode';
            }
        }

        // Theme Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;
            
            // Set initial theme based on localStorage (defaults to light)
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                htmlElement.classList.add('dark-mode');
                htmlElement.classList.remove('light-mode');
            } else {
                htmlElement.classList.add('light-mode');
                htmlElement.classList.remove('dark-mode');
            }
            
            // Update toggle text
            updateThemeToggleText();

            // Theme toggle click handler
            themeToggle.addEventListener('click', function() {
                if (htmlElement.classList.contains('dark-mode')) {
                    // Switch to light mode
                    htmlElement.classList.remove('dark-mode');
                    htmlElement.classList.add('light-mode');
                    localStorage.setItem('theme', 'light');
                } else {
                    // Switch to dark mode
                    htmlElement.classList.remove('light-mode');
                    htmlElement.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                }
                
                // Update toggle text
                updateThemeToggleText();
                
                // Update map tiles if map exists
                if (window.updateMapTheme) {
                    window.updateMapTheme();
                }
            });

            // User Profile Management
            const user = JSON.parse(localStorage.getItem("user") || "{}");
            const userName = user.name || user.username || "User";
            const userEmail = user.user_email || "user@example.com";
            const userRole = user.user_tipe || "ADMIN";

            // Generate initials from name
            function getInitials(name) {
                return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
            }

            const initials = getInitials(userName);

            // Update UI elements
            document.getElementById('user-avatar').textContent = initials;
            document.getElementById('dropdown-avatar').textContent = initials;
            document.getElementById('user-name').textContent = userName;
            document.getElementById('dropdown-name').textContent = userName;
            document.getElementById('dropdown-email').textContent = userEmail;
            document.getElementById('dropdown-role').textContent = `Role: ${userRole}`;
            document.getElementById('current-role').textContent = userRole;

            // Toggle dropdown menu
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');

            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Logout functionality
            document.getElementById('logout-button').addEventListener('click', function() {
                Swal.fire({
                    title: 'Sign Out?',
                    text: 'Are you sure you want to sign out?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FDA300',
                    cancelButtonColor: '#2C5282',
                    confirmButtonText: 'Yes, sign out',
                    cancelButtonText: 'Cancel',
                    background: document.documentElement.classList.contains('light-mode') ? '#FFFFFF' : '#2C3E50',
                    color: document.documentElement.classList.contains('light-mode') ? '#2C5282' : '#EDF2F7'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: "info",
                            title: "Signing Out",
                            timer: 1500,
                            showConfirmButton: false,
                            background: document.documentElement.classList.contains('light-mode') ? '#FFFFFF' : '#2C3E50',
                            color: document.documentElement.classList.contains('light-mode') ? '#2C5282' : '#EDF2F7'
                        });

                        setTimeout(() => {
                            localStorage.clear();
                            window.location.href = "/auth/login.php";
                        }, 1500);
                    }
                });
            });
        });

        // Auth check
        (function() {
            try {
                const token = localStorage.getItem("jwt");
                const user = localStorage.getItem("user");

                if (!token || !user) {
                    console.log("Not authenticated, redirecting to login");
                    window.location.href = "/auth/login.php";
                    return;
                }
            } catch (error) {
                console.error("Auth check error:", error);
                window.location.href = "/auth/login.php";
            }
        })();
    </script>

    <script src="assets/js/dashboard3.js"></script>
</body>

</html>