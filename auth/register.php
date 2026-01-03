<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
</head>
<body>

<h2>Create Account</h2>

<form onsubmit="register(event)">
  <input id="name" type="text" placeholder="Full Name" required><br>
  <input id="email" type="email" placeholder="Email" required><br>
  <input id="password" type="password" placeholder="Password" required><br>
  <button type="submit">Register</button>
</form>

<p><a href="login.php">Back to Login</a></p>

<?php include "auth.js"; ?>
</body>
</html>
