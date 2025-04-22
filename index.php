<?php
// index.php
require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login or Register</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <h1>Welcome to the Charts Application</h1>
  <p>
    <a href="login.php">Login</a> | 
    <a href="register.php">Register</a>
  </p>
</body>
</html>

