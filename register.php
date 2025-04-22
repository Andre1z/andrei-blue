<?php
// register.php
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($username && $name && $email && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if username exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username already taken.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, name, email, password) VALUES (:username, :name, :email, :password)");
                $stmt->execute([
                    ':username' => $username,
                    ':name' => $name,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);
                header("Location: login.php");
                exit;
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Charts Application</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="form-container">
    <h1 class="form-title">Register</h1>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" placeholder="Choose a username" required>
      </div>
      <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" placeholder="Your full name" required>
      </div>
      <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter a password" required>
      </div>
      <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" placeholder="Confirm your password" required>
      </div>
      <button type="submit" class="btn">Register</button>
    </form>
    <p class="switch">Already have an account? <a href="login.php">Login here</a>.</p>
  </div>
</body>
</html>

