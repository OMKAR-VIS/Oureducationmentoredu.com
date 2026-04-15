<?php
session_start();

$dbHost = "127.0.0.1";
$dbUser = "root";
$dbPass = "";
$dbName = "oureducationmentor";

$conn = @new mysqli($dbHost, $dbUser, $dbPass);
if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed.");
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbName);

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    mobile VARCHAR(15) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$mobileColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'mobile'");
if ($mobileColumn && $mobileColumn->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN mobile VARCHAR(15) DEFAULT NULL AFTER email");
}

$conn->query("CREATE TABLE IF NOT EXISTS student_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    progress INT NOT NULL DEFAULT 0,
    videos_watched INT NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('video','pdf') NOT NULL,
    course VARCHAR(80) NOT NULL,
    title VARCHAR(180) NOT NULL,
    youtube_url VARCHAR(255) DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$adminEmail = "admin@oureducationmentor.com";
$adminPassword = "Admin@123";
$adminName = "Super Admin";

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $adminMobile = "9999999999";
    $insertAdmin = $conn->prepare("INSERT INTO users (name, email, mobile, password, role) VALUES (?, ?, ?, ?, 'admin')");
    $insertAdmin->bind_param("ssss", $adminName, $adminEmail, $adminMobile, $hash);
    $insertAdmin->execute();
}
$stmt->close();
