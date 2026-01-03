<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - Energy Monitoring System</title>
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
        
        .help-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 20px;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }
        
        .help-card {
            transition: all 0.3s ease;
        }
        
        .help-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border-color: #667eea;
        }
        
        input, textarea, select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #667eea;
            background-color: rgba(255, 255, 255, 0.08);
        }
        
        .search-input {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .search-input:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #667eea;
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
                    <a href="settings.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                    <a href="help.php" class="text-blue-400 hover:text-blue-300 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-question-circle mr-1"></i> Help
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Help & Support</h1>
            <p class="text-gray-400 mt-2">Find answers to common questions or contact our support team</p>
        </div>

        <!-- Search Bar -->
        <div class="glass-card p-6 mb-8">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchHelp" 
                       class="w-full pl-12 pr-4 py-4 search-input rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Search for help topics, guides, or troubleshooting...">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Quick Help -->
            <div class="lg:col-span-2">
                <!-- Quick Help Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="help-card glass-card p-6 cursor-pointer" onclick="showFAQ('getting-started')">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-rocket text-2xl text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Getting Started</h3>
                                <p class="text-sm text-gray-400">Learn the basics</p>
                            </div>
                        </div>
                        <p class="text-gray-300">Learn how to set up your energy monitoring system and get started with basic features.</p>
                    </div>
                    
                    <div class="help-card glass-card p-6 cursor-pointer" onclick="showFAQ('troubleshooting')">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-red-900/30 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-tools text-2xl text-red-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Troubleshooting</h3>
                                <p class="text-sm text-gray-400">Solve common issues</p>
                            </div>
                        </div>
                        <p class="text-gray-300">Find solutions to common problems and technical issues with your system.</p>
                    </div>
                    
                    <div class="help-card glass-card p-6 cursor-pointer" onclick="showFAQ('billing')">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-credit-card text-2xl text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Billing & Plans</h3>
                                <p class="text-sm text-gray-400">Payment questions</p>
                            </div>
                        </div>
                        <p class="text-gray-300">Information about pricing, billing cycles, and subscription plans.</p>
                    </div>
                    
                    <div class="help-card glass-card p-6 cursor-pointer" onclick="showFAQ('api')">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-code text-2xl text-purple-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">API & Integration</h3>
                                <p class="text-sm text-gray-400">Developer resources</p>
                            </div>
                        </div>
                        <p class="text-gray-300">Documentation and guides for API integration and development.</p>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="glass-card p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-question-circle mr-3 text-yellow-400"></i>
                            Frequently Asked Questions
                        </h2>
                        <button onclick="showAllFAQs()" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                            View All FAQs →
                        </button>
                    </div>
                    
                    <div class="faq-list space-y-4" id="faqList">
                        <!-- FAQ items will be loaded here -->
                    </div>
                </div>

                <!-- Contact Support Form -->
                <div class="glass-card p-6">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-headset mr-3 text-green-400"></i>
                        Contact Support
                    </h2>
                    
                    <form id="supportForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                                <input type="text" id="supportName" 
                                       class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your name" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                                <input type="email" id="supportEmail" 
                                       class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your email" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Issue Category</label>
                                <select id="supportCategory" 
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Select category</option>
                                    <option value="technical">Technical Issue</option>
                                    <option value="billing">Billing & Payment</option>
                                    <option value="account">Account Issue</option>
                                    <option value="feature">Feature Request</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Priority</label>
                                <select id="supportPriority" 
                                        class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Subject</label>
                                <input type="text" id="supportSubject" 
                                       class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Brief description of your issue" required>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                                <textarea id="supportMessage" rows="4"
                                          class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                          placeholder="Describe your issue in detail..." required></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Attachments (Optional)</label>
                                <div class="border-2 border-dashed border-gray-700 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-400 mb-2">Drag & drop files here or click to browse</p>
                                    <p class="text-xs text-gray-500">Maximum file size: 10MB each</p>
                                    <input type="file" id="supportAttachment" class="hidden" multiple>
                                    <button type="button" onclick="document.getElementById('supportAttachment').click()" 
                                            class="mt-2 px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 text-sm">
                                        <i class="fas fa-plus mr-1"></i> Add Files
                                    </button>
                                </div>
                                <div id="fileList" class="mt-2 space-y-2"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4 mt-8">
                            <button type="reset" 
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 font-medium">
                                Clear Form
                            </button>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                <i class="fas fa-paper-plane mr-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Support Resources -->
            <div class="lg:col-span-1">
                <!-- Support Channels -->
                <div class="glass-card p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-4">Support Channels</h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-gray-800/50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-envelope text-blue-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">Email Support</p>
                                <p class="text-sm text-gray-400">support@energymonitor.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-800/50 rounded-lg">
                            <div class="w-10 h-10 bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-phone text-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">Phone Support</p>
                                <p class="text-sm text-gray-400">+60 3-1234 5678</p>
                                <p class="text-xs text-gray-500">Mon-Fri: 9AM-6PM (MYT)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-800/50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-comments text-purple-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">Live Chat</p>
                                <p class="text-sm text-gray-400">Available 24/7</p>
                                <button onclick="startLiveChat()" class="mt-1 text-sm text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-comment-dots mr-1"></i> Start Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentation Links -->
                <div class="glass-card p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-4">Documentation</h3>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-book text-blue-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">User Guide</p>
                                <p class="text-sm text-gray-400">Complete user manual</p>
                            </div>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-video text-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">Video Tutorials</p>
                                <p class="text-sm text-gray-400">Step-by-step guides</p>
                            </div>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-code text-purple-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">API Documentation</p>
                                <p class="text-sm text-gray-400">Developer resources</p>
                            </div>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-download text-red-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-white">Download Center</p>
                                <p class="text-sm text-gray-400">Software & drivers</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">API Status</span>
                            <span class="flex items-center text-green-400">
                                <i class="fas fa-circle text-xs mr-1"></i> Operational
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Database</span>
                            <span class="flex items-center text-green-400">
                                <i class="fas fa-circle text-xs mr-1"></i> Normal
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Monitoring System</span>
                            <span class="flex items-center text-green-400">
                                <i class="fas fa-circle text-xs mr-1"></i> Online
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Support Response</span>
                            <span class="flex items-center text-yellow-400">
                                <i class="fas fa-circle text-xs mr-1"></i> Normal
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-sm text-gray-400">Last updated: Just now</p>
                        <button onclick="checkSystemStatus()" class="mt-2 text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh Status
                        </button>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="glass-card p-6 mt-6">
                    <h3 class="text-lg font-bold text-white mb-4">Quick Tips</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Check our FAQ before submitting a ticket</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Include error messages in your support request</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Attach screenshots for visual issues</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-400 mt-1 mr-3"></i>
                            <p class="text-sm text-gray-300">Provide steps to reproduce the issue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // FAQ data
        const faqData = {
            'getting-started': [
                {
                    question: 'How do I set up my energy monitoring system?',
                    answer: '1. Connect your monitoring devices to power sources. 2. Install the Energy Monitor app. 3. Follow the in-app setup wizard. 4. Connect devices to your network. 5. Configure monitoring parameters.'
                },
                {
                    question: 'What devices are compatible with Energy Monitor?',
                    answer: 'We support most smart meters, CT clamps, solar inverters, battery systems, and IoT energy sensors. Check our compatibility list for specific models.'
                },
                {
                    question: 'How do I add a new monitoring node?',
                    answer: 'Go to Dashboard → Add Node → Select device type → Follow pairing instructions → Configure settings.'
                }
            ],
            'troubleshooting': [
                {
                    question: 'My device is not connecting to the system',
                    answer: '1. Check power and network connections. 2. Restart the device. 3. Ensure firmware is updated. 4. Check network firewall settings. 5. Contact support if issue persists.'
                },
                {
                    question: 'Data is not updating in real-time',
                    answer: '1. Check internet connection. 2. Verify device is online. 3. Restart the monitoring service. 4. Clear browser cache. 5. Check for system updates.'
                },
                {
                    question: 'I\'m getting incorrect energy readings',
                    answer: '1. Calibrate your monitoring devices. 2. Check wiring connections. 3. Verify configuration settings. 4. Update device firmware. 5. Contact technical support.'
                }
            ],
            'billing': [
                {
                    question: 'How do I upgrade my subscription plan?',
                    answer: 'Go to Settings → Billing → Upgrade Plan → Select new plan → Confirm payment → Changes take effect immediately.'
                },
                {
                    question: 'Can I cancel my subscription anytime?',
                    answer: 'Yes, you can cancel anytime from Settings → Billing. Your access continues until the end of the billing period.'
                },
                {
                    question: 'How do I update my payment method?',
                    answer: 'Settings → Billing → Payment Methods → Add/Update card → Save changes.'
                }
            ],
            'api': [
                {
                    question: 'How do I get API access?',
                    answer: '1. Go to Settings → API Keys. 2. Generate a new API key. 3. Use the key in your requests. 4. Refer to API documentation for endpoints.'
                },
                {
                    question: 'What are the API rate limits?',
                    answer: 'Free plan: 100 requests/hour. Pro plan: 1,000 requests/hour. Enterprise: Custom limits. Check headers for remaining requests.'
                },
                {
                    question: 'How do I authenticate API requests?',
                    answer: 'Include your API key in the Authorization header: Authorization: Bearer YOUR_API_KEY'
                }
            ]
        };

        // Initialize help page
        document.addEventListener('DOMContentLoaded', async function() {
            // Check authentication
            const token = localStorage.getItem("jwt");
            const user = JSON.parse(localStorage.getItem("user") || "{}");
            
            if (!token || !user) {
                window.location.href = "/auth/login.php";
                return;
            }
            
            // Auto-fill user info
            autoFillUserInfo();
            
            // Load default FAQs
            loadFAQs('getting-started');
            
            // Setup event listeners
            setupHelpListeners();
        });
        
        function autoFillUserInfo() {
            try {
                const user = JSON.parse(localStorage.getItem("user") || "{}");
                document.getElementById('supportName').value = user.name || user.username || '';
                document.getElementById('supportEmail').value = user.user_email || '';
            } catch (error) {
                console.error('Error loading user data:', error);
            }
        }
        
        function loadFAQs(category) {
            const faqList = document.getElementById('faqList');
            const faqs = faqData[category] || faqData['getting-started'];
            
            faqList.innerHTML = '';
            
            faqs.forEach((faq, index) => {
                const faqItem = document.createElement('div');
                faqItem.className = 'faq-item bg-gray-800/50 rounded-lg overflow-hidden';
                faqItem.innerHTML = `
                    <div class="faq-question p-4 cursor-pointer flex justify-between items-center" onclick="toggleFAQ(${index}, this)">
                        <h4 class="font-medium text-white">${faq.question}</h4>
                        <i class="fas fa-plus faq-toggle text-gray-400"></i>
                    </div>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-4 border-t border-gray-700">
                            <p class="text-gray-300">${faq.answer}</p>
                        </div>
                    </div>
                `;
                faqList.appendChild(faqItem);
            });
        }
        
        function toggleFAQ(index, element) {
            const faqItem = element.closest('.faq-item');
            const answer = faqItem.querySelector('.faq-answer');
            const toggleIcon = faqItem.querySelector('.faq-toggle');
            
            // Close other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem && item.classList.contains('active')) {
                    item.classList.remove('active');
                    item.querySelector('.faq-answer').style.maxHeight = '0';
                    item.querySelector('.faq-answer').style.padding = '0';
                    item.querySelector('.faq-toggle').className = 'fas fa-plus faq-toggle text-gray-400';
                }
            });
            
            // Toggle current FAQ
            if (faqItem.classList.contains('active')) {
                faqItem.classList.remove('active');
                answer.style.maxHeight = '0';
                answer.style.padding = '0';
                toggleIcon.className = 'fas fa-plus faq-toggle text-gray-400';
            } else {
                faqItem.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.style.padding = '20px';
                toggleIcon.className = 'fas fa-plus faq-toggle text-gray-400 rotate-45';
            }
        }
        
        function showFAQ(category) {
            loadFAQs(category);
            
            // Scroll to FAQ section
            document.getElementById('faqList').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // Show notification
            const categoryNames = {
                'getting-started': 'Getting Started',
                'troubleshooting': 'Troubleshooting',
                'billing': 'Billing & Plans',
                'api': 'API & Integration'
            };
            
            Swal.fire({
                icon: 'info',
                title: categoryNames[category] || 'FAQs',
                text: `Showing FAQs for ${categoryNames[category]?.toLowerCase() || 'this category'}`,
                timer: 1500,
                showConfirmButton: false
            });
        }
        
        function showAllFAQs() {
            // Combine all FAQs
            const allFAQs = [];
            Object.values(faqData).forEach(category => {
                allFAQs.push(...category);
            });
            
            const faqList = document.getElementById('faqList');
            faqList.innerHTML = '';
            
            allFAQs.forEach((faq, index) => {
                const faqItem = document.createElement('div');
                faqItem.className = 'faq-item bg-gray-800/50 rounded-lg overflow-hidden';
                faqItem.innerHTML = `
                    <div class="faq-question p-4 cursor-pointer flex justify-between items-center" onclick="toggleFAQ(${index}, this)">
                        <h4 class="font-medium text-white">${faq.question}</h4>
                        <i class="fas fa-plus faq-toggle text-gray-400"></i>
                    </div>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-4 border-t border-gray-700">
                            <p class="text-gray-300">${faq.answer}</p>
                        </div>
                    </div>
                `;
                faqList.appendChild(faqItem);
            });
            
            Swal.fire({
                icon: 'success',
                title: 'All FAQs Loaded',
                text: 'Showing all available FAQs',
                timer: 1500,
                showConfirmButton: false
            });
        }
        
        function setupHelpListeners() {
            // Search functionality
            document.getElementById('searchHelp').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                
                if (searchTerm.length < 2) {
                    loadFAQs('getting-started');
                    return;
                }
                
                // Search across all FAQs
                const allFAQs = [];
                Object.values(faqData).forEach(category => {
                    allFAQs.push(...category);
                });
                
                const filteredFAQs = allFAQs.filter(faq => 
                    faq.question.toLowerCase().includes(searchTerm) || 
                    faq.answer.toLowerCase().includes(searchTerm)
                );
                
                if (filteredFAQs.length > 0) {
                    const faqList = document.getElementById('faqList');
                    faqList.innerHTML = '';
                    
                    filteredFAQs.forEach((faq, index) => {
                        const faqItem = document.createElement('div');
                        faqItem.className = 'faq-item bg-gray-800/50 rounded-lg overflow-hidden';
                        faqItem.innerHTML = `
                            <div class="faq-question p-4 cursor-pointer flex justify-between items-center" onclick="toggleFAQ(${index}, this)">
                                <h4 class="font-medium text-white">${faq.question}</h4>
                                <i class="fas fa-plus faq-toggle text-gray-400"></i>
                            </div>
                            <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                                <div class="p-4 border-t border-gray-700">
                                    <p class="text-gray-300">${faq.answer}</p>
                                </div>
                            </div>
                        `;
                        faqList.appendChild(faqItem);
                    });
                } else {
                    const faqList = document.getElementById('faqList');
                    faqList.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-search text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-400">No results found for "${searchTerm}"</p>
                            <p class="text-sm text-gray-500 mt-2">Try different keywords or contact support</p>
                        </div>
                    `;
                }
            });
            
            // Support form submission
            document.getElementById('supportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitSupportRequest();
            });
            
            // File attachment handling
            document.getElementById('supportAttachment').addEventListener('change', function(e) {
                const fileList = document.getElementById('fileList');
                fileList.innerHTML = '';
                
                Array.from(e.target.files).forEach((file, index) => {
                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: `${file.name} exceeds 10MB limit`,
                        });
                        return;
                    }
                    
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-gray-800/50 rounded';
                    fileItem.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                            <span class="text-sm text-gray-300 truncate">${file.name}</span>
                        </div>
                        <button type="button" onclick="removeFile(${index})" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
                });
            });
        }
        
        function removeFile(index) {
            const dt = new DataTransfer();
            const input = document.getElementById('supportAttachment');
            const files = Array.from(input.files);
            
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            input.files = dt.files;
            
            // Refresh file list display
            const event = new Event('change');
            input.dispatchEvent(event);
        }
        
        function startLiveChat() {
            Swal.fire({
                title: 'Live Chat Support',
                html: `
                    <div class="text-left">
                        <div class="mb-4 p-3 bg-gray-800/50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">Support Agent</p>
                                    <p class="text-xs text-gray-400">Connecting...</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chat-messages space-y-3 max-h-60 overflow-y-auto mb-4">
                            <div class="flex justify-start">
                                <div class="bg-gray-800/50 rounded-lg p-3 max-w-xs">
                                    <p class="text-white">Hello! How can I help you today?</p>
                                    <p class="text-xs text-gray-400 text-right mt-1">Just now</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <input type="text" id="chatMessage" placeholder="Type your message..." 
                                   class="flex-1 bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="sendChatMessage()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        
                        <div class="mt-4 text-center text-xs text-gray-500">
                            <p>Live chat is simulated for demonstration.</p>
                            <p>In production, this would connect to real support.</p>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                width: 500
            });
        }
        
        function sendChatMessage() {
            const messageInput = document.getElementById('chatMessage');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            const chatMessages = document.querySelector('.chat-messages');
            
            // Add user message
            const userMessage = document.createElement('div');
            userMessage.className = 'flex justify-end';
            userMessage.innerHTML = `
                <div class="bg-blue-600 rounded-lg p-3 max-w-xs">
                    <p class="text-white">${message}</p>
                    <p class="text-xs text-gray-300 text-right mt-1">Just now</p>
                </div>
            `;
            chatMessages.appendChild(userMessage);
            
            // Clear input
            messageInput.value = '';
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Simulate agent response
            setTimeout(() => {
                const agentMessage = document.createElement('div');
                agentMessage.className = 'flex justify-start';
                agentMessage.innerHTML = `
                    <div class="bg-gray-800/50 rounded-lg p-3 max-w-xs">
                        <p class="text-white">Thanks for your message. How can I assist you further?</p>
                        <p class="text-xs text-gray-400 text-right mt-1">Just now</p>
                    </div>
                `;
                chatMessages.appendChild(agentMessage);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 1000);
        }
        
        function checkSystemStatus() {
            Swal.fire({
                title: 'Checking System Status...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'All Systems Operational',
                            text: 'All services are running normally.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 1500);
                }
            });
        }
        
        function submitSupportRequest() {
            const formData = {
                name: document.getElementById('supportName').value,
                email: document.getElementById('supportEmail').value,
                category: document.getElementById('supportCategory').value,
                priority: document.getElementById('supportPriority').value,
                subject: document.getElementById('supportSubject').value,
                message: document.getElementById('supportMessage').value,
                attachments: document.getElementById('supportAttachment').files.length,
                timestamp: new Date().toISOString()
            };
            
            // Generate ticket ID
            const ticketId = 'TKT-' + Date.now().toString().slice(-8);
            
            Swal.fire({
                title: 'Submitting Support Request...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        // Store in localStorage (simulated)
                        const supportTickets = JSON.parse(localStorage.getItem('supportTickets') || '[]');
                        supportTickets.push({
                            ...formData,
                            ticketId: ticketId,
                            status: 'open'
                        });
                        localStorage.setItem('supportTickets', JSON.stringify(supportTickets));
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Support Request Submitted!',
                            html: `
                                <div class="text-left">
                                    <p class="mb-4">Thank you for contacting support.</p>
                                    <div class="bg-gray-800/50 p-4 rounded-lg mb-4">
                                        <p><strong>Ticket ID:</strong> <span class="text-blue-400">${ticketId}</span></p>
                                        <p><strong>Category:</strong> ${formData.category}</p>
                                        <p><strong>Priority:</strong> ${formData.priority}</p>
                                    </div>
                                    <p class="text-sm text-gray-400">We'll respond to <strong>${formData.email}</strong> within 24 hours.</p>
                                </div>
                            `,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reset form
                            document.getElementById('supportForm').reset();
                            document.getElementById('fileList').innerHTML = '';
                            autoFillUserInfo();
                        });
                    }, 2000);
                }
            });
        }
    </script>
</body>
</html>