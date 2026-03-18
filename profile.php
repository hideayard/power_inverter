<?php
session_start();
// Check if user is logged in (optional - you can use your auth.js instead)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Energy Monitoring System</title>
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
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .edit-btn {
            transition: all 0.3s ease;
        }
        
        .edit-btn:hover {
            transform: scale(1.05);
        }
        
        input:disabled, textarea:disabled {
            background-color: rgba(255, 255, 255, 0.05) !important;
            cursor: not-allowed;
        }
        
        input, textarea, select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #3b82f6;
            background-color: rgba(255, 255, 255, 0.08);
            outline: none;
        }

        .tab-active {
            border-bottom: 3px solid #667eea;
            color: #667eea;
            font-weight: 600;
        }

        .avatar-container {
            position: relative;
            width: 128px;
            height: 128px;
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(59, 130, 246, 0.5);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
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

        .activity-item {
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .security-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .security-item:hover {
            border-left-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
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
                    <a href="profile.php" class="text-blue-400 hover:text-blue-300 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-user mr-1"></i> Profile
                    </a>
                    <a href="settings.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">My Profile</h1>
            <p class="text-gray-400 mt-2">Manage your personal information and account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Overview -->
            <div class="lg:col-span-2">
                <!-- Profile Header -->
                <div class="glass-card p-6 mb-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <!-- Profile Picture -->
                        <div class="avatar-container">
                            <img id="profile-avatar-img" src="" alt="Profile" class="avatar-image hidden">
                            <div id="profile-avatar-placeholder" class="avatar-placeholder">
                                <span id="profile-initials">JD</span>
                            </div>
                            <button id="change-photo-btn" class="absolute bottom-2 right-2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                            <input type="file" id="photo-upload" class="hidden" accept="image/*">
                        </div>
                        
                        <!-- User Info -->
                        <div class="flex-1 text-center sm:text-left">
                            <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start">
                                <div>
                                    <h2 id="profile-name" class="text-2xl font-bold text-white">Loading...</h2>
                                    <p id="profile-role" class="text-blue-400 font-medium">-</p>
                                    <p id="profile-email" class="text-gray-400 mt-2">-</p>
                                    <p id="profile-joined" class="text-gray-500 text-sm mt-1">-</p>
                                </div>
                                <button id="edit-profile-btn" class="edit-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium mt-4 sm:mt-0">
                                    <i class="fas fa-edit mr-2"></i> Edit Profile
                                </button>
                            </div>
                            
                            <!-- Status Indicators -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar-check text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Devices</p>
                                            <p class="text-xl font-bold" id="stat-devices">0</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-chart-line text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Active</p>
                                            <p class="text-xl font-bold" id="stat-active">0</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-bolt text-purple-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Total kWh</p>
                                            <p class="text-xl font-bold" id="stat-energy">0</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-leaf text-yellow-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">CO₂ Saved</p>
                                            <p class="text-xl font-bold" id="stat-co2">0t</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-700 mb-6">
                    <div class="flex space-x-8">
                        <button id="tab-personal" class="tab-active py-3 px-1 text-lg">Personal Info</button>
                        <button id="tab-activity" class="py-3 px-1 text-lg text-gray-400 hover:text-white">Activity</button>
                        <button id="tab-security" class="py-3 px-1 text-lg text-gray-400 hover:text-white">Security</button>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div id="profile-loading" class="hidden">
                    <div class="loading-spinner"></div>
                    <p class="text-center text-gray-400">Loading profile...</p>
                </div>

                <!-- Personal Info Tab -->
                <div id="personal-info" class="glass-card p-6">
                    <h3 class="text-xl font-bold text-white mb-6">Personal Information</h3>
                    
                    <form id="profile-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                                <input type="text" id="full-name" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your full name" disabled>
                            </div>
                            
                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                                <input type="text" id="username" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter username" disabled readonly>
                            </div>
                            
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                                <input type="email" id="email" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter email address" disabled>
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                                <input type="tel" id="phone" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="+60 12-345 6789" disabled>
                            </div>
                            
                            <!-- Timezone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Timezone</label>
                                <select id="timezone" disabled
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Asia/Kuala_Lumpur">Asia/Kuala Lumpur (GMT+8)</option>
                                    <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                    <option value="Asia/Jakarta">Asia/Jakarta (GMT+7)</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo (GMT+9)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                </select>
                            </div>
                            
                            <!-- Language -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Language</label>
                                <select id="language" disabled
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="en">English</option>
                                    <option value="ms">Bahasa Malaysia</option>
                                    <option value="zh">中文</option>
                                    <option value="ta">தமிழ்</option>
                                </select>
                            </div>
                            
                            <!-- Bio -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
                                <textarea id="bio" rows="3"
                                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                          placeholder="Tell us about yourself..." disabled></textarea>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 mt-8" id="form-actions" style="display: none;">
                            <button type="button" id="cancel-btn" 
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium">
                                Cancel
                            </button>
                            <button type="submit" id="save-btn"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Activity Tab (Hidden by default) -->
                <div id="activity-tab" class="hidden glass-card p-6">
                    <h3 class="text-xl font-bold text-white mb-6">Recent Activity</h3>
                    
                    <div id="activity-loading" class="hidden">
                        <div class="loading-spinner"></div>
                        <p class="text-center text-gray-400">Loading activities...</p>
                    </div>

                    <div id="activity-list" class="space-y-4">
                        <!-- Activities will be loaded here -->
                    </div>

                    <div id="no-activities" class="text-center py-8 hidden">
                        <i class="fas fa-history text-4xl text-gray-600 mb-3"></i>
                        <p class="text-gray-400">No recent activities found</p>
                    </div>
                </div>

                <!-- Security Tab (Hidden by default) -->
                <div id="security-tab" class="hidden glass-card p-6">
                    <h3 class="text-xl font-bold text-white mb-6">Security Settings</h3>
                    
                    <div class="space-y-6">
                        <!-- Password Change -->
                        <div class="security-item p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-white">Change Password</h4>
                                    <p class="text-sm text-gray-400">Update your password regularly</p>
                                </div>
                                <button onclick="showChangePasswordModal()" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                                    Change
                                </button>
                            </div>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <!-- <div class="security-item p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-white">Two-Factor Authentication</h4>
                                    <p class="text-sm text-gray-400">Add an extra layer of security</p>
                                </div>
                                <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Enabled
                                </span>
                            </div>
                        </div> -->

                        <!-- Active Sessions -->
                        <div class="security-item p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-white">Active Sessions</h4>
                                    <p class="text-sm text-gray-400">Manage your logged-in devices</p>
                                </div>
                                <button onclick="showSessions()" 
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                    View
                                </button>
                            </div>
                        </div>

                        <!-- Login History -->
                        <div class="security-item p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-white">Login History</h4>
                                    <p class="text-sm text-gray-400">Review recent login activity</p>
                                </div>
                                <button onclick="showLoginHistory()" 
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="lg:col-span-1">
                <!-- Account Status -->
                <div class="glass-card p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-4">Account Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Verification</span>
                            <span class="text-green-400 font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Verified
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Account Type</span>
                            <span id="account-type" class="text-blue-400 font-medium">Standard</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Account Status</span>
                            <span id="account-status" class="text-green-400 font-medium">Active</span>
                        </div>
                        <!-- <div class="flex items-center justify-between">
                            <span class="text-gray-400">2FA Status</span>
                            <span class="text-green-400 font-medium">
                                <i class="fas fa-shield-alt mr-1"></i> Enabled
                            </span>
                        </div> -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Last Login</span>
                            <span id="last-login" class="text-gray-300">-</span>
                        </div>
                    </div>
                    
                    <!-- <div class="mt-6 pt-6 border-t border-gray-700">
                        <button id="upgrade-btn" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 rounded-lg font-medium">
                            <i class="fas fa-crown mr-2"></i> Upgrade to Pro
                        </button>
                    </div> -->
                </div>

                <!-- Quick Links -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Quick Links</h3>
                    <div class="space-y-3">
                        <a href="settings.php" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-blue-400"></i>
                            </div>
                            <div>
                                <p class="font-medium">Account Settings</p>
                                <p class="text-sm text-gray-400">Manage your preferences</p>
                            </div>
                        </a>
                        
                        <a href="dashboard-inverter.php" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-10 h-10 bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tachometer-alt text-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium">Dashboard</p>
                                <p class="text-sm text-gray-400">Back to monitoring</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="glass-card p-6 mt-6">
                    <h3 class="text-lg font-bold text-white mb-4">Security Tips</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Use a strong, unique password</p>
                        </div>
                        <!-- <div class="flex items-start">
                            <i class="fas fa-check text-green-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Enable two-factor authentication</p>
                        </div> -->
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Review login activity regularly</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Update your recovery email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="password-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6 m-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Change Password</h3>
                <button onclick="closePasswordModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="password-form" onsubmit="changePassword(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                        <input type="password" id="modal-current-password" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                        <input type="password" id="modal-new-password" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                        <input type="password" id="modal-confirm-password" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePasswordModal()"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentUser = null;
        let baseUrl = '';
        let activities = [];
        let devices = [];

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            const token = localStorage.getItem("jwt");
            
            // Try to decode JWT to get URL
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    baseUrl = payload.url || 'https://itrust-tech.id';
                    console.log('Base URL from JWT:', baseUrl);
                } catch (e) {
                    console.error('Error decoding JWT:', e);
                    baseUrl = 'https://itrust-tech.id'; // Fallback
                }
            }

            const user = JSON.parse(localStorage.getItem("user") || "{}");
            
            if (!token || !user) {
                window.location.href = "/auth/login.php";
                return;
            }
            
            currentUser = user;
            
            // Show loading
            document.getElementById('profile-loading').classList.remove('hidden');
            
            // Fetch profile from server
            fetchUserProfile();
            
            // Fetch devices for stats
            fetchUserDevices();
            
            // Setup event listeners
            setupEventListeners();
        });

        // Helper function to get full image URL
        function getImageUrl(photoPath) {
            if (!photoPath) return null;
            if (photoPath.startsWith('http')) return photoPath;
            return `${baseUrl}/${photoPath}`;
        }

        // Fetch user profile from server
        async function fetchUserProfile() {
            try {
                const authToken = localStorage.getItem("jwt");

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
                    currentUser = result.data;
                    localStorage.setItem('user', JSON.stringify(currentUser));
                    populateProfileData(currentUser);
                } else {
                    // Fallback to localStorage
                    populateProfileData(currentUser);
                }
            } catch (error) {
                console.error("Error fetching profile:", error);
                // Fallback to localStorage
                populateProfileData(currentUser);
            } finally {
                document.getElementById('profile-loading').classList.add('hidden');
            }
        }

        // Fetch user devices for stats
        async function fetchUserDevices() {
            try {
                const authToken = localStorage.getItem("jwt");

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

                if (!response.ok) throw new Error(`Failed to fetch devices: ${response.status}`);

                const result = await response.json();

                if (result.success && result.data) {
                    devices = result.data;
                    updateDeviceStats();
                }
            } catch (error) {
                console.error("Error fetching devices:", error);
            }
        }

        // Update device statistics
        function updateDeviceStats() {
            const total = devices.length;
            const active = devices.filter(d => d.is_active).length;
            
            document.getElementById('stat-devices').textContent = total;
            document.getElementById('stat-active').textContent = active;
            
            // Simulate energy stats (replace with actual data when available)
            document.getElementById('stat-energy').textContent = '1,234';
            document.getElementById('stat-co2').textContent = '0.8t';
        }

        // Populate profile data
        function populateProfileData(user) {
            // Generate initials
            const name = user.user_nama || user.name || user.username || "User";
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
            
            // Handle avatar
            if (user.user_foto) {
                const avatarUrl = getImageUrl(user.user_foto);
                const avatarImg = document.getElementById('profile-avatar-img');
                avatarImg.src = avatarUrl;
                avatarImg.classList.remove('hidden');
                document.getElementById('profile-avatar-placeholder').classList.add('hidden');
            } else {
                document.getElementById('profile-avatar-img').classList.add('hidden');
                document.getElementById('profile-avatar-placeholder').classList.remove('hidden');
                document.getElementById('profile-initials').textContent = initials;
            }
            
            // Set profile data
            document.getElementById('profile-name').textContent = name;
            document.getElementById('profile-role').textContent = user.user_tipe === "ADMIN" ? "Administrator" : "Standard User";
            document.getElementById('profile-email').textContent = user.user_email || "email@example.com";
            document.getElementById('account-type').textContent = user.user_tipe === "ADMIN" ? "Administrator" : "Standard";
            document.getElementById('account-status').textContent = user.user_status ? "Active" : "Inactive";
            
            // Set form values
            document.getElementById('full-name').value = name;
            document.getElementById('username').value = user.user_name || user.username || "";
            document.getElementById('email').value = user.user_email || "";
            document.getElementById('phone').value = user.user_hp || user.phone || "";
            document.getElementById('timezone').value = user.timezone || "Asia/Kuala_Lumpur";
            document.getElementById('language').value = user.language || "en";
            document.getElementById('bio').value = user.bio || "Energy monitoring enthusiast focused on sustainable solutions.";
            
            // Set joined date
            if (user.created_at) {
                const joinedDate = new Date(user.created_at).toLocaleDateString('en-US', { 
                    month: 'short', 
                    year: 'numeric' 
                });
                document.getElementById('profile-joined').textContent = `Member since: ${joinedDate}`;
            } else {
                document.getElementById('profile-joined').textContent = `Member since: Jan 2024`;
            }

            // Set last login (simulated)
            document.getElementById('last-login').textContent = new Date().toLocaleString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        }
        
        function setupEventListeners() {
            // Edit Profile Button
            document.getElementById('edit-profile-btn').addEventListener('click', function() {
                enableFormEditing(true);
            });
            
            // Cancel Button
            document.getElementById('cancel-btn').addEventListener('click', function() {
                enableFormEditing(false);
                // Reload original data
                populateProfileData(currentUser);
            });
            
            // Save Button
            document.getElementById('profile-form').addEventListener('submit', function(e) {
                e.preventDefault();
                saveProfileChanges();
            });
            
            // Tabs
            document.getElementById('tab-personal').addEventListener('click', function() {
                switchTab('personal');
            });
            
            document.getElementById('tab-activity').addEventListener('click', function() {
                switchTab('activity');
                loadActivities();
            });
            
            document.getElementById('tab-security').addEventListener('click', function() {
                switchTab('security');
            });
            
            // Change Photo Button
            document.getElementById('change-photo-btn').addEventListener('click', function() {
                document.getElementById('photo-upload').click();
            });
            
            // Photo Upload
            document.getElementById('photo-upload').addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    uploadPhoto(e.target.files[0]);
                }
            });
            
            // Upgrade Button
            document.getElementById('upgrade-btn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Upgrade to Pro',
                    html: `
                        <div class="text-left">
                            <p class="mb-4">Upgrade to unlock premium features:</p>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Advanced analytics & reports</li>
                                <li>Priority support</li>
                                <li>Unlimited devices</li>
                                <li>Custom alerts & notifications</li>
                                <li>Historical data export</li>
                            </ul>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Upgrade Now',
                    cancelButtonText: 'Maybe Later'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Coming Soon!',
                            text: 'Pro features will be available soon.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        }

        function switchTab(tabName) {
            // Update tab styles
            document.getElementById('tab-personal').classList.remove('tab-active');
            document.getElementById('tab-activity').classList.remove('tab-active');
            document.getElementById('tab-security').classList.remove('tab-active');
            
            document.getElementById('tab-personal').classList.add('text-gray-400', 'hover:text-white');
            document.getElementById('tab-activity').classList.add('text-gray-400', 'hover:text-white');
            document.getElementById('tab-security').classList.add('text-gray-400', 'hover:text-white');
            
            // Hide all tab contents
            document.getElementById('personal-info').classList.add('hidden');
            document.getElementById('activity-tab').classList.add('hidden');
            document.getElementById('security-tab').classList.add('hidden');
            
            // Show selected tab
            if (tabName === 'personal') {
                document.getElementById('tab-personal').classList.add('tab-active');
                document.getElementById('tab-personal').classList.remove('text-gray-400', 'hover:text-white');
                document.getElementById('personal-info').classList.remove('hidden');
            } else if (tabName === 'activity') {
                document.getElementById('tab-activity').classList.add('tab-active');
                document.getElementById('tab-activity').classList.remove('text-gray-400', 'hover:text-white');
                document.getElementById('activity-tab').classList.remove('hidden');
            } else if (tabName === 'security') {
                document.getElementById('tab-security').classList.add('tab-active');
                document.getElementById('tab-security').classList.remove('text-gray-400', 'hover:text-white');
                document.getElementById('security-tab').classList.remove('hidden');
            }
        }
        
        function enableFormEditing(enable) {
            const formInputs = document.querySelectorAll('#profile-form input:not([readonly]), #profile-form textarea, #profile-form select');
            const formActions = document.getElementById('form-actions');
            const editBtn = document.getElementById('edit-profile-btn');
            
            if (enable) {
                formInputs.forEach(input => {
                    input.disabled = false;
                });
                formActions.style.display = 'flex';
                editBtn.style.display = 'none';
            } else {
                formInputs.forEach(input => {
                    input.disabled = true;
                });
                formActions.style.display = 'none';
                editBtn.style.display = 'block';
            }
        }
        
        async function saveProfileChanges() {
            const authToken = localStorage.getItem("jwt");

            const userData = {
                user_nama: document.getElementById('full-name').value,
                user_email: document.getElementById('email').value,
                user_hp: document.getElementById('phone').value,
                timezone: document.getElementById('timezone').value,
                language: document.getElementById('language').value,
                bio: document.getElementById('bio').value
            };
            
            // Show loading
            const saveBtn = document.getElementById('save-btn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            saveBtn.disabled = true;
            
            try {
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
                    // Update UI
                    document.getElementById('profile-name').textContent = userData.user_nama;
                    document.getElementById('profile-email').textContent = userData.user_email;
                    
                    // Update localStorage
                    const updatedUser = { ...currentUser, ...userData };
                    localStorage.setItem("user", JSON.stringify(updatedUser));
                    currentUser = updatedUser;
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        text: 'Your profile has been successfully updated.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    
                    // Reset form
                    enableFormEditing(false);
                } else {
                    throw new Error(result.message || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error saving profile:', error);
                
                // Fallback
                document.getElementById('profile-name').textContent = userData.user_nama;
                document.getElementById('profile-email').textContent = userData.user_email;
                
                const updatedUser = { ...currentUser, ...userData };
                localStorage.setItem("user", JSON.stringify(updatedUser));
                currentUser = updatedUser;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Offline Mode',
                    text: 'Profile updated locally. Changes will sync when online.',
                    timer: 3000,
                    showConfirmButton: false
                });
                
                enableFormEditing(false);
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        }

        async function uploadPhoto(file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please select an image file.',
                });
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 2MB.',
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
                    const avatarUrl = getImageUrl(result.data.user_foto);
                    const avatarImg = document.getElementById('profile-avatar-img');
                    avatarImg.src = avatarUrl;
                    avatarImg.classList.remove('hidden');
                    document.getElementById('profile-avatar-placeholder').classList.add('hidden');

                    Swal.fire({
                        icon: 'success',
                        title: 'Photo Updated!',
                        text: 'Your profile photo has been updated.',
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
                    title: 'Upload Failed',
                    text: error.message || 'Failed to upload photo',
                });
            }
        }

        // Activity functions
        function loadActivities() {
            document.getElementById('activity-loading').classList.remove('hidden');
            
            // Simulate loading activities (replace with actual API call)
            setTimeout(() => {
                const activities = [
                    { type: 'login', description: 'Logged in from Chrome on Windows', time: '2 hours ago', icon: 'fa-sign-in-alt', color: 'blue' },
                    { type: 'device', description: 'Added new device: Main Inverter', time: '1 day ago', icon: 'fa-microchip', color: 'green' },
                    { type: 'alert', description: 'Energy threshold exceeded', time: '2 days ago', icon: 'fa-exclamation-triangle', color: 'yellow' },
                    { type: 'settings', description: 'Updated profile settings', time: '3 days ago', icon: 'fa-cog', color: 'purple' },
                    { type: 'device', description: 'Device offline: Battery Bank', time: '5 days ago', icon: 'fa-power-off', color: 'red' },
                ];

                const activityList = document.getElementById('activity-list');
                activityList.innerHTML = activities.map(activity => `
                    <div class="activity-item flex items-center p-3 rounded-lg bg-gray-800/30">
                        <div class="w-10 h-10 bg-${activity.color}-900/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas ${activity.icon} text-${activity.color}-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-white">${activity.description}</p>
                            <p class="text-sm text-gray-400">${activity.time}</p>
                        </div>
                    </div>
                `).join('');

                document.getElementById('activity-loading').classList.add('hidden');
                document.getElementById('no-activities').classList.add('hidden');
            }, 1000);
        }

        // Security functions
        function showChangePasswordModal() {
            document.getElementById('password-modal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('password-modal').classList.add('hidden');
            document.getElementById('password-form').reset();
        }

        async function changePassword(event) {
            event.preventDefault();

            const currentPass = document.getElementById('modal-current-password').value;
            const newPass = document.getElementById('modal-new-password').value;
            const confirmPass = document.getElementById('modal-confirm-password').value;

            if (newPass !== confirmPass) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New passwords do not match.'
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
                        title: 'Password Updated',
                        text: 'Your password has been changed successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closePasswordModal();
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

        function showSessions() {
            Swal.fire({
                title: 'Active Sessions',
                html: `
                    <div class="text-left space-y-3">
                        <div class="flex items-center justify-between p-2 bg-gray-700 rounded">
                            <div>
                                <p class="font-medium text-white">Current Session</p>
                                <p class="text-sm text-gray-400">Chrome on Windows</p>
                            </div>
                            <span class="text-green-400 text-sm">Active</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-700 rounded">
                            <div>
                                <p class="font-medium text-white">Mobile Device</p>
                                <p class="text-sm text-gray-400">Safari on iPhone</p>
                            </div>
                            <span class="text-yellow-400 text-sm">2 hours ago</span>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function showLoginHistory() {
            Swal.fire({
                title: 'Login History',
                html: `
                    <div class="text-left space-y-3">
                        <div class="p-2 bg-gray-700 rounded">
                            <p class="text-white">Today, 14:30 - Chrome on Windows</p>
                            <p class="text-sm text-gray-400">IP: 192.168.1.100</p>
                        </div>
                        <div class="p-2 bg-gray-700 rounded">
                            <p class="text-white">Yesterday, 09:15 - Safari on iPhone</p>
                            <p class="text-sm text-gray-400">IP: 192.168.1.101</p>
                        </div>
                        <div class="p-2 bg-gray-700 rounded">
                            <p class="text-white">Jan 15, 2025 - Firefox on Mac</p>
                            <p class="text-sm text-gray-400">IP: 192.168.1.102</p>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    </script>
</body>
</html>