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

</head>

<body class="bg-gray-900 text-gray-100 min-h-screen p-4 sm:p-8">

    <!-- Google-style User Profile Dropdown -->
    <div class="fixed top-4 right-4 z-50">
        <div class="relative">
            <!-- User Avatar Button -->
            <button id="user-menu-button" class="flex items-center gap-2 px-3 py-2 rounded-full bg-gray-800 hover:bg-gray-700 border border-gray-700 transition-all duration-200 shadow-lg">
                <!-- Avatar with initials -->
                <div id="user-avatar" class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    <!-- Initials will be added by JavaScript -->
                </div>
                <!-- User Name -->
                <span id="user-name" class="text-sm font-medium text-gray-200 hidden md:inline">Loading...</span>
                <!-- Chevron Icon -->
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="absolute right-0 mt-2 w-64 bg-gray-800 border border-gray-700 rounded-lg shadow-2xl hidden overflow-hidden">
                <!-- User Info Section -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center gap-3">
                        <div id="dropdown-avatar" class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            <!-- Initials will be added by JavaScript -->
                        </div>
                        <div>
                            <h3 id="dropdown-name" class="font-semibold text-gray-100">Loading...</h3>
                            <p id="dropdown-email" class="text-sm text-gray-400 truncate"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5a36353b3e33343d1a3f223b372a363f74393537">[email&#160;protected]</a></p>
                            <p id="dropdown-role" class="text-xs text-blue-400 font-medium mt-1">Role: Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                    <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition-colors">
                        <i class="fas fa-user w-5 text-center"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="settings.php" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition-colors">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span>Settings</span>
                    </a>
                    <a href="help.php" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition-colors">
                        <i class="fas fa-question-circle w-5 text-center"></i>
                        <span>Help & Support</span>
                    </a>
                    <div class="border-t border-gray-700 my-2"></div>
                    <button id="logout-button" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-900/20 w-full text-left transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Sign Out</span>
                    </button>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-700 bg-gray-900/50">
                    <p class="text-xs text-gray-500">© 2024 Energy Monitoring System</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Header & Status Bar -->
        <header class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-white text-center sm:text-left border-b border-gray-700 pb-3">
                        PROJEK PASAR MALAM HIJAU DIKUASAKAN POWER INVERTER 2.0
                    </h1>
                    <p class="text-gray-400 text-center sm:text-left mt-2">
                        dibiyai oleh Geran Komuniti Iskandar Puteri Rendah Karbon 5.0
                    </p>
                </div>
            </div>
            <div id="status-bar" class="mt-4 flex flex-col sm:flex-row justify-between text-sm p-3 bg-gray-800 rounded-lg border border-gray-700">
                <p id="auth-status" class="text-green-400 font-semibold">
                    <i class="fas fa-shield-alt mr-2"></i>Connected as: <span id="current-role"></span>
                </p>
                <p id="user-id-display" class="text-gray-500 truncate mt-1 sm:mt-0">
                    <i class="fas fa-microchip mr-2"></i>Active Nodes: <span id="active-nodes-count">5</span>
                </p>
                <p id="last-updated" class="text-gray-400 mt-1 sm:mt-0">
                    <i class="fas fa-clock mr-2"></i>Last Updated: <span id="last-updated-time">7:41:05 PM</span>
                </p>
            </div>
        </header>

        <!-- Business Metrics Section -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <!-- Projected Annual Savings -->
            <div class="md:col-span-2 bg-green-900/40 p-6 rounded-xl shadow-2xl border border-green-700 transition duration-300 hover:shadow-green-500/50">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-green-400">Total CO₂ Reduction</h2>
                    <i class="fas fa-leaf text-green-400 text-xl"></i>
                </div>
                <div class="text-6xl font-extrabold tracking-tight text-white mt-3">8.212</div>
                <p class="text-lg text-green-300 mt-2">
                kg CO₂
                </p>
            </div>

            <div class="metric-card p-6 rounded-xl shadow-lg border-b-4 border-yellow-500 transition duration-300 hover:shadow-yellow-700/50">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-yellow-400">CO₂ Reduction</h2>
                    <i class="fas fa-leaf text-yellow-400 text-xl"></i>
                </div>
                <div class="text-4xl font-bold tracking-tight text-white" id="carbon-reduction-value">3657.85</div>
                <div class="text-lg font-light text-gray-400 mt-1">kg CO₂</div>
            </div>

        </section>

        <!-- Two Column Layout for Map and Table -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- GPS Location (Map View) -->
            <div id="map-container" class="p-6 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 transition duration-300 hover:shadow-lg hover:shadow-purple-500/30">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-purple-400">Inverter Locations (Johor, Malaysia)</h2>
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
                <div id="map" class="w-full rounded-lg overflow-hidden"></div>
                <p class="text-lg text-gray-300 mt-3" id="coordinates-text">Click on any inverter marker to view details</p>
            </div>

            <!-- Nodes Table -->
            <div class="p-6 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 transition duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-emerald-400">All Nodes Overview</h2>
                    <div class="text-gray-400">
                        <span id="total-nodes">5</span> Nodes Active
                    </div>
                </div>
                <div class="node-table-container">
                    <table class="w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Power (W)</th>
                                <th class="px-4 py-3">Voltage (V)</th>
                                <th class="px-4 py-3">Current (A)</th>
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

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        // User Profile Management
        document.addEventListener('DOMContentLoaded', function() {
            // Get user data from localStorage
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
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, sign out',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call logout function from auth.js
                        if (typeof logout === 'function') {
                            logout();
                        } else {
                            // Fallback logout
                            localStorage.clear();
                            window.location.href = '/auth/login.php';
                        }
                    }
                });
            });

            // Make logout function globally available if not in auth.js
            if (typeof logout === 'undefined') {
                window.logout = function() {
                    Swal.fire({
                        icon: "info",
                        title: "Signing Out",
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => {
                        localStorage.clear();
                        window.location.href = "/auth/login.php";
                    }, 1500);
                };
            }
        });

        // Auth check - moved to bottom to ensure DOM is loaded
        (function() {
            try {
                const token = localStorage.getItem("jwt");
                const user = localStorage.getItem("user");

                if (!token || !user) {
                    console.log("Not authenticated, redirecting to login");
                    window.location.href = "/auth/login.php";
                    return;
                }

                // If requireAuth exists, use it
                if (typeof requireAuth === 'function') {
                    requireAuth("ADMIN").then(authResult => {
                        if (!authResult) {
                            window.location.href = "/auth/login.php";
                        }
                    });
                }
           } catch (error) {
                console.error("Auth check error:", error);
                window.location.href = "/auth/login.php";
            }
        })();
    </script>

    <!-- <script src="assets/js/dashboard-inverter.js"></script> -->
    <script src="assets/js/dashboard3.js"></script>

</body>

</html>