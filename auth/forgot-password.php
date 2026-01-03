<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
</head>
<body>

<h2>Forgot Password</h2>

<form onsubmit="forgotPassword(event)">
  <input id="email" type="email" placeholder="Your Email" required><br>
  <button type="submit">Send Reset Link</button>
</form>

<p><a href="login.php">Back to Login</a></p>

<?php include "auth.js"; ?>
</body>
</html>
