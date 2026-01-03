// auth.js - Updated to use proxy
const API_BASE = window.location.origin; // Your domain
const PROXY_ENDPOINT = "/proxy.php"; // Your proxy file

// FIXED requireAuth - Only clears session when absolutely necessary
async function requireAuth(role = null) {
  try {
    const token = localStorage.getItem("jwt");
    const user = localStorage.getItem("user");

    console.log("requireAuth: Checking token and user...");

    // Basic check - if no token/user, redirect to login
    if (!token || !user) {
      console.log("requireAuth: No token or user found");
      // Don't call clearSession here - just return false
      // Let the caller decide what to do
      return false;
    }

    // Parse user data
    let userObj;
    try {
      userObj = JSON.parse(user);
    } catch (e) {
      console.error("requireAuth: Failed to parse user data");
      return false;
    }

    // Check token expiration using isExpired function
    if (typeof isExpired === "function") {
      if (isExpired(token)) {
        console.log("requireAuth: Token expired, attempting refresh...");

        if (typeof refreshToken === "function") {
          const ok = await refreshToken();
          if (!ok) {
            console.log("requireAuth: Token refresh failed");
            // Only clear session if refresh fails
            clearSession();
            return false;
          }
          console.log("requireAuth: Token refreshed successfully");

          // Update user object after refresh
          const updatedUser = localStorage.getItem("user");
          if (updatedUser) {
            try {
              userObj = JSON.parse(updatedUser);
            } catch (e) {
              console.error("requireAuth: Failed to parse updated user data");
            }
          }
        } else {
          console.log("requireAuth: refreshToken function not available");
          // Don't clear session just because we can't refresh
          // The token might still be valid
          return true;
        }
      } else {
        console.log("requireAuth: Token is valid (not expired)");
      }
    } else {
      console.log(
        "requireAuth: isExpired function not available, skipping expiration check"
      );
    }

    // Check role if specified
    if (role && userObj.user_tipe !== role) {
      console.log(
        `requireAuth: Role mismatch. Required: ${role}, User has: ${userObj.user_tipe}`
      );
      // Don't clear session for role mismatch - just return false
      // User might want to navigate to a different page
      return false;
    }

    console.log("requireAuth: User authenticated successfully");
    return true;
  } catch (error) {
    console.error("requireAuth error:", error);
    // Don't clear session on unexpected errors
    // Could be network issues, etc.
    return false;
  }
}

// Also add this function to check login state
function isLoggedIn() {
  try {
    const token = localStorage.getItem("jwt");
    const user = localStorage.getItem("user");

    if (!token || !user) return false;

    // Quick check without validation
    return true;
  } catch {
    return false;
  }
}

/* ========= SESSION MANAGEMENT ========= */
function saveSession(token, user) {
  localStorage.setItem("jwt", token);
  localStorage.setItem("user", JSON.stringify(user));
  localStorage.setItem("lastLogin", new Date().toISOString());

  // Store expiration time (24 hours from now)
  const expiresAt = Date.now() + 24 * 60 * 60 * 1000;
  localStorage.setItem("jwt_expires", expiresAt.toString());

  // Also set as cookie for compatibility
  document.cookie = `jwt=${token}; path=/; max-age=${
    7 * 24 * 60 * 60
  }; SameSite=Lax`;
  document.cookie = `user_id=${user.id}; path=/; max-age=${
    7 * 24 * 60 * 60
  }; SameSite=Lax`;

  console.log(
    "Session saved, expires at:",
    new Date(expiresAt).toLocaleString()
  );
}

// FIXED clearSession - Only redirects if called from login page
function clearSession() {
  console.log("clearSession: Clearing user session...");

  // Remove localStorage items
  localStorage.removeItem("jwt");
  localStorage.removeItem("user");
  localStorage.removeItem("lastLogin");
  localStorage.removeItem("jwt_expires");

  // Keep remembered user (optional)
  // localStorage.removeItem("rememberedUser");

  // Clear cookies
  document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

  console.log("clearSession: Session cleared");

  // Only redirect if we're not already on the login page
  if (!window.location.pathname.includes("login")) {
    console.log("clearSession: Redirecting to login page");
    setTimeout(() => {
      window.location.href = "/auth/login.php";
    }, 500);
  }
}

/* ========= LOGIN FUNCTION (USING PROXY) ========= */
async function login(credentials) {
  try {
    const { username, password, rememberMe } = credentials;

    console.log("Attempting login via proxy...");

    // Use FormData for multipart/form-data
    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    // Make request to YOUR proxy
    const res = await fetch(PROXY_ENDPOINT, {
      method: "POST",
      body: formData, // FormData automatically sets Content-Type
      credentials: "same-origin", // Send cookies if any
    });

    console.log("Proxy response status:", res.status);

    // Check response
    if (!res.ok) {
      const errorText = await res.text();
      console.error("Login failed via proxy:", errorText);

      try {
        const errorData = JSON.parse(errorText);
        throw new Error(errorData.message || `Login failed: ${res.status}`);
      } catch {
        throw new Error(`Login failed: ${res.status} - Server error`);
      }
    }

    // Parse JSON response
    const data = await res.json();
    console.log("Login response:", data);

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
      let redirectUrl = "../dashboard-inverter.php";

      // Custom redirect logic
      switch (userType) {
        case "admin":
          redirectUrl = "../dashboard-inverter.php"; //'admin-dashboard.html';
          break;
        case "user":
          redirectUrl = "../dashboard-inverter.php"; //user-dashboard.html';
          break;
        default:
          redirectUrl = "../dashboard-inverter.php"; //'dashboard.html';
      }

      console.log("Redirecting to:", redirectUrl);
      window.location.href = redirectUrl;
    }, 1500);

    return data;
  } catch (err) {
    console.error("Login error:", err);

    // User-friendly error messages
    let errorMessage = err.message;
    if (
      err.message.includes("Network") ||
      err.message.includes("Failed to fetch")
    ) {
      errorMessage =
        "Cannot connect to server. Please check your internet connection.";
    } else if (err.message.includes("CORS")) {
      errorMessage =
        "Cross-origin request blocked. Please contact administrator.";
    }

    Swal.fire({
      icon: "error",
      title: "Login Failed",
      text: errorMessage,
      confirmButtonText: "Try Again",
    });

    throw err;
  }
}

/* ========= AUTH CHECK ========= */
function checkAuth() {
  const token = localStorage.getItem("jwt");
  const user = localStorage.getItem("user");

  console.log("checkAuth: Checking token and user...");
  console.log("Token exists:", !!token);
  console.log("User exists:", !!user);

  if (!token || !user) {
    console.log("checkAuth: Missing token or user data");
    return false;
  }

  try {
    const userObj = JSON.parse(user);
    console.log("checkAuth: User parsed successfully:", userObj.username);

    // Check expiration using stored timestamp (if available)
    const expiresAt = localStorage.getItem("jwt_expires");
    if (expiresAt) {
      const expirationTime = parseInt(expiresAt);
      const now = Date.now();

      if (now > expirationTime) {
        console.log("checkAuth: Token expired based on stored timestamp");
        // Don't clear here - let requireAuth handle refresh
        return false;
      } else {
        console.log("checkAuth: Token is valid (not expired)");
        return true;
      }
    }

    // If no expiration timestamp, return true (let requireAuth handle JWT parsing)
    console.log("checkAuth: No expiration timestamp, assuming valid");
    return true;
  } catch (error) {
    console.error("checkAuth: Error parsing user data:", error);
    return false;
  }
}

/* ========= INITIALIZE ========= */
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOMContentLoaded");

  // Check if user is already logged in
  if (checkAuth() && !window.location.pathname.includes("login")) {
    const user = JSON.parse(localStorage.getItem("user"));
    console.log("User already logged in:", user.username);
    // You could auto-redirect here if needed
  }

  // Initialize login page UI
  initLoginUI();
});

// FIXED initLoginUI - Only redirects if token is valid
function initLoginUI() {
  // Only run on login page
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return;

  console.log("Initializing login UI...");

  // Check if already logged in
  if (isLoggedIn()) {
    console.log("User already logged in, checking token...");

    // First check with simple validation
    const token = localStorage.getItem("jwt");
    const userStr = localStorage.getItem("user");

    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);

        // Check expiration using timestamp first (less aggressive)
        const expiresAt = localStorage.getItem("jwt_expires");
        let shouldRedirect = false;

        if (expiresAt) {
          const expirationTime = parseInt(expiresAt);
          const now = Date.now();

          if (now <= expirationTime) {
            console.log("Token not expired, redirecting...");
            shouldRedirect = true;
          } else if (typeof isExpired === "function" && !isExpired(token)) {
            // Double-check with JWT parsing
            console.log("Token still valid (JWT check), redirecting...");
            shouldRedirect = true;
          }
        } else {
          // No expiration stored, be cautious
          console.log("No expiration timestamp, proceeding cautiously");
        }

        if (shouldRedirect) {
          redirectLoggedInUser();
          return; // Stop further initialization
        }
      } catch (e) {
        console.error("Error checking logged in status:", e);
      }
    }
  }

  // Password toggle
  const togglePasswordBtn = document.getElementById("togglePassword");
  if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener("click", function () {
      const passwordInput = document.getElementById("password");
      const eyeIcon = this.querySelector("i");

      if (passwordInput && eyeIcon) {
        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          eyeIcon.className = "fas fa-eye-slash";
        } else {
          passwordInput.type = "password";
          eyeIcon.className = "fas fa-eye";
        }
      }
    });
  }

  // Add this helper function
  function redirectLoggedInUser() {
    try {
      const user = JSON.parse(localStorage.getItem("user") || "{}");
      const userType = user.user_tipe || "";
      let redirectUrl = "/dashboard-inverter.php";

      if (userType.toUpperCase() === "ADMIN") {
        redirectUrl = "/dashboard-inverter.php"; // "/admin/dashboard.php";
      } else if (userType.toUpperCase() === "USER") {
        redirectUrl = "/dashboard-inverter.php"; // "/user/dashboard.php";
      }

      console.log("Auto-redirecting logged-in user to:", redirectUrl);

      // Show message before redirect
      Swal.fire({
        icon: "info",
        title: "Already Logged In",
        text: `Redirecting to dashboard...`,
        timer: 1500,
        showConfirmButton: false,
      });

      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 1500);
    } catch (error) {
      console.error("Error in redirectLoggedInUser:", error);
    }
  }

  // Form submission
  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const username = document.getElementById("username")?.value.trim();
    const password = document.getElementById("password")?.value;
    const rememberMe = document.getElementById("remember")?.checked;

    // Validation
    if (!username || !password) {
      Swal.fire({
        icon: "error",
        title: "Missing Fields",
        text: "Please enter both username and password",
      });
      return;
    }

    if (password.length < 6) {
      Swal.fire({
        icon: "error",
        title: "Invalid Password",
        text: "Password must be at least 6 characters",
      });
      return;
    }

    // Show loading state
    const loginButton = document.getElementById("loginButton");
    if (loginButton) {
      const originalText = loginButton.innerHTML;
      loginButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Signing In...';
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
  const rememberedUser = localStorage.getItem("rememberedUser");
  if (rememberedUser) {
    const usernameInput = document.getElementById("username");
    const rememberCheckbox = document.getElementById("remember");
    if (usernameInput) usernameInput.value = rememberedUser;
    if (rememberCheckbox) rememberCheckbox.checked = true;
  }

  // Add focus effects
  const inputs = document.querySelectorAll(".form-input");
  inputs.forEach((input) => {
    if (!input) return;

    input.addEventListener("focus", function () {
      const icon = this.parentElement.querySelector(".input-icon");
      if (icon) {
        icon.style.color = "#667eea";
      }
    });

    input.addEventListener("blur", function () {
      const icon = this.parentElement.querySelector(".input-icon");
      if (icon) {
        icon.style.color = "#a0aec0";
      }
    });
  });

  console.log("Login UI initialized");
}
