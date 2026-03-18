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

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
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

        .loading-spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top: 3px solid #3b82f6;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .user-row:hover {
            background: rgba(59, 130, 246, 0.1);
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

                        <!-- User Management Tab (Visible only for Admin) -->
                        <button id="user-management-tab-btn" onclick="switchTab('users')" class="tab-btn hidden w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors flex items-center">
                            <i class="fas fa-users-cog mr-3 text-purple-400 w-5"></i>
                            <span>User Management</span>
                            <span id="user-count" class="ml-auto bg-gray-700 text-xs px-2 py-1 rounded-full">0</span>
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
                                <p class="text-xs text-gray-500 truncate" id="sidebar-role">Administrator</p>
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
                                        <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold overflow-hidden">
                                            <span id="profile-initials" class="absolute inset-0 flex items-center justify-center">JD</span>
                                            <img id="profile-avatar-img" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                                        </div>
                                        <button type="button" onclick="changeAvatar()" class="absolute bottom-0 right-0 bg-blue-600 p-2 rounded-full hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-camera text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                                    <input type="text" id="profile-username"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                                        placeholder="Username" readonly disabled>
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
                                    <label class="block text-sm font-medium text-gray-300 mb-2">User Type</label>
                                    <input type="text" id="profile-type"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white"
                                        readonly disabled>
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
                                <p class="text-sm text-gray-400 mb-1">Active</p>
                                <p class="text-2xl font-bold text-green-400" id="active-devices">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Inactive</p>
                                <p class="text-2xl font-bold text-gray-400" id="inactive-devices">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Device Limit</p>
                                <p class="text-2xl font-bold text-yellow-400" id="device-limit">5</p>
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
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <button onclick="syncDevicesWithServer()" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i>Sync
                            </button>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loading-devices" class="hidden">
                            <div class="loading-spinner"></div>
                            <p class="text-center text-gray-400">Loading devices...</p>
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

                <!-- User Management Tab (Admin Only) -->
                <div id="users-tab" class="tab-content hidden">
                    <div class="glass-card p-6">
                        <!-- Header with Stats -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-users-cog mr-3 text-purple-400"></i>
                                User Management
                            </h2>
                            <button onclick="refreshUserList()" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i>Refresh
                            </button>
                        </div>

                        <!-- User Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Total Users</p>
                                <p class="text-2xl font-bold text-white" id="total-users">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Active</p>
                                <p class="text-2xl font-bold text-green-400" id="active-users">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Inactive</p>
                                <p class="text-2xl font-bold text-gray-400" id="inactive-users">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Admins</p>
                                <p class="text-2xl font-bold text-purple-400" id="admin-users">0</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-400 mb-1">Regular Users</p>
                                <p class="text-2xl font-bold text-blue-400" id="regular-users">0</p>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="flex flex-col md:flex-row gap-4 mb-6">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" id="user-search" placeholder="Search users..." 
                                    class="w-full bg-gray-800/50 border border-gray-700 rounded-lg pl-10 pr-4 py-2 text-white focus:border-blue-500"
                                    onkeyup="filterUsers()">
                            </div>
                            <select id="user-type-filter" onchange="filterUsers()" 
                                class="w-full md:w-48 bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500">
                                <option value="all">All Types</option>
                                <option value="ADMIN">Admin</option>
                                <option value="USER">Regular User</option>
                            </select>
                            <select id="user-status-filter" onchange="filterUsers()" 
                                class="w-full md:w-48 bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500">
                                <option value="all">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loading-users" class="hidden">
                            <div class="loading-spinner"></div>
                            <p class="text-center text-gray-400">Loading users...</p>
                        </div>

                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-700">
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">User</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Contact</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Type</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Status</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Devices</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Joined</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body">
                                    <!-- Users will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="empty-users" class="text-center py-12 hidden">
                            <i class="fas fa-users text-5xl text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-medium text-white mb-2">No Users Found</h3>
                            <p class="text-gray-400">There are no users matching your criteria.</p>
                        </div>

                        <!-- Pagination -->
                        <div id="users-pagination" class="flex justify-center items-center space-x-2 mt-6 hidden">
                            <button onclick="loadUsersPage('prev')" class="px-3 py-1 bg-gray-700 rounded-lg hover:bg-gray-600 disabled:opacity-50" id="prev-page">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="page-info" class="text-gray-400 px-4">Page 1</span>
                            <button onclick="loadUsersPage('next')" class="px-3 py-1 bg-gray-700 rounded-lg hover:bg-gray-600 disabled:opacity-50" id="next-page">
                                <i class="fas fa-chevron-right"></i>
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
                <input type="hidden" id="device-record-id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device ID</label>
                        <input type="text" id="device-device-id" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="Enter device ID (e.g., INV-001)">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device Name</label>
                        <input type="text" id="device-name" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="e.g., Main Inverter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device Alias</label>
                        <input type="text" id="device-alias"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500"
                            placeholder="e.g., Living Room Inverter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea id="device-description" rows="2"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 resize-none"
                            placeholder="Device description..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Remarks</label>
                        <textarea id="device-remark" rows="2"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 resize-none"
                            placeholder="Additional remarks..."></textarea>
                    </div>

                    <!-- Admin: User Assignment -->
                    <div id="device-user-assignment" class="hidden">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Assign to User</label>
                        <select id="device-user-id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                            <option value="">Select User</option>
                        </select>
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

    <!-- Edit User Modal (Admin) -->
    <div id="edit-user-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6 m-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Edit User</h3>
                <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="edit-user-form" onsubmit="updateUser(event)">
                <input type="hidden" id="edit-user-id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                        <input type="text" id="edit-username" readonly disabled
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                        <input type="text" id="edit-fullname" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" id="edit-email" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                        <input type="tel" id="edit-phone"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">User Type</label>
                        <select id="edit-user-type" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                            <option value="USER">Regular User</option>
                            <option value="ADMIN">Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select id="edit-user-status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditUserModal()"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Update User
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
                <h3 class="text-xl font-bold text-white mb-2">Confirm Delete</h3>
                <p class="text-gray-400 mb-6" id="delete-message">Are you sure you want to delete this item?</p>
                
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
        let users = [];
        let allUsers = [];
        let deleteId = null;
        let deleteType = null;
        let currentUser = null;
        let isAdmin = false;
        
        // Pagination variables
        let currentPage = 1;
        let totalPages = 1;
        let totalUsers = 0;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            const token = localStorage.getItem('jwt');
            const user = JSON.parse(localStorage.getItem('user') || '{}');

            if (!token || !user) {
                window.location.href = '/auth/login.php';
                return;
            }

            currentUser = user;
            isAdmin = user.user_tipe === 'ADMIN';

            // Load user profile from server
            fetchUserProfile();
            
            // Load devices from server
            fetchDevicesFromServer();
            
            // Show admin menu if user is admin
            if (isAdmin) {
                document.getElementById('user-management-tab-btn').classList.remove('hidden');
                fetchUsersFromServer();
            }
        });

        // ============= PROFILE FUNCTIONS =============

        async function fetchUserProfile() {
            try {
                const authToken = localStorage.getItem("jwt");
                if (!authToken) return;

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "get_user_profile",
                    }),
                });

                if (!response.ok) throw new Error(`Failed to fetch profile: ${response.status}`);

                const result = await response.json();

                if (result.success && result.data) {
                    // Update user data in localStorage
                    const userData = result.data;
                    localStorage.setItem('user', JSON.stringify(userData));
                    currentUser = userData;
                    isAdmin = userData.user_tipe === 'ADMIN';
                    
                    loadUserData(userData);
                }
            } catch (error) {
                console.error("Error fetching profile:", error);
                // Fallback to localStorage
                loadUserData(currentUser);
            }
        }

        function loadUserData(user) {
            // Set initials
            const name = user.user_nama || user.name || user.username || 'User';
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
            
            // Update sidebar
            document.getElementById('sidebar-initials').textContent = initials;
            document.getElementById('sidebar-name').textContent = name;
            document.getElementById('sidebar-email').textContent = user.user_email || user.email || '';
            document.getElementById('sidebar-role').textContent = user.user_tipe === 'ADMIN' ? 'Administrator' : 'Standard User';
            
            // Update profile form
            document.getElementById('profile-initials').textContent = initials;
            document.getElementById('profile-username').value = user.user_name || user.username || '';
            document.getElementById('profile-fullname').value = name;
            document.getElementById('profile-email').value = user.user_email || user.email || '';
            document.getElementById('profile-phone').value = user.user_hp || user.phone || '';
            document.getElementById('profile-type').value = user.user_tipe === 'ADMIN' ? 'Administrator' : 'Standard User';
            document.getElementById('profile-bio').value = user.bio || '';
            document.getElementById('profile-timezone').value = user.timezone || 'Asia/Kuala_Lumpur';
            document.getElementById('profile-language').value = user.language || 'en';

            // Handle avatar
            if (user.user_foto) {
                const avatarImg = document.getElementById('profile-avatar-img');
                avatarImg.src = user.user_foto;
                avatarImg.classList.remove('hidden');
                document.getElementById('profile-initials').classList.add('hidden');
            }
        }

        async function saveProfile(event) {
            event.preventDefault();

            const authToken = localStorage.getItem("jwt");
            if (!authToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please login first'
                });
                return;
            }

            const userData = {
                user_nama: document.getElementById('profile-fullname').value,
                user_email: document.getElementById('profile-email').value,
                user_hp: document.getElementById('profile-phone').value,
                // These would need corresponding backend fields
                // bio: document.getElementById('profile-bio').value,
                // timezone: document.getElementById('profile-timezone').value,
                // language: document.getElementById('profile-language').value
            };

            try {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "update_user",
                        ...userData
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    // Update localStorage
                    const updatedUser = { ...currentUser, ...userData };
                    localStorage.setItem('user', JSON.stringify(updatedUser));
                    currentUser = updatedUser;
                    
                    // Update UI
                    loadUserData(updatedUser);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Profile updated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(result.message || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error saving profile:', error);
                
                // Fallback to localStorage
                const updatedUser = { ...currentUser, ...userData };
                localStorage.setItem('user', JSON.stringify(updatedUser));
                currentUser = updatedUser;
                loadUserData(updatedUser);

                Swal.fire({
                    icon: 'warning',
                    title: 'Offline Mode',
                    text: 'Profile updated locally. Changes will sync when online.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        function resetProfileForm() {
            loadUserData(currentUser);
        }

        async function changePassword() {
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

            const authToken = localStorage.getItem("jwt");
            
            try {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "change_password",
                        current_password: currentPass,
                        new_password: newPass,
                        confirm_password: confirmPass
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Password changed successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        document.getElementById('current-password').value = '';
                        document.getElementById('new-password').value = '';
                        document.getElementById('confirm-password').value = '';
                    });
                } else {
                    throw new Error(result.message || 'Failed to change password');
                }
            } catch (error) {
                console.error('Error changing password:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to change password'
                });
            }
        }

        function changeAvatar() {
            // Create file input
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/jpeg,image/png,image/jpg';
            fileInput.onchange = async (e) => {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file type
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select an image file'
                    });
                    return;
                }

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'File size must be less than 2MB'
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('user_foto', file);
                formData.append('action', 'update_photo');

                const authToken = localStorage.getItem("jwt");

                try {
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const response = await fetch("/proxy2.php", {
                        method: "POST",
                        headers: {
                            "Authorization": `Bearer ${authToken}`,
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Update avatar
                        const avatarImg = document.getElementById('profile-avatar-img');
                        avatarImg.src = result.data.user_foto;
                        avatarImg.classList.remove('hidden');
                        document.getElementById('profile-initials').classList.add('hidden');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Photo updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(result.message || 'Failed to upload photo');
                    }
                } catch (error) {
                    console.error('Error uploading photo:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to upload photo'
                    });
                }
            };
            fileInput.click();
        }

        // ============= DEVICE FUNCTIONS =============

        async function fetchDevicesFromServer() {
            try {
                const authToken = localStorage.getItem("jwt");
                if (!authToken) {
                    throw new Error("Please login first");
                }

                document.getElementById('loading-devices').classList.remove('hidden');
                document.getElementById('devices-container').innerHTML = '';
                document.getElementById('empty-devices').classList.add('hidden');

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "get_devices",
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch devices: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    devices = result.data;
                    localStorage.setItem('devices', JSON.stringify(devices));
                    renderDevices();
                } else {
                    throw new Error(result.message || "Failed to fetch devices");
                }
            } catch (error) {
                console.error("Error fetching devices:", error);
                
                const savedDevices = localStorage.getItem('devices');
                if (savedDevices) {
                    devices = JSON.parse(savedDevices);
                    renderDevices();
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Offline Mode',
                        text: 'Using cached device data.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    document.getElementById('empty-devices').classList.remove('hidden');
                }
            } finally {
                document.getElementById('loading-devices').classList.add('hidden');
            }
        }

        function syncDevicesWithServer() {
            fetchDevicesFromServer();
        }

        function renderDevices() {
            const container = document.getElementById('devices-container');
            const emptyState = document.getElementById('empty-devices');
            const searchTerm = document.getElementById('device-search')?.value.toLowerCase() || '';
            const filterStatus = document.getElementById('device-filter')?.value || 'all';

            let filteredDevices = devices.filter(device => {
                const matchesSearch = (device.device_name?.toLowerCase().includes(searchTerm) ||
                                    device.device_alias?.toLowerCase().includes(searchTerm) ||
                                    device.device_id?.toLowerCase().includes(searchTerm) || false);
                
                const matchesFilter = filterStatus === 'all' || device.is_active.toString() === filterStatus;
                
                return matchesSearch && matchesFilter;
            });

            updateDeviceStats();

            if (filteredDevices.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            container.innerHTML = filteredDevices.map(device => `
                <div class="device-card bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-microchip text-green-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white">${device.device_name || 'Unnamed Device'}</h4>
                                <p class="text-sm text-gray-400">${device.device_alias || 'No alias'}</p>
                            </div>
                        </div>
                        <span class="status-badge ${device.is_active ? 'status-online' : 'status-offline'}">
                            <i class="fas fa-circle text-${device.is_active ? 'green' : 'gray'}-400 text-xs mr-1"></i>
                            ${device.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-qrcode w-5"></i>
                            <span>Device ID: ${device.device_id}</span>
                        </div>
                        ${device.device_description ? `
                        <div class="flex items-start text-gray-400">
                            <i class="fas fa-align-left w-5 mt-1"></i>
                            <span class="flex-1">${device.device_description}</span>
                        </div>
                        ` : ''}
                        ${device.device_remark ? `
                        <div class="flex items-start text-gray-400">
                            <i class="fas fa-sticky-note w-5 mt-1"></i>
                            <span class="flex-1">${device.device_remark}</span>
                        </div>
                        ` : ''}
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-calendar w-5"></i>
                            <span>Added: ${formatDate(device.created_at)}</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-700">
                        <button onclick="editDevice('${device.id}')" 
                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <button onclick="showDeleteModal('device', '${device.id}')" 
                            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function updateDeviceStats() {
            const total = devices.length;
            const active = devices.filter(d => d.is_active).length;
            const inactive = total - active;
            const limit = isAdmin ? '∞' : '5';

            document.getElementById('total-devices').textContent = total;
            document.getElementById('active-devices').textContent = active;
            document.getElementById('inactive-devices').textContent = inactive;
            document.getElementById('device-limit').textContent = limit;
            document.getElementById('device-count').textContent = total;
        }

        function filterDevices() {
            renderDevices();
        }

        function showAddDeviceModal() {
            document.getElementById('modal-title').textContent = 'Add New Device';
            document.getElementById('device-form').reset();
            document.getElementById('device-id').value = '';
            document.getElementById('device-record-id').value = '';
            
            // Show user assignment for admin
            const userAssignment = document.getElementById('device-user-assignment');
            if (isAdmin) {
                userAssignment.classList.remove('hidden');
                populateUserSelect();
            } else {
                userAssignment.classList.add('hidden');
            }
            
            document.getElementById('device-modal').classList.remove('hidden');
        }

        function editDevice(recordId) {
            const device = devices.find(d => d.id == recordId);
            if (!device) return;

            document.getElementById('modal-title').textContent = 'Edit Device';
            document.getElementById('device-id').value = device.id;
            document.getElementById('device-record-id').value = device.id;
            document.getElementById('device-device-id').value = device.device_id;
            document.getElementById('device-name').value = device.device_name || '';
            document.getElementById('device-alias').value = device.device_alias || '';
            document.getElementById('device-description').value = device.device_description || '';
            document.getElementById('device-remark').value = device.device_remark || '';

            // Show user assignment for admin
            const userAssignment = document.getElementById('device-user-assignment');
            if (isAdmin) {
                userAssignment.classList.remove('hidden');
                populateUserSelect(device.user_id);
            } else {
                userAssignment.classList.add('hidden');
            }

            document.getElementById('device-modal').classList.remove('hidden');
        }

        function closeDeviceModal() {
            document.getElementById('device-modal').classList.add('hidden');
            document.getElementById('device-form').reset();
        }

        async function saveDevice(event) {
            event.preventDefault();

            const authToken = localStorage.getItem("jwt");
            if (!authToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please login first'
                });
                return;
            }

            const recordId = document.getElementById('device-id').value;
            const isEditing = !!recordId;

            const deviceData = {
                device_id: document.getElementById('device-device-id').value,
                device_name: document.getElementById('device-name').value,
                device_alias: document.getElementById('device-alias').value,
                device_description: document.getElementById('device-description').value,
                device_remark: document.getElementById('device-remark').value,
            };

            // Add user_id for admin
            if (isAdmin) {
                const userId = document.getElementById('device-user-id').value;
                if (userId) {
                    deviceData.user_id = userId;
                }
            }

            if (isEditing) {
                deviceData.id = recordId;
            }

            try {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const endpoint = isEditing ? 'update_device' : 'create_device';
                
                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: endpoint,
                        ...deviceData
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Device ${isEditing ? 'updated' : 'added'} successfully!`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    closeDeviceModal();
                    await fetchDevicesFromServer();
                } else {
                    throw new Error(result.message || `Failed to ${isEditing ? 'update' : 'add'} device`);
                }
            } catch (error) {
                console.error('Error saving device:', error);
                
                // Fallback to localStorage
                if (isEditing) {
                    const index = devices.findIndex(d => d.id == recordId);
                    if (index !== -1) {
                        devices[index] = { ...devices[index], ...deviceData, id: recordId };
                    }
                } else {
                    const newDevice = {
                        ...deviceData,
                        id: Date.now().toString(),
                        created_at: new Date().toISOString(),
                        is_active: 1
                    };
                    devices.push(newDevice);
                }

                localStorage.setItem('devices', JSON.stringify(devices));
                
                closeDeviceModal();
                renderDevices();

                Swal.fire({
                    icon: 'warning',
                    title: 'Offline Mode',
                    text: `Device ${isEditing ? 'updated' : 'added'} locally.`,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        // ============= USER MANAGEMENT FUNCTIONS (ADMIN) =============

        async function fetchUsersFromServer(page = 1) {
            if (!isAdmin) return;

            try {
                const authToken = localStorage.getItem("jwt");
                
                document.getElementById('loading-users').classList.remove('hidden');
                document.getElementById('users-table-body').innerHTML = '';
                document.getElementById('empty-users').classList.add('hidden');

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "get_users",
                        page: page,
                        limit: 10
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch users: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    allUsers = result.data.users || [];
                    totalUsers = result.data.pagination?.total || 0;
                    totalPages = result.data.pagination?.total_pages || 1;
                    currentPage = page;
                    
                    filterUsers();
                    updatePagination();
                    
                    // Also fetch user stats
                    fetchUserStats();
                } else {
                    throw new Error(result.message || "Failed to fetch users");
                }
            } catch (error) {
                console.error("Error fetching users:", error);
                document.getElementById('empty-users').classList.remove('hidden');
            } finally {
                document.getElementById('loading-users').classList.add('hidden');
            }
        }

        async function fetchUserStats() {
            if (!isAdmin) return;

            try {
                const authToken = localStorage.getItem("jwt");
                
                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "get_user_stats",
                    }),
                });

                const result = await response.json();

                if (result.success && result.data) {
                    const stats = result.data;
                    document.getElementById('total-users').textContent = stats.total_users || 0;
                    document.getElementById('active-users').textContent = stats.active_users || 0;
                    document.getElementById('inactive-users').textContent = stats.inactive_users || 0;
                    document.getElementById('admin-users').textContent = stats.admin_users || 0;
                    document.getElementById('regular-users').textContent = stats.regular_users || 0;
                    document.getElementById('user-count').textContent = stats.total_users || 0;
                }
            } catch (error) {
                console.error("Error fetching user stats:", error);
            }
        }

        function filterUsers() {
            const searchTerm = document.getElementById('user-search')?.value.toLowerCase() || '';
            const typeFilter = document.getElementById('user-type-filter')?.value || 'all';
            const statusFilter = document.getElementById('user-status-filter')?.value || 'all';

            let filteredUsers = allUsers.filter(user => {
                const matchesSearch = (user.user_nama?.toLowerCase().includes(searchTerm) ||
                                    user.user_name?.toLowerCase().includes(searchTerm) ||
                                    user.user_email?.toLowerCase().includes(searchTerm) ||
                                    user.user_hp?.toLowerCase().includes(searchTerm) || false);
                
                const matchesType = typeFilter === 'all' || user.user_tipe === typeFilter;
                const matchesStatus = statusFilter === 'all' || user.user_status.toString() === statusFilter;
                
                return matchesSearch && matchesType && matchesStatus;
            });

            renderUsers(filteredUsers);
        }

        function renderUsers(usersToRender) {
            const tbody = document.getElementById('users-table-body');
            const emptyState = document.getElementById('empty-users');

            if (usersToRender.length === 0) {
                tbody.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            tbody.innerHTML = usersToRender.map(user => `
                <tr class="border-b border-gray-700 user-row">
                    <td class="py-3 px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                ${getInitials(user.user_nama || user.user_name)}
                            </div>
                            <div>
                                <p class="font-medium text-white">${user.user_nama || 'N/A'}</p>
                                <p class="text-xs text-gray-400">@${user.user_name}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-sm text-white">${user.user_email || 'N/A'}</p>
                        <p class="text-xs text-gray-400">${user.user_hp || 'No phone'}</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 text-xs rounded-full ${user.user_tipe === 'ADMIN' ? 'bg-purple-900/50 text-purple-400' : 'bg-blue-900/50 text-blue-400'}">
                            ${user.user_tipe || 'USER'}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="status-badge ${user.user_status ? 'status-active' : 'status-inactive'}">
                            <i class="fas fa-circle text-${user.user_status ? 'green' : 'red'}-400 text-xs mr-1"></i>
                            ${user.user_status ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="text-white">${user.device_count || 0}</span>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-sm text-white">${formatDate(user.created_at)}</p>
                    </td>
                    <td class="py-3 px-4">
                        <button onclick="editUser('${user.user_id}')" 
                            class="text-blue-400 hover:text-blue-300 mr-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="showDeleteModal('user', '${user.user_id}')" 
                            class="text-red-400 hover:text-red-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function refreshUserList() {
            fetchUsersFromServer(1);
        }

        function loadUsersPage(direction) {
            if (direction === 'prev' && currentPage > 1) {
                fetchUsersFromServer(currentPage - 1);
            } else if (direction === 'next' && currentPage < totalPages) {
                fetchUsersFromServer(currentPage + 1);
            }
        }

        function updatePagination() {
            const pagination = document.getElementById('users-pagination');
            if (totalPages > 1) {
                pagination.classList.remove('hidden');
                document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
                document.getElementById('prev-page').disabled = currentPage === 1;
                document.getElementById('next-page').disabled = currentPage === totalPages;
            } else {
                pagination.classList.add('hidden');
            }
        }

        function editUser(userId) {
            const user = allUsers.find(u => u.user_id == userId);
            if (!user) return;

            document.getElementById('edit-user-id').value = user.user_id;
            document.getElementById('edit-username').value = user.user_name;
            document.getElementById('edit-fullname').value = user.user_nama || '';
            document.getElementById('edit-email').value = user.user_email || '';
            document.getElementById('edit-phone').value = user.user_hp || '';
            document.getElementById('edit-user-type').value = user.user_tipe || 'USER';
            document.getElementById('edit-user-status').value = user.user_status;

            document.getElementById('edit-user-modal').classList.remove('hidden');
        }

        function closeEditUserModal() {
            document.getElementById('edit-user-modal').classList.add('hidden');
            document.getElementById('edit-user-form').reset();
        }

        async function updateUser(event) {
            event.preventDefault();

            const authToken = localStorage.getItem("jwt");
            const userId = document.getElementById('edit-user-id').value;

            const userData = {
                user_id: userId,
                user_nama: document.getElementById('edit-fullname').value,
                user_email: document.getElementById('edit-email').value,
                user_hp: document.getElementById('edit-phone').value,
                user_tipe: document.getElementById('edit-user-type').value,
                user_status: document.getElementById('edit-user-status').value
            };

            try {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: "update_user",
                        ...userData
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'User updated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    closeEditUserModal();
                    fetchUsersFromServer(currentPage);
                    fetchUserStats();
                } else {
                    throw new Error(result.message || 'Failed to update user');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to update user'
                });
            }
        }

        function populateUserSelect(selectedUserId = null) {
            const select = document.getElementById('device-user-id');
            select.innerHTML = '<option value="">Select User</option>';
            
            allUsers.forEach(user => {
                const option = document.createElement('option');
                option.value = user.user_id;
                option.textContent = `${user.user_nama || user.user_name} (${user.user_tipe})`;
                if (selectedUserId && user.user_id == selectedUserId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        // ============= DELETE FUNCTIONS =============

        function showDeleteModal(type, id) {
            deleteType = type;
            deleteId = id;
            
            const message = type === 'device' 
                ? 'Are you sure you want to delete this device? This action can be undone by reactivating.'
                : 'Are you sure you want to delete this user? This will deactivate the user account.';
            
            document.getElementById('delete-message').textContent = message;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteId = null;
            deleteType = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        async function confirmDelete() {
            if (!deleteId || !deleteType) return;

            const authToken = localStorage.getItem("jwt");
            
            try {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let endpoint = deleteType === 'device' ? 'delete_device' : 'delete_user';
                
                const response = await fetch("/proxy2.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Authorization": `Bearer ${authToken}`,
                    },
                    body: new URLSearchParams({
                        action: endpoint,
                        id: deleteId
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${deleteType === 'device' ? 'Device' : 'User'} deleted successfully.`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    closeDeleteModal();
                    
                    if (deleteType === 'device') {
                        await fetchDevicesFromServer();
                    } else {
                        await fetchUsersFromServer(currentPage);
                        await fetchUserStats();
                    }
                } else {
                    throw new Error(result.message || `Failed to delete ${deleteType}`);
                }
            } catch (error) {
                console.error(`Error deleting ${deleteType}:`, error);
                
                // Fallback
                if (deleteType === 'device') {
                    devices = devices.filter(d => d.id != deleteId);
                    localStorage.setItem('devices', JSON.stringify(devices));
                    renderDevices();
                }
                
                closeDeleteModal();

                Swal.fire({
                    icon: 'warning',
                    title: 'Offline Mode',
                    text: `${deleteType === 'device' ? 'Device' : 'User'} deleted locally.`,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        // ============= TAB SWITCHING =============

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

            // Refresh data when switching tabs
            if (tabName === 'devices') {
                fetchDevicesFromServer();
            } else if (tabName === 'users' && isAdmin) {
                fetchUsersFromServer(1);
                fetchUserStats();
            } else if (tabName === 'profile') {
                fetchUserProfile();
            }
        }

        // ============= UTILITY FUNCTIONS =============

        function getInitials(name) {
            if (!name) return 'U';
            return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    </script>
</body>

</html>