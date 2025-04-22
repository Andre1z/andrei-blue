<?php
// init_db.php
require_once 'config.php';

// Create users table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    name TEXT,
    email TEXT,
    password TEXT
)");

// Create charts table with the new 'importance' column
$db->exec("CREATE TABLE IF NOT EXISTS charts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    chart_name TEXT,
    chart_type TEXT,
    data_url TEXT,
    importance INTEGER DEFAULT 1,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

// Insert initial user if not exists
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
$stmt->execute([':username' => 'jocarsa']);
if ($stmt->fetchColumn() == 0) {
    $hashedPassword = password_hash('jocarsa', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, name, email, password) VALUES (:username, :name, :email, :password)");
    $stmt->execute([
        ':username' => 'jocarsa',
        ':name' => 'Jose Vicente Carratala',
        ':email' => 'info@josevicentecarratala.com',
        ':password' => $hashedPassword
    ]);
    echo "Initial user created.\n";
} else {
    echo "Initial user already exists.\n";
}

echo "Database initialized successfully.";
?>

