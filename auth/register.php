<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Energy Monitoring System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/register.css">


</head>

<body>
  <div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <div class="register-container">
    <div class="logo-container">
      <div class="logo">
        <i class="fas fa-bolt"></i>
      </div>
      <h1 class="register-title">Create Account</h1>
      <p class="register-subtitle">Join our energy monitoring platform</p>
    </div>

    <form id="registerForm">
      <div class="name-group">
        <div class="form-group">
          <i class="fas fa-user input-icon"></i>
          <input type="text"
            id="firstName"
            class="form-input"
            placeholder="First name"
            required>
        </div>

        <div class="form-group">
          <i class="fas fa-user input-icon"></i>
          <input type="text"
            id="lastName"
            class="form-input"
            placeholder="Last name"
            required>
        </div>
      </div>

      <div class="form-group">
        <i class="fas fa-envelope input-icon"></i>
        <input type="email"
          id="email"
          class="form-input"
          placeholder="Enter your email"
          required>
      </div>

      <div class="form-group">
        <i class="fas fa-user input-icon"></i>
        <input type="text"
          id="username"
          class="form-input"
          placeholder="Choose a username"
          required>
      </div>

      <div class="form-group">
        <div class="password-container">
          <input type="password"
            id="password"
            class="form-input"
            placeholder="Create a password"
            required>
          <button type="button"
            class="toggle-password"
            id="togglePassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="password-strength">
          <div class="strength-bar" id="passwordStrength"></div>
        </div>
        <div class="password-hints" id="passwordHints">
          <ul>
            <li id="lengthHint"><i class="fas fa-circle"></i> At least 8 characters</li>
            <li id="uppercaseHint"><i class="fas fa-circle"></i> One uppercase letter</li>
            <li id="numberHint"><i class="fas fa-circle"></i> One number</li>
            <li id="specialHint"><i class="fas fa-circle"></i> One special character</li>
          </ul>
        </div>
      </div>

      <div class="form-group">
        <i class="fas fa-lock input-icon"></i>
        <div class="password-container">
          <input type="password"
            id="confirmPassword"
            class="form-input"
            placeholder="Confirm your password"
            required>
          <button type="button"
            class="toggle-password"
            id="toggleConfirmPassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div id="passwordMatch" style="font-size: 12px; margin-top: 8px;"></div>
      </div>

      <div class="terms-group">
        <input type="checkbox" id="terms" required>
        <label for="terms">
          I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>
        </label>
      </div>

      <button type="submit" class="register-button" id="registerButton">
        <i class="fas fa-user-plus"></i>
        Create Account
      </button>
    </form>

    <p class="login-link">
      Already have an account?
      <a href="login.php">Sign in here</a>
    </p>
  </div>

  <div class="copyright">
    © 2024 <a href="#">Energy Monitoring System</a>. All rights reserved.
    <br>
    <span style="font-size: 12px; opacity: 0.7;">
      Version 5.0 | Made with <i class="fas fa-heart" style="color: #ff6b6b;"></i> for a sustainable future
    </span>
  </div>

  <script src="../assets/js/auth.js"></script>
  <script>
    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });

    // Confirm password visibility toggle
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
      const confirmPasswordInput = document.getElementById('confirmPassword');
      const icon = this.querySelector('i');

      if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        confirmPasswordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
      const password = this.value;
      const strengthBar = document.getElementById('passwordStrength');
      const hints = {
        length: document.getElementById('lengthHint'),
        uppercase: document.getElementById('uppercaseHint'),
        number: document.getElementById('numberHint'),
        special: document.getElementById('specialHint')
      };

      let strength = 0;

      // Check password requirements
      const hasLength = password.length >= 8;
      const hasUppercase = /[A-Z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

      // Update hints
      updateHint(hints.length, hasLength);
      updateHint(hints.uppercase, hasUppercase);
      updateHint(hints.number, hasNumber);
      updateHint(hints.special, hasSpecial);

      // Calculate strength
      if (hasLength) strength++;
      if (hasUppercase) strength++;
      if (hasNumber) strength++;
      if (hasSpecial) strength++;

      // Update strength bar
      strengthBar.className = 'strength-bar';
      if (strength === 0) {
        strengthBar.style.width = '0%';
      } else if (strength === 1) {
        strengthBar.classList.add('strength-weak');
      } else if (strength === 2 || strength === 3) {
        strengthBar.classList.add('strength-medium');
      } else if (strength === 4) {
        strengthBar.classList.add('strength-strong');
      }

      // Check password match
      checkPasswordMatch();
    });

    function updateHint(element, condition) {
      const icon = element.querySelector('i');
      if (condition) {
        element.classList.add('requirement-met');
        element.classList.remove('requirement-not-met');
        icon.style.color = '#48bb78';
      } else {
        element.classList.remove('requirement-met');
        element.classList.add('requirement-not-met');
        icon.style.color = '#a0aec0';
      }
    }

    // Password match checker
    document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const matchIndicator = document.getElementById('passwordMatch');

      if (confirmPassword === '') {
        matchIndicator.textContent = '';
        matchIndicator.style.color = '';
        return;
      }

      if (password === confirmPassword) {
        matchIndicator.textContent = '✓ Passwords match';
        matchIndicator.style.color = '#48bb78';
      } else {
        matchIndicator.textContent = '✗ Passwords do not match';
        matchIndicator.style.color = '#f56565';
      }
    }

    // Form submission
    // Form submission
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      // Get form values
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const terms = document.getElementById('terms').checked;

      // Validate passwords match
      if (password !== confirmPassword) {
        Swal.fire('Error', 'Passwords do not match!', 'error');
        return;
      }

      // Validate terms agreement
      if (!terms) {
        Swal.fire('Error', 'You must agree to the terms and conditions!', 'error');
        return;
      }

      // Validate password strength
      const hasLength = password.length >= 8;
      const hasUppercase = /[A-Z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

      if (!hasLength || !hasUppercase || !hasNumber || !hasSpecial) {
        Swal.fire('Error', 'Please create a stronger password that meets all requirements!', 'error');
        return;
      }

      // Show loading state
      const registerButton = document.getElementById('registerButton');
      const originalText = registerButton.innerHTML;
      registerButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
      registerButton.disabled = true;

      // Start registration process
      try {
        await register({
          firstName,
          lastName,
          email,
          username,
          password,
          confirmPassword,
          acceptTerms: terms
        });
      } catch (error) {
        // Error is already shown by register function
        registerButton.innerHTML = originalText;
        registerButton.disabled = false;
      }
    });

    // Social registration buttons
    document.getElementById('googleRegister').addEventListener('click', () => {
      Swal.fire('Info', 'Google registration coming soon!', 'info');
    });

    document.getElementById('githubRegister').addEventListener('click', () => {
      Swal.fire('Info', 'GitHub registration coming soon!', 'info');
    });
  </script>
</body>

</html>