<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Energy Monitoring System</title>
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

        .settings-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .tab-active {
            border-bottom: 3px solid #667eea;
            color: #667eea;
            font-weight: 600;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        input:disabled,
        textarea:disabled,
        select:disabled {
            background-color: rgba(255, 255, 255, 0.05) !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        input,
        select,
        textarea {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #667eea;
            background-color: rgba(255, 255, 255, 0.08);
        }

        /* =============== DROPDOWN FIXES =============== */
        select,
        option {
            background-color: #1f2937 !important;
            color: white !important;
        }

        /* For disabled dropdowns */
        select:disabled {
            background-color: rgba(31, 41, 55, 0.5) !important;
            color: #6b7280 !important;
        }

        /* For focused dropdown */
        select:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }

        /* Ensure options have proper background */
        option:checked {
            background: #374151 !important;
        }

        option:hover {
            background: #4b5563 !important;
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
                    <a href="help.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-question-circle mr-1"></i> Help
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Account Settings</h1>
            <p class="text-gray-400 mt-2">Manage your preferences, security, and account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Column - Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-white mb-4">Settings Menu</h3>
                    <div class="space-y-2">
                        <button onclick="switchTab('profile')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn active">
                            <i class="fas fa-user mr-3 text-blue-400"></i>
                            <span>Profile Settings</span>
                        </button>
                        <button onclick="switchTab('security')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn">
                            <i class="fas fa-shield-alt mr-3 text-green-400"></i>
                            <span>Security</span>
                        </button>
                        <button onclick="switchTab('notifications')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn">
                            <i class="fas fa-bell mr-3 text-yellow-400"></i>
                            <span>Notifications</span>
                        </button>
                        <button onclick="switchTab('preferences')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn">
                            <i class="fas fa-sliders-h mr-3 text-purple-400"></i>
                            <span>Preferences</span>
                        </button>
                        <button onclick="switchTab('api')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn">
                            <i class="fas fa-code mr-3 text-red-400"></i>
                            <span>API Keys</span>
                        </button>
                        <button onclick="switchTab('danger')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors tab-btn text-red-400">
                            <i class="fas fa-exclamation-triangle mr-3"></i>
                            <span>Danger Zone</span>
                        </button>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <div class="flex items-center p-3 bg-gray-800/50 rounded-lg">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                <span id="settings-initials">JD</span>
                            </div>
                            <div>
                                <p class="font-medium" id="settings-name">John Doe</p>
                                <p class="text-sm text-gray-400" id="settings-role">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings Content -->
            <div class="lg:col-span-3">
                <!-- Profile Settings Tab -->
                <div id="profile-tab" class="tab-content active">
                    <div class="glass-card p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-user mr-3 text-blue-400"></i>
                            Profile Settings
                        </h2>

                        <form id="settings-form">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                                    <input type="text" id="settings-fullname"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter your full name">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                                    <input type="email" id="settings-email"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter your email">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                                    <input type="tel" id="settings-phone"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="+60 12-345 6789">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Company</label>
                                    <input type="text" id="settings-company"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Your company name">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
                                    <textarea id="settings-bio" rows="4"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Tell us about yourself..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Timezone</label>
                                    <select id="settings-timezone"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="UTC+8">UTC+8 (Malaysia Time)</option>
                                        <option value="UTC+0">UTC+0 (GMT)</option>
                                        <option value="UTC-5">UTC-5 (EST)</option>
                                        <option value="UTC-8">UTC-8 (PST)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Language</label>
                                    <select id="settings-language"
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="en">English</option>
                                        <option value="ms">Bahasa Malaysia</option>
                                        <option value="zh">中文</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-8">
                                <button type="button" onclick="resetSettings()"
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium">
                                    Reset
                                </button>
                                <button type="submit"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Tab -->
                <div id="security-tab" class="tab-content hidden">
                    <div class="glass-card p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-shield-alt mr-3 text-green-400"></i>
                            Security Settings
                        </h2>

                        <div class="space-y-6">
                            <!-- Password Change -->
                            <div>
                                <h3 class="text-lg font-medium text-white mb-4">Change Password</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                                        <div class="relative">
                                            <input type="password" id="current-password"
                                                class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                                placeholder="Current password">
                                            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-white" onclick="togglePasswordVisibility('current-password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                                        <div class="relative">
                                            <input type="password" id="new-password"
                                                class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                                placeholder="New password">
                                            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-white" onclick="togglePasswordVisibility('new-password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                                        <div class="relative">
                                            <input type="password" id="confirm-password"
                                                class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                                placeholder="Confirm new password">
                                            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-white" onclick="togglePasswordVisibility('confirm-password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-end">
                                        <button onclick="changePassword()"
                                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium w-full">
                                            Update Password
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-400 mt-2">Password must be at least 8 characters long with numbers and special characters.</p>
                            </div>

                            <!-- 2FA Settings -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">Two-Factor Authentication</h3>
                                <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-white">Two-Factor Authentication</p>
                                        <p class="text-sm text-gray-400">Add an extra layer of security to your account</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-green-400 font-medium">
                                            <i class="fas fa-check-circle mr-1"></i> Enabled
                                        </span>
                                        <button onclick="manage2FA()"
                                            class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 text-sm font-medium">
                                            Manage
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Session Management -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">Active Sessions</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-desktop text-blue-400 mr-3 text-lg"></i>
                                            <div>
                                                <p class="font-medium text-white">Current Session</p>
                                                <p class="text-sm text-gray-400">Chrome on Windows • Now</p>
                                            </div>
                                        </div>
                                        <span class="text-green-400 text-sm">Active</span>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-mobile-alt text-purple-400 mr-3 text-lg"></i>
                                            <div>
                                                <p class="font-medium text-white">Mobile Device</p>
                                                <p class="text-sm text-gray-400">Safari on iPhone • 2 hours ago</p>
                                            </div>
                                        </div>
                                        <button onclick="revokeSession(this)"
                                            class="px-3 py-1 border border-red-600 text-red-400 rounded-lg hover:bg-red-900/30 text-sm">
                                            Revoke
                                        </button>
                                    </div>
                                </div>
                                <button onclick="revokeAllSessions()"
                                    class="mt-4 px-4 py-2 border border-red-600 text-red-400 rounded-lg hover:bg-red-900/30 text-sm font-medium">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout All Other Devices
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div id="notifications-tab" class="tab-content hidden">
                    <div class="glass-card p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-bell mr-3 text-yellow-400"></i>
                            Notification Settings
                        </h2>

                        <div class="space-y-6">
                            <!-- Email Notifications -->
                            <div>
                                <h3 class="text-lg font-medium text-white mb-4">Email Notifications</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-white">System Alerts</p>
                                            <p class="text-sm text-gray-400">Critical system notifications and updates</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" id="system-alerts" checked>
                                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-white">Energy Reports</p>
                                            <p class="text-sm text-gray-400">Daily and weekly energy consumption reports</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" id="energy-reports" checked>
                                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-white">Maintenance Alerts</p>
                                            <p class="text-sm text-gray-400">Equipment maintenance reminders</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" id="maintenance-alerts" checked>
                                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Push Notifications -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">Push Notifications</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-white">Real-time Alerts</p>
                                            <p class="text-sm text-gray-400">Immediate notifications for critical events</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" id="realtime-alerts" checked>
                                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-white">Threshold Breaches</p>
                                            <p class="text-sm text-gray-400">When energy consumption exceeds limits</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" id="threshold-alerts" checked>
                                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Frequency -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">Notification Frequency</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button onclick="setNotificationFrequency('instant')"
                                        class="p-4 bg-gray-800/50 border border-blue-600 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <p class="font-medium text-white">Instant</p>
                                        <p class="text-sm text-gray-400">Receive immediately</p>
                                    </button>

                                    <button onclick="setNotificationFrequency('hourly')"
                                        class="p-4 bg-gray-800/50 border border-gray-700 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <p class="font-medium text-white">Hourly Digest</p>
                                        <p class="text-sm text-gray-400">Once per hour</p>
                                    </button>

                                    <button onclick="setNotificationFrequency('daily')"
                                        class="p-4 bg-gray-800/50 border border-gray-700 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <p class="font-medium text-white">Daily Summary</p>
                                        <p class="text-sm text-gray-400">Once per day</p>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-8">
                                <button onclick="resetNotifications()"
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium">
                                    Reset to Default
                                </button>
                                <button onclick="saveNotificationSettings()"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                    Save Notification Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div id="preferences-tab" class="tab-content hidden">
                    <div class="glass-card p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-sliders-h mr-3 text-purple-400"></i>
                            Preferences
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Theme Settings -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-white mb-4">Theme & Display</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <button onclick="setTheme('dark')"
                                        class="p-4 bg-gray-800 border border-blue-600 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-moon text-2xl mb-2 text-blue-400"></i>
                                        <p class="font-medium text-white">Dark Mode</p>
                                        <p class="text-sm text-gray-400">Default theme</p>
                                    </button>

                                    <button onclick="setTheme('light')"
                                        class="p-4 bg-gray-800/50 border border-gray-700 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-sun text-2xl mb-2 text-yellow-400"></i>
                                        <p class="font-medium text-white">Light Mode</p>
                                        <p class="text-sm text-gray-400">Bright theme</p>
                                    </button>

                                    <button onclick="setTheme('auto')"
                                        class="p-4 bg-gray-800/50 border border-gray-700 rounded-lg text-center hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-adjust text-2xl mb-2 text-purple-400"></i>
                                        <p class="font-medium text-white">Auto</p>
                                        <p class="text-sm text-gray-400">System preference</p>
                                    </button>
                                </div>
                            </div>

                            <!-- Dashboard Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-white mb-4">Dashboard Preferences</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Default Dashboard View</label>
                                        <select id="dashboard-view"
                                            class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="overview">Overview</option>
                                            <option value="detailed">Detailed View</option>
                                            <option value="analytics">Analytics</option>
                                            <option value="realtime">Real-time</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Refresh Interval</label>
                                        <select id="refresh-interval"
                                            class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="5">5 seconds</option>
                                            <option value="10">10 seconds</option>
                                            <option value="30" selected>30 seconds</option>
                                            <option value="60">1 minute</option>
                                            <option value="300">5 minutes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-white mb-4">Data & Units</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Energy Unit</label>
                                        <select id="energy-unit"
                                            class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="kWh">kWh</option>
                                            <option value="MWh">MWh</option>
                                            <option value="J">Joules</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Temperature Unit</label>
                                        <select id="temperature-unit"
                                            class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="celsius">°C</option>
                                            <option value="fahrenheit">°F</option>
                                            <option value="kelvin">K</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Currency</label>
                                        <select id="currency"
                                            class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="MYR">MYR (RM)</option>
                                            <option value="USD">USD ($)</option>
                                            <option value="EUR">EUR (€)</option>
                                            <option value="GBP">GBP (£)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 flex justify-end space-x-4 mt-8">
                                <button onclick="resetPreferences()"
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium">
                                    Reset Preferences
                                </button>
                                <button onclick="savePreferences()"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                    Save Preferences
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Keys Tab -->
                <div id="api-tab" class="tab-content hidden">
                    <div class="glass-card p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-code mr-3 text-red-400"></i>
                            API Keys Management
                        </h2>

                        <div class="space-y-6">
                            <!-- API Key List -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-white">Your API Keys</h3>
                                    <button onclick="generateNewAPIKey()"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                        <i class="fas fa-plus mr-2"></i> Generate New Key
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div class="p-4 bg-gray-800/50 rounded-lg">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-key text-yellow-400 mr-3"></i>
                                                <div>
                                                    <p class="font-medium text-white">Production API Key</p>
                                                    <p class="text-sm text-gray-400">Created: 2024-01-15 • Last used: Today</p>
                                                </div>
                                            </div>
                                            <span class="text-green-400 text-sm font-medium">Active</span>
                                        </div>
                                        <div class="flex items-center mt-3">
                                            <code class="flex-1 bg-gray-900 px-4 py-2 rounded text-sm font-mono text-gray-300 truncate">
                                                12345678901234567890
                                            </code>
                                            <button onclick="copyAPIKey(this)"
                                                class="ml-2 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="revokeAPIKey(this)"
                                                class="ml-2 px-3 py-2 bg-red-900/30 hover:bg-red-800 text-red-400 rounded-lg text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-800/50 rounded-lg">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-key text-blue-400 mr-3"></i>
                                                <div>
                                                    <p class="font-medium text-white">Development API Key</p>
                                                    <p class="text-sm text-gray-400">Created: 2024-01-10 • Last used: 2 days ago</p>
                                                </div>
                                            </div>
                                            <span class="text-green-400 text-sm font-medium">Active</span>
                                        </div>
                                        <div class="flex items-center mt-3">
                                            <code class="flex-1 bg-gray-900 px-4 py-2 rounded text-sm font-mono text-gray-300 truncate">
                                                sk_test_abcdef1234567890abcdef1234567890
                                            </code>
                                            <button onclick="copyAPIKey(this)"
                                                class="ml-2 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="revokeAPIKey(this)"
                                                class="ml-2 px-3 py-2 bg-red-900/30 hover:bg-red-800 text-red-400 rounded-lg text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- API Documentation -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">API Documentation</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <a href="#" class="p-4 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                                        <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mb-3">
                                            <i class="fas fa-book text-blue-400"></i>
                                        </div>
                                        <p class="font-medium text-white">Getting Started</p>
                                        <p class="text-sm text-gray-400">Learn API basics</p>
                                    </a>

                                    <a href="#" class="p-4 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                                        <div class="w-10 h-10 bg-green-900/30 rounded-lg flex items-center justify-center mb-3">
                                            <i class="fas fa-code text-green-400"></i>
                                        </div>
                                        <p class="font-medium text-white">API Reference</p>
                                        <p class="text-sm text-gray-400">Detailed endpoints</p>
                                    </a>

                                    <a href="#" class="p-4 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                                        <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mb-3">
                                            <i class="fas fa-shield-alt text-purple-400"></i>
                                        </div>
                                        <p class="font-medium text-white">Security Guide</p>
                                        <p class="text-sm text-gray-400">Best practices</p>
                                    </a>
                                </div>
                            </div>

                            <!-- API Usage Stats -->
                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-lg font-medium text-white mb-4">API Usage Statistics</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="p-4 bg-gray-800/50 rounded-lg">
                                        <p class="text-sm text-gray-400 mb-1">Requests Today</p>
                                        <p class="text-2xl font-bold text-white">1,247</p>
                                    </div>

                                    <div class="p-4 bg-gray-800/50 rounded-lg">
                                        <p class="text-sm text-gray-400 mb-1">Success Rate</p>
                                        <p class="text-2xl font-bold text-green-400">99.8%</p>
                                    </div>

                                    <div class="p-4 bg-gray-800/50 rounded-lg">
                                        <p class="text-sm text-gray-400 mb-1">Remaining Limit</p>
                                        <p class="text-2xl font-bold text-blue-400">4,873/5,000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone Tab -->
                <div id="danger-tab" class="tab-content hidden">
                    <div class="glass-card p-6">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-400"></i>
                            Danger Zone
                        </h2>

                        <div class="space-y-6">
                            <!-- Account Deletion -->
                            <div class="p-6 bg-red-900/20 border border-red-800 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-red-400 text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white mb-2">Delete Account</h3>
                                        <p class="text-gray-300 mb-4">
                                            Once you delete your account, there is no going back. All your data,
                                            including energy monitoring history, settings, and personal information
                                            will be permanently deleted.
                                        </p>
                                        <button onclick="deleteAccount()"
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                                            <i class="fas fa-trash mr-2"></i> Delete My Account
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Export -->
                            <div class="p-6 bg-yellow-900/20 border border-yellow-800 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-database text-yellow-400 text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white mb-2">Export All Data</h3>
                                        <p class="text-gray-300 mb-4">
                                            Download all your data from Energy Monitor. This includes your profile
                                            information, energy consumption history, settings, and logs in JSON format.
                                        </p>
                                        <button onclick="exportAllData()"
                                            class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium">
                                            <i class="fas fa-download mr-2"></i> Export All Data
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Reset Settings -->
                            <div class="p-6 bg-blue-900/20 border border-blue-800 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-undo-alt text-blue-400 text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white mb-2">Reset All Settings</h3>
                                        <p class="text-gray-300 mb-4">
                                            Reset all your settings to their default values. This includes notification
                                            preferences, dashboard settings, and other configurations. Your data will
                                            not be affected.
                                        </p>
                                        <button onclick="resetAllSettings()"
                                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                            <i class="fas fa-redo mr-2"></i> Reset All Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize settings page
        document.addEventListener('DOMContentLoaded', async function() {
            // Check authentication
            const token = localStorage.getItem("jwt");
            const user = JSON.parse(localStorage.getItem("user") || "{}");

            if (!token || !user) {
                window.location.href = "/auth/login.php";
                return;
            }

            // Populate user data
            populateSettingsData(user);

            // Load saved settings
            loadSavedSettings();

            // Setup event listeners
            setupSettingsListeners();
        });

        function populateSettingsData(user) {
            // Generate initials
            const name = user.name || user.username || "User";
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);

            // Set user info
            document.getElementById('settings-initials').textContent = initials;
            document.getElementById('settings-name').textContent = name;
            document.getElementById('settings-role').textContent = user.user_tipe === "ADMIN" ? "Administrator" : "Standard User";

            // Set form values
            document.getElementById('settings-fullname').value = name;
            document.getElementById('settings-email').value = user.user_email || "";
            document.getElementById('settings-phone').value = user.phone || "+60 12-345 6789";
            document.getElementById('settings-company').value = user.company || "";
            document.getElementById('settings-bio').value = user.bio || "Energy monitoring enthusiast.";
        }

        function loadSavedSettings() {
            // Load from localStorage
            const settings = JSON.parse(localStorage.getItem('userSettings') || '{}');

            // Timezone
            if (settings.timezone) {
                document.getElementById('settings-timezone').value = settings.timezone;
            }

            // Language
            if (settings.language) {
                document.getElementById('settings-language').value = settings.language;
            }

            // Notification settings
            if (settings.notifications) {
                document.getElementById('system-alerts').checked = settings.notifications.systemAlerts !== false;
                document.getElementById('energy-reports').checked = settings.notifications.energyReports !== false;
                document.getElementById('maintenance-alerts').checked = settings.notifications.maintenanceAlerts !== false;
                document.getElementById('realtime-alerts').checked = settings.notifications.realtimeAlerts !== false;
                document.getElementById('threshold-alerts').checked = settings.notifications.thresholdAlerts !== false;
            }

            // Preferences
            if (settings.preferences) {
                document.getElementById('dashboard-view').value = settings.preferences.dashboardView || 'overview';
                document.getElementById('refresh-interval').value = settings.preferences.refreshInterval || '30';
                document.getElementById('energy-unit').value = settings.preferences.energyUnit || 'kWh';
                document.getElementById('temperature-unit').value = settings.preferences.temperatureUnit || 'celsius';
                document.getElementById('currency').value = settings.preferences.currency || 'MYR';
            }
        }

        function setupSettingsListeners() {
            // Settings form submission
            document.getElementById('settings-form').addEventListener('submit', function(e) {
                e.preventDefault();
                saveSettings();
            });

            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all tabs and tab buttons
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Get tab ID from text content
                    const tabName = this.querySelector('span').textContent.toLowerCase().replace(' ', '-');
                    const tabId = tabName + '-tab';
                    document.getElementById(tabId).classList.add('active');
                });
            });
        }

        function switchTab(tabName) {
            // Remove active class from all tabs and tab buttons
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Add active class to target tab
            const tabBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn =>
                btn.textContent.toLowerCase().includes(tabName)
            );
            if (tabBtn) tabBtn.classList.add('active');

            document.getElementById(tabName + '-tab').classList.add('active');
        }

        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('button');
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function saveSettings() {
            const settings = {
                fullname: document.getElementById('settings-fullname').value,
                email: document.getElementById('settings-email').value,
                phone: document.getElementById('settings-phone').value,
                company: document.getElementById('settings-company').value,
                bio: document.getElementById('settings-bio').value,
                timezone: document.getElementById('settings-timezone').value,
                language: document.getElementById('settings-language').value,
                notifications: {
                    systemAlerts: document.getElementById('system-alerts').checked,
                    energyReports: document.getElementById('energy-reports').checked,
                    maintenanceAlerts: document.getElementById('maintenance-alerts').checked,
                    realtimeAlerts: document.getElementById('realtime-alerts').checked,
                    thresholdAlerts: document.getElementById('threshold-alerts').checked
                },
                preferences: {
                    dashboardView: document.getElementById('dashboard-view').value,
                    refreshInterval: document.getElementById('refresh-interval').value,
                    energyUnit: document.getElementById('energy-unit').value,
                    temperatureUnit: document.getElementById('temperature-unit').value,
                    currency: document.getElementById('currency').value
                }
            };

            // Save to localStorage
            localStorage.setItem('userSettings', JSON.stringify(settings));

            // Update user data in localStorage
            const user = JSON.parse(localStorage.getItem("user") || "{}");
            user.name = settings.fullname;
            user.user_email = settings.email;
            user.phone = settings.phone;
            user.company = settings.company;
            user.bio = settings.bio;
            localStorage.setItem("user", JSON.stringify(user));

            // Update UI
            populateSettingsData(user);

            Swal.fire({
                icon: 'success',
                title: 'Settings Saved!',
                text: 'Your settings have been updated successfully.',
                timer: 3000,
                showConfirmButton: false
            });
        }

        function resetSettings() {
            Swal.fire({
                title: 'Reset Settings?',
                text: 'This will reset all settings to their default values.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear settings
                    localStorage.removeItem('userSettings');

                    // Reload page
                    location.reload();
                }
            });
        }

        function changePassword() {
            const currentPass = document.getElementById('current-password').value;
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-password').value;

            if (!currentPass || !newPass || !confirmPass) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all password fields.',
                });
                return;
            }

            if (newPass.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Password must be at least 8 characters long.',
                });
                return;
            }

            if (newPass !== confirmPass) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'New passwords do not match.',
                });
                return;
            }

            Swal.fire({
                title: 'Change Password?',
                text: 'Your password will be updated.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // In real app, make API call here
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated!',
                        text: 'Your password has been changed successfully.',
                    }).then(() => {
                        // Clear password fields
                        document.getElementById('current-password').value = '';
                        document.getElementById('new-password').value = '';
                        document.getElementById('confirm-password').value = '';
                    });
                }
            });
        }

        function manage2FA() {
            Swal.fire({
                title: 'Two-Factor Authentication',
                html: `
                    <div class="text-left">
                        <p class="mb-4">Two-factor authentication is currently enabled.</p>
                        <div class="space-y-3">
                            <button onclick="disable2FA()" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                Disable 2FA
                            </button>
                            <button onclick="showBackupCodes()" class="w-full px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800">
                                View Backup Codes
                            </button>
                            <button onclick="setupNew2FA()" class="w-full px-4 py-2 border border-blue-600 text-blue-400 rounded-lg hover:bg-blue-900/30">
                                Setup New Device
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function revokeSession(button) {
            const sessionCard = button.closest('.bg-gray-800\\/50');
            Swal.fire({
                title: 'Revoke Session?',
                text: 'This will log out the selected device.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Revoke',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionCard.style.opacity = '0.5';
                    sessionCard.style.pointerEvents = 'none';
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check mr-1"></i> Revoked';

                    Swal.fire({
                        icon: 'success',
                        title: 'Session Revoked',
                        text: 'The selected session has been terminated.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function revokeAllSessions() {
            Swal.fire({
                title: 'Logout All Devices?',
                text: 'This will log you out from all devices except this one.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, logout all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sessions Revoked',
                        text: 'All other sessions have been terminated.',
                    });
                }
            });
        }

        function setNotificationFrequency(frequency) {
            document.querySelectorAll('#notifications-tab .grid button').forEach(btn => {
                btn.classList.remove('border-blue-600');
                btn.classList.add('border-gray-700');
            });

            const selectedBtn = document.querySelector(`#notifications-tab button[onclick*="${frequency}"]`);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-gray-700');
                selectedBtn.classList.add('border-blue-600');
            }

            Swal.fire({
                icon: 'success',
                title: 'Frequency Updated',
                text: `Notifications will be sent ${frequency === 'instant' ? 'immediately' : frequency === 'hourly' ? 'hourly' : 'daily'}.`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function saveNotificationSettings() {
            Swal.fire({
                icon: 'success',
                title: 'Notifications Saved!',
                text: 'Your notification settings have been updated.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function resetNotifications() {
            document.getElementById('system-alerts').checked = true;
            document.getElementById('energy-reports').checked = true;
            document.getElementById('maintenance-alerts').checked = true;
            document.getElementById('realtime-alerts').checked = true;
            document.getElementById('threshold-alerts').checked = true;

            Swal.fire({
                icon: 'success',
                title: 'Notifications Reset',
                text: 'Notification settings reset to default.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function setTheme(theme) {
            document.querySelectorAll('#preferences-tab .grid button').forEach(btn => {
                btn.classList.remove('border-blue-600');
                btn.classList.add('border-gray-700');
            });

            const selectedBtn = document.querySelector(`#preferences-tab button[onclick*="${theme}"]`);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-gray-700');
                selectedBtn.classList.add('border-blue-600');
            }

            localStorage.setItem('theme', theme);

            Swal.fire({
                icon: 'success',
                title: 'Theme Updated',
                text: `Theme set to ${theme}.`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function savePreferences() {
            Swal.fire({
                icon: 'success',
                title: 'Preferences Saved!',
                text: 'Your preferences have been updated.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function resetPreferences() {
            document.getElementById('dashboard-view').value = 'overview';
            document.getElementById('refresh-interval').value = '30';
            document.getElementById('energy-unit').value = 'kWh';
            document.getElementById('temperature-unit').value = 'celsius';
            document.getElementById('currency').value = 'MYR';

            Swal.fire({
                icon: 'success',
                title: 'Preferences Reset',
                text: 'All preferences reset to default.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function generateNewAPIKey() {
            Swal.fire({
                title: 'Generate New API Key',
                input: 'text',
                inputLabel: 'Key Name',
                inputPlaceholder: 'Enter a name for this key',
                showCancelButton: true,
                confirmButtonText: 'Generate',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please enter a name for the API key';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Generate fake API key (in real app, this would come from server)
                    const newKey = 'sk_live_' + Array.from({
                            length: 32
                        }, () =>
                        '0123456789abcdef' [Math.floor(Math.random() * 16)]
                    ).join('');

                    Swal.fire({
                        title: 'API Key Generated!',
                        html: `
                            <div class="text-left">
                                <p class="mb-2">Key: <strong>${result.value}</strong></p>
                                <p class="mb-4 text-gray-400 text-sm">Save this key somewhere safe. You won't be able to see it again.</p>
                                <div class="bg-gray-900 p-4 rounded-lg mb-4">
                                    <code class="text-sm font-mono text-white break-all">${newKey}</code>
                                </div>
                                <button onclick="navigator.clipboard.writeText('${newKey}')" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                    <i class="fas fa-copy mr-2"></i> Copy to Clipboard
                                </button>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                }
            });
        }

        function copyAPIKey(button) {
            const apiKey = button.parentElement.querySelector('code').textContent;
            navigator.clipboard.writeText(apiKey).then(() => {
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('bg-gray-700');
                button.classList.add('bg-green-600');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-gray-700');
                }, 2000);
            });
        }

        function revokeAPIKey(button) {
            Swal.fire({
                title: 'Revoke API Key?',
                text: 'This key will no longer be able to access the API.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, revoke it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const keyCard = button.closest('.bg-gray-800\\/50');
                    keyCard.style.opacity = '0.5';
                    keyCard.style.pointerEvents = 'none';

                    Swal.fire({
                        icon: 'success',
                        title: 'Key Revoked',
                        text: 'The API key has been revoked.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function deleteAccount() {
            Swal.fire({
                title: 'Delete Your Account?',
                html: `
                    <div class="text-left">
                        <p class="mb-4 text-red-400 font-bold">This action cannot be undone!</p>
                        <p class="mb-4">All your data will be permanently deleted. This includes:</p>
                        <ul class="list-disc pl-5 space-y-2 mb-6">
                            <li>Your profile information</li>
                            <li>Energy monitoring history</li>
                            <li>All settings and preferences</li>
                            <li>API keys and configurations</li>
                        </ul>
                        <p class="mb-4">Please type <strong>DELETE</strong> to confirm:</p>
                        <input type="text" id="confirmDelete" class="swal2-input" placeholder="Type DELETE here">
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete Account',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const confirmInput = document.getElementById('confirmDelete');
                    if (confirmInput.value !== 'DELETE') {
                        Swal.showValidationMessage('Please type DELETE to confirm');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // In a real app, send delete request to API
                    Swal.fire({
                        icon: 'success',
                        title: 'Account Deleted',
                        text: 'Your account has been successfully deleted.',
                    }).then(() => {
                        // Clear session and redirect to home
                        if (typeof clearSession === 'function') {
                            clearSession();
                        } else {
                            localStorage.clear();
                            window.location.href = '/';
                        }
                    });
                }
            });
        }

        function exportAllData() {
            Swal.fire({
                title: 'Exporting Data...',
                text: 'Preparing your data for download.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();

                    // Simulate data export
                    setTimeout(() => {
                        const user = JSON.parse(localStorage.getItem("user") || "{}");
                        const settings = JSON.parse(localStorage.getItem("userSettings") || "{}");

                        const exportData = {
                            user: user,
                            settings: settings,
                            exportDate: new Date().toISOString(),
                            version: '1.0'
                        };

                        const dataStr = JSON.stringify(exportData, null, 2);
                        const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);

                        const exportFileDefaultName = `energy-monitor-data-${new Date().toISOString().split('T')[0]}.json`;

                        Swal.fire({
                            icon: 'success',
                            title: 'Data Exported!',
                            html: `
                                <p class="mb-4">Your data has been prepared for download.</p>
                                <a href="${dataUri}" download="${exportFileDefaultName}" 
                                   class="inline-block px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                                    <i class="fas fa-download mr-2"></i> Download JSON File
                                </a>
                            `,
                            showConfirmButton: false,
                            showCloseButton: true
                        });
                    }, 2000);
                }
            });
        }

        function resetAllSettings() {
            Swal.fire({
                title: 'Reset All Settings?',
                text: 'All your settings will be restored to their default values.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('userSettings');
                    location.reload();
                }
            });
        }
    </script>
</body>

</html>