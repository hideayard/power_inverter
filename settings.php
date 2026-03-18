<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Energy Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        .tab-active {
            border-left: 4px solid #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .device-card {
            transition: all 0.3s ease;
        }

        .device-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-online {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .status-offline {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
        }

        .status-maintenance {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        input, select, textarea {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #3b82f6;
            background-color: rgba(255, 255, 255, 0.08);
            outline: none;
            ring: 2px solid #3b82f6;
        }

        select option {
            background-color: #1f2937;
            color: white;
        }
    </style>
</head>

<body class="text-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-gray-900 border-b border-gray-800 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="dashboard-inverter.php" class="flex items-center">
                        <i class="fas fa-bolt text-2xl text-green-400 mr-2"></i>
                        <span class="text-xl font-bold">Energy Monitor</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard-inverter.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="profile.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-user mr-1"></i> Profile
                    </a>
                    <a href="settings.php" class="text-blue-400 hover:text-blue-300 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Settings</h1>
            <p class="text-gray-400 mt-2">Manage your profile and connected devices</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Column - Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-white mb-4">Menu</h3>
                    <div class="space-y-2">
                        <button onclick="switchTab('profile')" class="tab-btn active w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors flex items-center">
                            <i class="fas fa-user mr-3 text-blue-400 w-5"></i>
                            <span>Profile Settings</span>
                        </button>
                        
                        <button onclick="switchTab('devices')" class="tab-btn w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors flex items-center">
                            <i class="fas fa-microchip mr-3 text-green-400 w-5"></i>
                            <span>My Devices</span>
                            <span id="device-count" class="ml-auto bg-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </button>
                    </div>

                    <!-- User Info Card -->
                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <div class="flex items-center p-3 bg-gray-800/50 rounded-lg">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                <span id="sidebar-initials">JD</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate" id="sidebar-name">John Doe</p>
                                <p class="text-sm text-gray-400 truncate" id="sidebar-email">john@example.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings Content -->
            <div class="lg:col-span-3">
                <!-- Profile Settings Tab -->
                <div id="profile-tab" class="tab-content active">
                    <div class="glass-card p-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-user mr-3 text-blue-400"></i>
                            Profile Settings
                        </h2>

                        <form id="profile-form" onsubmit="saveProfile(event)">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 flex justify-center mb-4">
                                    <div class="relative">
                                        <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                                            <span id="profile-initials">JD</span>
                                        </div>
                                        <button type="button" onclick="changeAvatar()" class="absolute bottom-0 right-0 bg-blue-600 p-2 rounded-full hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-camera text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                                    <input type="text" id="profile-fullname"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                                        placeholder="Enter your full name" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                                    <input type="email" id="profile-email"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                                        placeholder="Enter your email" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                                    <input type="tel" id="profile-phone"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                                        placeholder="+60 12-345 6789">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Company</label>
                                    <input type="text" id="profile-company"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                                        placeholder="Your company name">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
                                    <textarea id="profile-bio" rows="3"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 resize-none"
                                        placeholder="Tell us about yourself..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Timezone</label>
                                    <select id="profile-timezone" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                                        <option value="Asia/Kuala_Lumpur">Asia/Kuala Lumpur (GMT+8)</option>
                                        <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                        <option value="Asia/Jakarta">Asia/Jakarta (GMT+7)</option>
                                        <option value="Asia/Tokyo">Asia/Tokyo (GMT+9)</option>
                                        <option value="Australia/Sydney">Australia/Sydney (GMT+11)</option>
                                        <option value="UTC">UTC (GMT+0)</option>
                                        <option value="America/New_York">America/New York (GMT-5)</option>
                                        <option value="America/Los_Angeles">America/Los Angeles (GMT-8)</option>
                                        <option value="Europe/London">Europe/London (GMT+0)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Language</label>
                                    <select id="profile-language" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                                        <option value="en">English</option>
                                        <option value="ms">Bahasa Malaysia</option>
                                        <option value="zh">中文</option>
                                        <option value="ta">தமிழ்</option>
                                        <option value="ja">日本語</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-8">
                                <button type="button" onclick="resetProfileForm()"
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium transition-colors">
                                    Reset
                                </button>
                                <button type="submit"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>

                        <!-- Change Password Section -->
                        <div class="mt-8 pt-8 border-t border-gray-700">
                            <h3 class="text-lg font-medium text-white mb-4">Change Password</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                                    <input type="password" id="current-password"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                                    <input type="password" id="new-password"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                                    <div class="flex">
                                        <input type="password" id="confirm-password"
                                            class="flex-1 bg-gray-800/50 border border-gray-700 rounded-l-lg px-4 py-3 text-white focus:border-blue-500">
                                        <button type="button" onclick="changePassword()"
                                            class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-r-lg font-medium transition-colors">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-400 mt-2">Password must be at least 8 characters with numbers and special characters.</p>
                        </div>
                    </div>
                </div>

                <!-- Devices Management Tab -->
                <div id="devices-tab" class="tab-content hidden">
                    <div class="glass-card p-6">
                        <!-- Header with Add Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-microchip mr-3 text-green-400"></i>
                                My Devices
                            </h2>
                            <button onclick="showAddDeviceModal()"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add Device
                            </button>
                        </div>

                        <!-- Device Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Total Devices</p>
                                <p class="text-2xl font-bold text-white" id="total-devices">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Online</p>
                                <p class="text-2xl font-bold text-green-400" id="online-devices">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Offline</p>
                                <p class="text-2xl font-bold text-gray-400" id="offline-devices">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Maintenance</p>
                                <p class="text-2xl font-bold text-yellow-400" id="maintenance-devices">0</p>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="flex flex-col md:flex-row gap-4 mb-6">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" id="device-search" placeholder="Search devices..." 
                                    class="w-full bg-gray-800/50 border border-gray-700 rounded-lg pl-10 pr-4 py-2 text-white focus:border-blue-500"
                                    onkeyup="filterDevices()">
                            </div>
                            <select id="device-filter" onchange="filterDevices()" 
                                class="w-full md:w-48 bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500">
                                <option value="all">All Devices</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <!-- Devices Grid -->
                        <div id="devices-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Devices will be loaded here -->
                        </div>

                        <!-- Empty State -->
                        <div id="empty-devices" class="text-center py-12 hidden">
                            <i class="fas fa-microchip text-5xl text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-medium text-white mb-2">No Devices Found</h3>
                            <p class="text-gray-400 mb-6">Get started by adding your first device</p>
                            <button onclick="showAddDeviceModal()" 
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add Device
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Device Modal -->
    <div id="device-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6 m-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white" id="modal-title">Add New Device</h3>
                <button onclick="closeDeviceModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="device-form" onsubmit="saveDevice(event)">
                <input type="hidden" id="device-id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device Name</label>
                        <input type="text" id="device-name" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="e.g., Main Inverter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device Type</label>
                        <select id="device-type" required class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                            <option value="inverter">Inverter</option>
                            <option value="solar_panel">Solar Panel</option>
                            <option value="battery">Battery</option>
                            <option value="meter">Energy Meter</option>
                            <option value="sensor">Sensor</option>
                            <option value="controller">Controller</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Serial Number</label>
                        <input type="text" id="device-serial" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="SN-XXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Location</label>
                        <input type="text" id="device-location"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="e.g., Main Building, Room 101">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select id="device-status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Installation Date</label>
                        <input type="date" id="device-installation-date"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                        <textarea id="device-notes" rows="2"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 resize-none"
                            placeholder="Additional information..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeDeviceModal()"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Save Device
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Delete Device</h3>
                <p class="text-gray-400 mb-6">Are you sure you want to delete this device? This action cannot be undone.</p>
                
                <div class="flex justify-center space-x-4">
                    <button onclick="closeDeleteModal()"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 font-medium transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let devices = [];
        let deleteDeviceId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            const token = localStorage.getItem('jwt');
            const user = JSON.parse(localStorage.getItem('user') || '{}');

            if (!token || !user) {
                window.location.href = '/auth/login.php';
                return;
            }

            // Load user data
            loadUserData(user);
            
            // Load devices from localStorage or initialize with sample data
            loadDevices();
            
            // Update device count in sidebar
            updateDeviceCount();
        });

        function loadUserData(user) {
            // Set initials
            const name = user.name || user.username || 'User';
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
            
            // Update sidebar
            document.getElementById('sidebar-initials').textContent = initials;
            document.getElementById('sidebar-name').textContent = name;
            document.getElementById('sidebar-email').textContent = user.user_email || '';
            
            // Update profile form
            document.getElementById('profile-initials').textContent = initials;
            document.getElementById('profile-fullname').value = name;
            document.getElementById('profile-email').value = user.user_email || '';
            document.getElementById('profile-phone').value = user.phone || '';
            document.getElementById('profile-company').value = user.company || '';
            document.getElementById('profile-bio').value = user.bio || '';
            document.getElementById('profile-timezone').value = user.timezone || 'Asia/Kuala_Lumpur';
            document.getElementById('profile-language').value = user.language || 'en';
        }

        function loadDevices() {
            // Try to load from localStorage
            const savedDevices = localStorage.getItem('devices');
            
            if (savedDevices) {
                devices = JSON.parse(savedDevices);
            } else {
                // Sample devices for demonstration
                devices = [
                    {
                        id: '1',
                        name: 'Main Inverter',
                        type: 'inverter',
                        serial: 'INV-2024-001',
                        location: 'Main Building - Room 101',
                        status: 'online',
                        installationDate: '2024-01-15',
                        notes: 'Main inverter for solar panels',
                        lastSeen: new Date().toISOString()
                    },
                    {
                        id: '2',
                        name: 'Battery Bank',
                        type: 'battery',
                        serial: 'BAT-2024-002',
                        location: 'Main Building - Basement',
                        status: 'online',
                        installationDate: '2024-01-15',
                        notes: 'Lithium battery bank',
                        lastSeen: new Date().toISOString()
                    },
                    {
                        id: '3',
                        name: 'Solar Array Controller',
                        type: 'controller',
                        serial: 'CTRL-2024-003',
                        location: 'Roof - Section A',
                        status: 'maintenance',
                        installationDate: '2024-01-20',
                        notes: 'Scheduled maintenance',
                        lastSeen: new Date(Date.now() - 86400000).toISOString()
                    }
                ];
                localStorage.setItem('devices', JSON.stringify(devices));
            }
            
            renderDevices();
        }

        function renderDevices() {
            const container = document.getElementById('devices-container');
            const emptyState = document.getElementById('empty-devices');
            const searchTerm = document.getElementById('device-search')?.value.toLowerCase() || '';
            const filterStatus = document.getElementById('device-filter')?.value || 'all';

            // Filter devices
            let filteredDevices = devices.filter(device => {
                const matchesSearch = device.name.toLowerCase().includes(searchTerm) ||
                                    device.serial.toLowerCase().includes(searchTerm) ||
                                    device.location?.toLowerCase().includes(searchTerm) || false;
                
                const matchesFilter = filterStatus === 'all' || device.status === filterStatus;
                
                return matchesSearch && matchesFilter;
            });

            // Update stats
            updateDeviceStats();

            if (filteredDevices.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            // Render devices
            container.innerHTML = filteredDevices.map(device => `
                <div class="device-card bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas ${getDeviceIcon(device.type)} text-${getDeviceColor(device.type)}-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white">${device.name}</h4>
                                <p class="text-sm text-gray-400">${device.type.replace('_', ' ').toUpperCase()}</p>
                            </div>
                        </div>
                        <span class="status-badge status-${device.status}">
                            <i class="fas fa-circle text-${device.status === 'online' ? 'green' : device.status === 'offline' ? 'gray' : 'yellow'}-400 text-xs mr-1"></i>
                            ${device.status.charAt(0).toUpperCase() + device.status.slice(1)}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-barcode w-5"></i>
                            <span>${device.serial}</span>
                        </div>
                        ${device.location ? `
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-map-marker-alt w-5"></i>
                            <span>${device.location}</span>
                        </div>
                        ` : ''}
                        ${device.installationDate ? `
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-calendar w-5"></i>
                            <span>Installed: ${formatDate(device.installationDate)}</span>
                        </div>
                        ` : ''}
                        ${device.notes ? `
                        <div class="flex items-start text-gray-400">
                            <i class="fas fa-sticky-note w-5 mt-1"></i>
                            <span class="flex-1">${device.notes}</span>
                        </div>
                        ` : ''}
                    </div>

                    <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-700">
                        <button onclick="editDevice('${device.id}')" 
                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <button onclick="showDeleteModal('${device.id}')" 
                            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function getDeviceIcon(type) {
            const icons = {
                'inverter': 'fa-bolt',
                'solar_panel': 'fa-sun',
                'battery': 'fa-battery-full',
                'meter': 'fa-gauge-high',
                'sensor': 'fa-temperature-high',
                'controller': 'fa-microchip'
            };
            return icons[type] || 'fa-microchip';
        }

        function getDeviceColor(type) {
            const colors = {
                'inverter': 'green',
                'solar_panel': 'yellow',
                'battery': 'blue',
                'meter': 'purple',
                'sensor': 'red',
                'controller': 'indigo'
            };
            return colors[type] || 'gray';
        }

        function updateDeviceStats() {
            const total = devices.length;
            const online = devices.filter(d => d.status === 'online').length;
            const offline = devices.filter(d => d.status === 'offline').length;
            const maintenance = devices.filter(d => d.status === 'maintenance').length;

            document.getElementById('total-devices').textContent = total;
            document.getElementById('online-devices').textContent = online;
            document.getElementById('offline-devices').textContent = offline;
            document.getElementById('maintenance-devices').textContent = maintenance;
            document.getElementById('device-count').textContent = total;
        }

        function updateDeviceCount() {
            const count = devices.length;
            document.getElementById('device-count').textContent = count;
        }

        function filterDevices() {
            renderDevices();
        }

        function showAddDeviceModal() {
            document.getElementById('modal-title').textContent = 'Add New Device';
            document.getElementById('device-form').reset();
            document.getElementById('device-id').value = '';
            
            // Set default installation date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('device-installation-date').value = today;
            
            document.getElementById('device-modal').classList.remove('hidden');
        }

        function editDevice(deviceId) {
            const device = devices.find(d => d.id === deviceId);
            if (!device) return;

            document.getElementById('modal-title').textContent = 'Edit Device';
            document.getElementById('device-id').value = device.id;
            document.getElementById('device-name').value = device.name;
            document.getElementById('device-type').value = device.type;
            document.getElementById('device-serial').value = device.serial;
            document.getElementById('device-location').value = device.location || '';
            document.getElementById('device-status').value = device.status;
            document.getElementById('device-installation-date').value = device.installationDate || '';
            document.getElementById('device-notes').value = device.notes || '';

            document.getElementById('device-modal').classList.remove('hidden');
        }

        function closeDeviceModal() {
            document.getElementById('device-modal').classList.add('hidden');
            document.getElementById('device-form').reset();
        }

        function saveDevice(event) {
            event.preventDefault();

            const deviceId = document.getElementById('device-id').value;
            const deviceData = {
                name: document.getElementById('device-name').value,
                type: document.getElementById('device-type').value,
                serial: document.getElementById('device-serial').value,
                location: document.getElementById('device-location').value,
                status: document.getElementById('device-status').value,
                installationDate: document.getElementById('device-installation-date').value,
                notes: document.getElementById('device-notes').value,
                lastSeen: new Date().toISOString()
            };

            if (deviceId) {
                // Update existing device
                const index = devices.findIndex(d => d.id === deviceId);
                if (index !== -1) {
                    devices[index] = { ...devices[index], ...deviceData, id: deviceId };
                }
            } else {
                // Add new device
                const newDevice = {
                    ...deviceData,
                    id: Date.now().toString()
                };
                devices.push(newDevice);
            }

            // Save to localStorage
            localStorage.setItem('devices', JSON.stringify(devices));

            // Close modal and refresh
            closeDeviceModal();
            renderDevices();
            updateDeviceCount();

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `Device ${deviceId ? 'updated' : 'added'} successfully!`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function showDeleteModal(deviceId) {
            deleteDeviceId = deviceId;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteDeviceId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function confirmDelete() {
            if (deleteDeviceId) {
                devices = devices.filter(d => d.id !== deleteDeviceId);
                localStorage.setItem('devices', JSON.stringify(devices));
                
                closeDeleteModal();
                renderDevices();
                updateDeviceCount();

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Device has been deleted.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }

        // Profile functions
        function saveProfile(event) {
            event.preventDefault();

            const userData = {
                name: document.getElementById('profile-fullname').value,
                email: document.getElementById('profile-email').value,
                phone: document.getElementById('profile-phone').value,
                company: document.getElementById('profile-company').value,
                bio: document.getElementById('profile-bio').value,
                timezone: document.getElementById('profile-timezone').value,
                language: document.getElementById('profile-language').value
            };

            // Save to localStorage
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const updatedUser = { ...user, ...userData };
            localStorage.setItem('user', JSON.stringify(updatedUser));

            // Update UI
            loadUserData(updatedUser);

            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile has been saved successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function resetProfileForm() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            loadUserData(user);
        }

        function changePassword() {
            const currentPass = document.getElementById('current-password').value;
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-password').value;

            if (!currentPass || !newPass || !confirmPass) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all password fields.'
                });
                return;
            }

            if (newPass.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Password must be at least 8 characters long.'
                });
                return;
            }

            if (newPass !== confirmPass) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New passwords do not match.'
                });
                return;
            }

            // In a real app, make API call here
            Swal.fire({
                icon: 'success',
                title: 'Password Updated',
                text: 'Your password has been changed successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                document.getElementById('current-password').value = '';
                document.getElementById('new-password').value = '';
                document.getElementById('confirm-password').value = '';
            });
        }

        function changeAvatar() {
            Swal.fire({
                title: 'Change Avatar',
                html: `
                    <div class="text-center">
                        <p class="mb-4">Choose a new profile picture</p>
                        <input type="file" accept="image/*" id="avatar-upload" class="hidden">
                        <button onclick="document.getElementById('avatar-upload').click()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Select Image
                        </button>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'tab-active');
            });
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });

            const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn =>
                btn.textContent.toLowerCase().includes(tabName)
            );
            
            if (activeBtn) {
                activeBtn.classList.add('active', 'tab-active');
            }

            document.getElementById(tabName + '-tab').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    </script>
</body>

</html>