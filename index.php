<!DOCTYPE html>
<html>
<head>
  <title>Power Inverter</title>
  <script src="/auth/assets/js/auth.js"></script>
</head>
<body>
  <div id="loading" style="
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
    font-family: Arial, sans-serif;
  ">
    <div style="
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    "></div>
    <p>Checking authentication...</p>
  </div>

  <script>
  (async () => {
    try {
      console.log('Power Inverter: Checking authentication...');
      
      // First, check localStorage directly without waiting for auth.js
      const token = localStorage.getItem("jwt");
      const userStr = localStorage.getItem("user");
      
      if (!token || !userStr) {
        console.log('No token or user in localStorage, redirecting to login');
        location.replace("/auth/login.php");
        return;
      }
      
      let user;
      try {
        user = JSON.parse(userStr);
      } catch (e) {
        console.log('Invalid user data in localStorage');
        localStorage.clear();
        location.replace("/auth/login.php");
        return;
      }
      
      console.log('Found user in localStorage:', user.username);
      
      // Check if auth.js is loaded
      if (typeof isExpired === 'undefined') {
        console.log('Auth.js not loaded yet, waiting...');
        // If auth.js fails to load, still try to proceed
        setTimeout(() => {
          redirectUser(user);
        }, 1000);
        return;
      }
      
      // Check token expiration
      if (isExpired(token)) {
        console.log('Token expired, attempting refresh...');
        
        if (typeof refreshToken !== 'undefined') {
          const ok = await refreshToken();
          if (!ok) {
            console.log('Token refresh failed');
            if (typeof clearSession !== 'undefined') {
              clearSession();
            } else {
              localStorage.clear();
            }
            location.replace("/auth/login.php");
            return;
          }
          console.log('Token refreshed successfully');
          
          // Get updated user data after refresh
          const updatedUserStr = localStorage.getItem("user");
          if (updatedUserStr) {
            try {
              user = JSON.parse(updatedUserStr);
            } catch (e) {
              console.error('Failed to parse updated user data');
            }
          }
        } else {
          console.log('refreshToken function not available');
        }
      }
      
      // Redirect user based on role
      redirectUser(user);
      
    } catch (error) {
      console.error('Auth check error:', error);
      location.replace("/auth/login.php");
    }
    
    function redirectUser(user) {
      let redirectUrl = '/dashboard-inverter.php'; // Default
      
      if (user.user_tipe === "ADMIN") {
        redirectUrl = '/dashboard-inverter.php'; ///'admin/dashboard.php';
      } else if (user.user_tipe === "USER") {
        redirectUrl = '/dashboard-inverter.php'; //'/user/dashboard.php';
      }
      
      console.log('Redirecting to:', redirectUrl);
      
      // Small delay for UX
      setTimeout(() => {
        location.replace(redirectUrl);
      }, 300);
    }
  })();

  // CSS for spinner
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
  </script>
</body>
</html>