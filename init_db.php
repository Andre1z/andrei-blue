<?php
// init_db.php
require_once 'config.php';

$db = new PDO('sqlite:chart.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Importante: activar soporte para claves foráneas en SQLite
$db->exec("PRAGMA foreign_keys = ON");

// Crear tabla de usuarios
$db->exec("CREATE TABLE IF NOT EXISTS users (
    identificador INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    name TEXT,
    email TEXT,
    password TEXT
)");

// Crear tabla de gráficos con FK hacia users(identificador)
$db->exec("CREATE TABLE IF NOT EXISTS charts (
    identificador INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    chart_name TEXT,
    chart_type TEXT,
    data_url TEXT,
    importance INTEGER DEFAULT 1,
    FOREIGN KEY(user_id) REFERENCES users(identificador) ON DELETE CASCADE
)");

// Insertar usuario inicial si no existe
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
$stmt->execute([':username' => 'jocarsa']);
if ($stmt->fetchColumn() == 0) {
    $hashedPassword = password_hash('jocarsa', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, name, email, password) VALUES (:username, :name, :email, :password)");
    $stmt->execute([
        ':username' => 'andre1z',
        ':name' => 'Andrei Buga',
        ':email' => 'bugaandrei1@gmail.com',
        ':password' => $hashedPassword
    ]);
    echo "Initial user created.\n";
} else {
    echo "Initial user already exists.\n";
}

echo "Database initialized successfully.";
?>