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
        
        .tab-active {
            border-bottom: 3px solid #667eea;
            color: #667eea;
            font-weight: 600;
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
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                                <span id="profile-initials">JD</span>
                            </div>
                            <button id="change-photo-btn" class="absolute bottom-2 right-2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                            <input type="file" id="photo-upload" class="hidden" accept="image/*">
                        </div>
                        
                        <!-- User Info -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 id="profile-name" class="text-2xl font-bold text-white">John Doe</h2>
                                    <p id="profile-role" class="text-blue-400 font-medium">Administrator</p>
                                    <p id="profile-email" class="text-gray-400 mt-2">john.doe@example.com</p>
                                    <p id="profile-joined" class="text-gray-500 text-sm mt-1">Member since: Jan 2024</p>
                                </div>
                                <button id="edit-profile-btn" class="edit-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
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
                                            <p class="text-sm text-gray-400">Active Days</p>
                                            <p class="text-xl font-bold">45</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-chart-line text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Nodes Monitored</p>
                                            <p class="text-xl font-bold">8</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-bolt text-purple-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Total Energy</p>
                                            <p class="text-xl font-bold">12.5k kWh</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-card bg-gray-800/50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-leaf text-yellow-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">CO₂ Reduced</p>
                                            <p class="text-xl font-bold">8.2t</p>
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

                <!-- Personal Info Form -->
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
                                       placeholder="Enter username" disabled>
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
                            
                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Location</label>
                                <input type="text" id="location" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Johor, Malaysia" disabled>
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
                            <span id="account-type" class="text-blue-400 font-medium">Premium</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">2FA Status</span>
                            <span class="text-yellow-400 font-medium">
                                <i class="fas fa-shield-alt mr-1"></i> Enabled
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Last Login</span>
                            <span class="text-gray-300">Today, 14:30</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <button id="upgrade-btn" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 rounded-lg font-medium">
                            <i class="fas fa-crown mr-2"></i> Upgrade to Pro
                        </button>
                    </div>
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
                        
                        <a href="help.php" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-question-circle text-purple-400"></i>
                            </div>
                            <div>
                                <p class="font-medium">Help Center</p>
                                <p class="text-sm text-gray-400">Get support</p>
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
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Enable two-factor authentication</p>
                        </div>
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

    <script>
        // Load user data from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            const token = localStorage.getItem("jwt");
            const user = JSON.parse(localStorage.getItem("user") || "{}");
            
            if (!token || !user) {
                window.location.href = "/auth/login.php";
                return;
            }
            
            // Populate profile data
            populateProfileData(user);
            
            // Setup event listeners
            setupEventListeners();
        });
        
        function populateProfileData(user) {
            // Generate initials
            const name = user.name || user.username || "User";
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
            
            // Set profile data
            document.getElementById('profile-initials').textContent = initials;
            document.getElementById('profile-name').textContent = name;
            document.getElementById('profile-role').textContent = user.user_tipe || "User";
            document.getElementById('profile-email').textContent = user.user_email || "email@example.com";
            document.getElementById('account-type').textContent = user.user_tipe === "ADMIN" ? "Administrator" : "Standard";
            
            // Set form values
            document.getElementById('full-name').value = name;
            document.getElementById('username').value = user.username || "";
            document.getElementById('email').value = user.user_email || "";
            document.getElementById('phone').value = user.phone || "+60 12-345 6789";
            document.getElementById('location').value = user.location || "Johor, Malaysia";
            document.getElementById('bio').value = user.bio || "Energy monitoring enthusiast focused on sustainable solutions.";
            
            // Set joined date (use registration date if available)
            const joinedDate = user.created_at ? new Date(user.created_at).toLocaleDateString('en-US', { 
                month: 'short', 
                year: 'numeric' 
            }) : "Jan 2024";
            document.getElementById('profile-joined').textContent = `Member since: ${joinedDate}`;
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
                const user = JSON.parse(localStorage.getItem("user") || "{}");
                populateProfileData(user);
            });
            
            // Save Button
            document.getElementById('profile-form').addEventListener('submit', function(e) {
                e.preventDefault();
                saveProfileChanges();
            });
            
            // Tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    document.querySelectorAll('[id^="tab-"]').forEach(t => {
                        t.classList.remove('tab-active');
                        t.classList.add('text-gray-400', 'hover:text-white');
                    });
                    
                    // Add active class to clicked tab
                    this.classList.add('tab-active');
                    this.classList.remove('text-gray-400', 'hover:text-white');
                    
                    // Show corresponding content (you can implement this if needed)
                });
            });
            
            // Change Photo Button
            document.getElementById('change-photo-btn').addEventListener('click', function() {
                document.getElementById('photo-upload').click();
            });
            
            // Photo Upload
            document.getElementById('photo-upload').addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            // In a real app, you would upload this to your server
                            Swal.fire({
                                icon: 'info',
                                title: 'Photo Upload',
                                text: 'In a real application, this would upload your photo to the server.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        };
                        reader.readAsDataURL(file);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File',
                            text: 'Please select an image file.',
                        });
                    }
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
                                <li>Unlimited nodes monitoring</li>
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
        
        function enableFormEditing(enable) {
            const formInputs = document.querySelectorAll('#profile-form input, #profile-form textarea');
            const formActions = document.getElementById('form-actions');
            const editBtn = document.getElementById('edit-profile-btn');
            
            if (enable) {
                formInputs.forEach(input => {
                    input.disabled = false;
                    input.classList.add('bg-gray-700');
                });
                formActions.style.display = 'flex';
                editBtn.style.display = 'none';
            } else {
                formInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.remove('bg-gray-700');
                });
                formActions.style.display = 'none';
                editBtn.style.display = 'block';
            }
        }
        
        function saveProfileChanges() {
            const formData = {
                fullName: document.getElementById('full-name').value,
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                location: document.getElementById('location').value,
                bio: document.getElementById('bio').value
            };
            
            // Show loading
            const saveBtn = document.getElementById('save-btn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            saveBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // In a real app, you would make an API call here
                // For now, just update the UI
                document.getElementById('profile-name').textContent = formData.fullName;
                document.getElementById('profile-email').textContent = formData.email;
                
                // Update localStorage (simulated)
                const user = JSON.parse(localStorage.getItem("user") || "{}");
                user.name = formData.fullName;
                user.username = formData.username;
                user.user_email = formData.email;
                localStorage.setItem("user", JSON.stringify(user));
                
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
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }, 1500);
        }
    </script>
</body>
</html>