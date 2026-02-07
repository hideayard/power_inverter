<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geran Komuniti Iskandar Puteri Rendah Karbon 5.0 Dashboard</title>

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Chart.js for graphing capabilities -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js" crossorigin="anonymous"></script>
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
                            <p id="dropdown-email" class="text-sm text-gray-400 truncate">loading@example.com</p>
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
                        Geran Komuniti Iskandar Puteri Rendah Karbon 5.0
                    </h1>
                    <p class="text-gray-400 text-center sm:text-left mt-2">
                        Real-time AC Power System Metrics Across Multiple Nodes
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

        <!-- Rest of your existing dashboard content remains exactly the same -->
        <!-- Node Selection and Toggle Controls -->
        <section class="mb-6 p-4 bg-gray-800 rounded-lg border border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-white mb-2">Node Selection</h2>
                    <div id="node-selector" class="flex flex-wrap gap-2">
                        <!-- Node selection buttons will be populated here -->
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-300">View Mode:</span>
                        <div class="flex bg-gray-700 rounded-lg p-1">
                            <button id="toggle-avg" class="px-3 py-1 rounded-md bg-blue-600 text-white">Average</button>
                            <button id="toggle-total" class="px-3 py-1 rounded-md text-gray-300 hover:bg-gray-600">Total</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-300">Selected Node:</span>
                        <select id="selected-node" class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-1 text-white">
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Business Metrics Section -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <!-- Projected Annual Savings -->
            <div class="md:col-span-2 bg-green-900/40 p-6 rounded-xl shadow-2xl border border-green-700 transition duration-300 hover:shadow-green-500/50">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-green-400">Projected Annual Savings</h2>
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 4v4m0 4v2"></path>
                    </svg>
                </div>
                <div class="text-6xl font-extrabold tracking-tight text-white mt-3" id="total-savings">RM 8,212.50</div>
                <p class="text-lg text-green-300 mt-2">
                    Capital retained yearly across all nodes by switching from high-cost fossil fuels.
                </p>
            </div>

            <!-- Daily Cost Difference Card -->
            <div class="metric-card p-6 rounded-xl shadow-lg border-b-4 border-yellow-500 transition duration-300 hover:shadow-yellow-700/50">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-yellow-400">Avg Cost Reduction</h2>
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.001 0 0120.488 9z"></path>
                    </svg>
                </div>
                <div class="text-4xl font-bold tracking-tight text-white" id="avg-reduction">90% Daily</div>
                <div class="text-lg font-light text-gray-400 mt-1" id="total-saved">RM 22.50 saved per cycle</div>
            </div>

        </section>

        <!-- Main Chart Section (Multi-line Trend Analysis) -->
        <section class="mb-10 p-6 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 transition duration-300 hover:shadow-lg hover:shadow-blue-500/30">
            <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
                <h2 class="text-2xl font-semibold text-blue-400" id="chart-title">Selected Node Parameter Trend Analysis</h2>

                <!-- Parameter Selector Checkboxes -->
                <div id="param-selector" class="flex flex-wrap gap-x-4 gap-y-2 text-sm">
                    <label class="inline-flex items-center cursor-pointer text-green-400">
                        <input type="checkbox" value="power" checked onchange="handleParamChange()" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-700 bg-gray-700 focus:ring-green-500">
                        <span class="ml-2 font-medium">Power (W)</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer text-cyan-400">
                        <input type="checkbox" value="voltage" checked onchange="handleParamChange()" class="form-checkbox h-4 w-4 text-cyan-500 rounded border-gray-700 bg-gray-700 focus:ring-cyan-500">
                        <span class="ml-2 font-medium">Voltage (V)</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer text-red-400">
                        <input type="checkbox" value="current" checked onchange="handleParamChange()" class="form-checkbox h-4 w-4 text-red-500 rounded border-gray-700 bg-gray-700 focus:ring-red-500">
                        <span class="ml-2 font-medium">Current (A)</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer text-indigo-400">
                        <input type="checkbox" value="pf" onchange="handleParamChange()" class="form-checkbox h-4 w-4 text-indigo-500 rounded border-gray-700 bg-gray-700 focus:ring-indigo-500">
                        <span class="ml-2 font-medium">PF (Ratio)</span>
                    </label>
                </div>
            </div>

            <div class="h-64">
                <canvas id="powerChart"></canvas>
            </div>
        </section>

        <!-- Metrics Grid (8 cards - your existing code remains) -->
        <main id="metrics-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Your existing 8 metric cards here -->
            <!-- 1. Voltage -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-cyan-700/50 hover:ring-2 ring-cyan-500/50" id="voltage-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-cyan-400">Voltage</h2>
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="voltage-value">230.38</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Volts (V)</div>
            </div>

            <!-- 2. Current -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-red-700/50 hover:ring-2 ring-red-500/50" id="current-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-red-400">Current</h2>
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="current-value">2.897</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Amperes (A)</div>
            </div>

            <!-- 3. Active Power -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-green-700/50 hover:ring-2 ring-green-500/50" id="power-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-green-400">Active Power</h2>
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.976l-1.293 1.293-2.31 2.31"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="power-value">658.7</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Watts (W)</div>
            </div>

            <!-- 4. Power Factor -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-indigo-700/50 hover:ring-2 ring-indigo-500/50" id="pf-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-indigo-400">Power Factor</h2>
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v14h-3"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="pf-value">0.987</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Ratio</div>
            </div>

            <!-- 5. Frequency -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-fuchsia-700/50 hover:ring-2 ring-fuchsia-500/50" id="frequency-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-fuchsia-400">Frequency</h2>
                    <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13h10M7 17h10M7 9h10"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="frequency-value">50.01</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Hertz (Hz)</div>
            </div>

            <!-- 6. Total Energy (Persisted) -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-yellow-700/50 hover:ring-2 ring-yellow-500/50" id="energy-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-yellow-400">Total Energy</h2>
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 18h18M3 6h18"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="energy-value">6250.55</div>
                <div class="text-2xl font-light text-gray-400 mt-1">Kilowatt-hours (kWh)</div>
            </div>

            <!-- 7. Battery Remaining Time (SoC Visual) -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-orange-700/50 hover:ring-2 ring-orange-500/50" id="battery-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-orange-400">Battery Status (SoC)</h2>
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-5xl font-bold tracking-tight" id="battery-time-value">04:32</div>
                    <div class="text-2xl font-bold text-orange-300" id="battery-soc-percent">95%</div>
                </div>
                <div class="text-2xl font-light text-gray-400 mb-2">Time Remaining (H:MM)</div>
                <div class="h-4 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div id="battery-bar" class="h-full bg-green-500 rounded-full battery-bar" style="width: 95%;"></div>
                </div>
            </div>

            <!-- 8. Carbon Reduction (Calculated) -->
            <div class="metric-card p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-sky-700/50 hover:ring-2 ring-sky-500/50" id="carbon-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-sky-400">CO₂ Reduction</h2>
                    <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0020 14c0 1.576-.58 3.056-1.55 4.168M1.168 18.45A8.001 8.001 0 014 6h5"></path>
                    </svg>
                </div>
                <div class="text-5xl font-bold tracking-tight" id="carbon-reduction-value">3657.85</div>
                <div class="text-2xl font-light text-gray-400 mt-1">kg CO₂</div>
            </div>
        </main>

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

    <script>
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