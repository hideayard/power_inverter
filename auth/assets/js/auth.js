// auth.js - Updated to use proxy
const API_BASE = window.location.origin; // Your domain
const PROXY_ENDPOINT = '/proxy.php'; // Your proxy file

/* ========= SESSION MANAGEMENT ========= */
function saveSession(token, user) {
    localStorage.setItem("jwt", token);
    localStorage.setItem("user", JSON.stringify(user));
    localStorage.setItem("lastLogin", new Date().toISOString());
    
    // Also set as cookie for compatibility
    document.cookie = `jwt=${token}; path=/; max-age=${7 * 24 * 60 * 60}; SameSite=Lax`;
    document.cookie = `user_id=${user.id}; path=/; max-age=${7 * 24 * 60 * 60}; SameSite=Lax`;
}

function clearSession() {
    localStorage.removeItem("jwt");
    localStorage.removeItem("user");
    localStorage.removeItem("lastLogin");
    localStorage.removeItem("rememberedUser");
    
    // Clear cookies
    document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    
    window.location.href = 'login.php';
}

/* ========= LOGIN FUNCTION (USING PROXY) ========= */
async function login(credentials) {
    try {
        const { username, password, rememberMe } = credentials;
        
        console.log('Attempting login via proxy...');
        
        // Use FormData for multipart/form-data
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        
        // Make request to YOUR proxy
        const res = await fetch(PROXY_ENDPOINT, {
            method: 'POST',
            body: formData, // FormData automatically sets Content-Type
            credentials: 'same-origin', // Send cookies if any
        });
        
        console.log('Proxy response status:', res.status);
        
        // Check response
        if (!res.ok) {
            const errorText = await res.text();
            console.error('Login failed via proxy:', errorText);
            
            try {
                const errorData = JSON.parse(errorText);
                throw new Error(errorData.message || `Login failed: ${res.status}`);
            } catch {
                throw new Error(`Login failed: ${res.status} - Server error`);
            }
        }
        
        // Parse JSON response
        const data = await res.json();
        console.log('Login response:', data);
        
        if (!data.success) {
            throw new Error(data.message || "Invalid username or password");
        }
        
        // ✅ Save session
        saveSession(data.token, data.user);
        
        // ✅ Save remembered user
        if (rememberMe) {
            localStorage.setItem("rememberedUser", username);
        } else {
            localStorage.removeItem("rememberedUser");
        }
        
        // ✅ Show success
        Swal.fire({
            icon: "success",
            title: "Login Successful",
            text: "Welcome! Redirecting to dashboard...",
            timer: 1500,
            showConfirmButton: false,
        });
        
        // ✅ Redirect based on user type
        setTimeout(() => {
            const userType = data.user?.user_tipe;
            let redirectUrl = 'dashboard.html';
            
            // Custom redirect logic
            switch(userType) {
                case 'admin':
                    redirectUrl = 'admin-dashboard.html';
                    break;
                case 'user':
                    redirectUrl = 'user-dashboard.html';
                    break;
                default:
                    redirectUrl = 'dashboard.html';
            }
            
            console.log('Redirecting to:', redirectUrl);
            window.location.href = redirectUrl;
        }, 1500);
        
        return data;
        
    } catch (err) {
        console.error('Login error:', err);
        
        // User-friendly error messages
        let errorMessage = err.message;
        if (err.message.includes('Network') || err.message.includes('Failed to fetch')) {
            errorMessage = 'Cannot connect to server. Please check your internet connection.';
        } else if (err.message.includes('CORS')) {
            errorMessage = 'Cross-origin request blocked. Please contact administrator.';
        }
        
        Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: errorMessage,
            confirmButtonText: 'Try Again',
        });
        
        throw err;
    }
}

/* ========= AUTH CHECK ========= */
function checkAuth() {
    const token = localStorage.getItem('jwt');
    const user = localStorage.getItem('user');
    
    if (!token || !user) {
        return false;
    }
    
    // Optional: Check token expiration
    const lastLogin = localStorage.getItem('lastLogin');
    if (lastLogin) {
        const loginTime = new Date(lastLogin);
        const now = new Date();
        const hoursDiff = (now - loginTime) / (1000 * 60 * 60);
        
        // If more than 24 hours, require re-login
        if (hoursDiff > 24) {
            clearSession();
            return false;
        }
    }
    
    return true;
}

/* ========= INITIALIZE ========= */
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in
    if (checkAuth() && !window.location.pathname.includes('login')) {
        const user = JSON.parse(localStorage.getItem('user'));
        console.log('User already logged in:', user.username);
        // You could auto-redirect here if needed
    }
    
    // Initialize login page UI
    initLoginUI();
});

function initLoginUI() {
    // Only run on login page
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    console.log('Initializing login UI...');
    
    // Password toggle
    const togglePasswordBtn = document.getElementById('togglePassword');
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');
            
            if (passwordInput && eyeIcon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.className = 'fas fa-eye';
                }
            }
        });
    }
    
    // Form submission
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const username = document.getElementById('username')?.value.trim();
        const password = document.getElementById('password')?.value;
        const rememberMe = document.getElementById('remember')?.checked;
        
        // Validation
        if (!username || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Fields',
                text: 'Please enter both username and password',
            });
            return;
        }
        
        if (password.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Password',
                text: 'Password must be at least 6 characters',
            });
            return;
        }
        
        // Show loading state
        const loginButton = document.getElementById('loginButton');
        if (loginButton) {
            const originalText = loginButton.innerHTML;
            loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            loginButton.disabled = true;
            
            try {
                await login({ username, password, rememberMe });
            } catch (error) {
                // Error is already shown by login function
                loginButton.innerHTML = originalText;
                loginButton.disabled = false;
            }
        }
    });
    
    // Auto-fill remembered user
    const rememberedUser = localStorage.getItem('rememberedUser');
    if (rememberedUser) {
        const usernameInput = document.getElementById('username');
        const rememberCheckbox = document.getElementById('remember');
        if (usernameInput) usernameInput.value = rememberedUser;
        if (rememberCheckbox) rememberCheckbox.checked = true;
    }
    
    // Add focus effects
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        if (!input) return;
        
        input.addEventListener('focus', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) {
                icon.style.color = '#667eea';
            }
        });
        
        input.addEventListener('blur', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) {
                icon.style.color = '#a0aec0';
            }
        });
    });
    
    console.log('Login UI initialized');
}