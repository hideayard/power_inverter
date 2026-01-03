<!DOCTYPE html>
<html>
<head>
  <title>App Loader</title>
  <script src="/assets/js/auth.js"></script>
</head>
<body>
<script>
(async () => {
  const token = localStorage.getItem("jwt");
  const user  = JSON.parse(localStorage.getItem("user"));

  if (!token || !user) {
    location.replace("/auth/login.php");
    return;
  }

  if (isExpired(token)) {
    const ok = await refreshToken();
    if (!ok) {
      location.replace("/auth/login.php");
      return;
    }
  }

  // Role-based routing
  if (user.user_tipe === "ADMIN") {
    location.replace("/dashboard-inverter.php");
  } else {
    location.replace("/dashboard-inverter.php"); // future user dashboard
  }
})();
</script>
</body>
</html>
